<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ApteksDataController extends Controller
{
    /**
     * DataTables server-side: отримання даних за статусом
     * URL: /api/apteks/status/{status}  (GET|POST)
     */
    public function getDataStatus(Request $request, $status)
    {
        $viewMap = [
            'working'   => 'apteks_working',
            'projected' => 'apteks_projected',
            'closed'    => 'apteks_closed',
            'connected' => 'apteks_connected',
        ];

        if (!array_key_exists($status, $viewMap)) {
            return response()->json(['error' => 'Invalid status'], 400);
        }

        $table = $viewMap[$status];

        $draw        = intval($request->input('draw'));
        $start       = intval($request->input('start'));
        $length      = intval($request->input('length'));
        $searchValue = $request->input('search.value');

        $query = DB::table($table);

        // --- NEW: підтримка вкладених фільтрів з Vue (d.filters = {...}) ---
        $filters = (array) $request->input('filters', []);

        // legacy (старий фронт): region/town
        // new (новий фронт): filters.region_id / filters.city_id / filters.company_id / filters.brand_id
        $region = $filters['region_id'] ?? $request->input('region');
        if (!empty($region)) {
            $query->where('address_region', $region);
        }

        $town = $filters['city_id'] ?? $request->input('town');
        if (!empty($town)) {
            $query->where('address_town', $town);
        }

        $company = $filters['company_id'] ?? null; // firma (строка)
        if (!empty($company)) {
            $query->where('firma', $company);
        }

        $brandId = $filters['brand_id'] ?? null; // brand_id (число/строка)
        if (!empty($brandId)) {
            $query->where('brand_id', $brandId);
        }

        $searchValue = trim((string) data_get($request->all(), 'search.value', ''));

        if ($searchValue !== '') {
            // Розбиваємо на токени за пробілами (можна ускладнити regex-ом, але цього вистачає)
            $tokens = preg_split('/\s+/', $searchValue, -1, PREG_SPLIT_NO_EMPTY);

            $query->where(function ($q) use ($tokens) {

                foreach ($tokens as $token) {
                    $t = trim($token);

                    if ($t === '') {
                        continue;
                    }

                    // 1) Якщо токен виглядає як "#123" — точний пошук по id (і цей токен має "знайтись")
                    if (str_starts_with($t, '#')) {
                        $potentialId = trim(substr($t, 1));

                        if ($potentialId !== '' && ctype_digit($potentialId)) {
                            $q->where('id', '=', (int) $potentialId);
                            continue; // переходимо до наступного токена
                        }

                        // Якщо після # не число — трактуємо як звичайний текст
                        $t = ltrim($t, '#');
                        if ($t === '') {
                            continue;
                        }
                    }

                    // 2) Звичайний токен: він має знайтись ХОЧА Б В ОДНОМУ полі
                    $like = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $t) . '%';

                    $q->where(function ($qq) use ($like, $t) {
                        $qq->where('name', 'like', $like)
                            ->orWhere('name_full', 'like', $like)       // якщо є поле name_full — залишаємо
                            ->orWhere('apteka_ip', 'like', $like)
                            ->orWhere('phone', 'like', $like)
                            ->orWhere('address_full', 'like', $like);

                        // 3) Якщо токен — число, додатково точний пошук по id
                        if (ctype_digit($t)) {
                            $qq->orWhere('id', '=', (int) $t);
                        }
                    });
                }
            });
        }

        // IMPORTANT: recordsFiltered має рахуватись ПІСЛЯ застосування фільтрів та search
        $totalFiltered = $query->count();

        // Order
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderColumnName  = $request->input("columns.$orderColumnIndex.data", 'id');
        $orderDir         = $request->input('order.0.dir', 'asc');

        if ($orderColumnName === 'name') {
            $query->orderBy('sort_prefix', $orderDir)
                ->orderBy('sort_number', $orderDir)
                ->orderBy('name', $orderDir);
        } else {
            // якщо DataTables прислав неіснуючу колонку — щоб не впасти 500
            // (опційно можна обмежити allowlist'ом)
            $query->orderBy($orderColumnName, $orderDir);
        }

        // Data
        $data = $query
            ->offset($start)
            ->limit($length)
            ->get();

        // Total без фільтрів
        $totalData = DB::table($table)->count();

        return response()->json([
            'draw'            => $draw,
            'recordsTotal'    => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data'            => $data,
        ]);
    }

    /**
     * LEGACY: старі довідники (старий фронт)
     * URL: /api/apteks/regions/{status}
     */
    public function getRegions(Request $request, $status)
    {
        $table = $this->tableByStatus($status);

        $regions = DB::table($table)
            ->select('address_region')
            ->whereNotNull('address_region')
            ->where('address_region', '!=', '')
            ->distinct()
            ->orderBy('address_region')
            ->pluck('address_region');

        return response()->json($regions);
    }

    /**
     * NEW: довідник для Vue-фільтрів
     * URL: /api/apteks/filters/regions?status=working
     * Response: [{id,name}]
     */
    public function regions(Request $request)
    {
        $status = $request->query('status', 'working');
        $table  = $this->tableByStatus($status);

        $rows = DB::table($table)
            ->select('address_region')
            ->whereNotNull('address_region')
            ->where('address_region', '!=', '')
            ->distinct()
            ->orderBy('address_region')
            ->pluck('address_region');

        $out = collect($rows)
            ->map(fn($v) => ['id' => (string) $v, 'name' => (string) $v])
            ->values();

        return response()->json($out);
    }

    /**
     * NEW: довідник для Vue-фільтрів
     * URL: /api/apteks/filters/cities?status=working&region_id=...
     * Response: [{id,name}]
     */
    public function cities(Request $request)
    {
        $status = $request->query('status', 'working');
        $table  = $this->tableByStatus($status);

        $region = $request->query('region_id');
        if (!$region) {
            return response()->json([], 422);
        }

        $rows = DB::table($table)
            ->where('address_region', $region)
            ->select('address_town')
            ->whereNotNull('address_town')
            ->where('address_town', '!=', '')
            ->distinct()
            ->orderBy('address_town')
            ->pluck('address_town');

        $out = collect($rows)
            ->map(fn($v) => ['id' => (string) $v, 'name' => (string) $v])
            ->values();

        return response()->json($out);
    }

    /**
     * NEW: довідник для Vue-фільтрів
     * URL: /api/apteks/filters/companies?status=working
     * Response: [{id,name}]
     *
     * Примітка: у твоїй таблиці поле називається firma (строка)
     */
    public function companies(Request $request)
    {
        $status = $request->query('status', 'working');
        $table  = $this->tableByStatus($status);

        $rows = DB::table($table)
            ->select('firma')
            ->whereNotNull('firma')
            ->where('firma', '!=', '')
            ->distinct()
            ->orderBy('firma')
            ->pluck('firma');

        $out = collect($rows)
            ->map(fn($v) => ['id' => (string) $v, 'name' => (string) $v])
            ->values();

        return response()->json($out);
    }

    /**
     * NEW: довідник для Vue-фільтрів
     * URL: /api/apteks/filters/brands?status=working
     * Response: [{id,name}]
     *
     * Примітка: у твоїй таблиці є brand_id + brand
     */
    public function brands(Request $request)
    {
        $status = $request->query('status', 'working');
        $table  = $this->tableByStatus($status);

        $rows = DB::table($table)
            ->select(['brand_id', 'brand'])
            ->whereNotNull('brand_id')
            ->whereNotNull('brand')
            ->distinct()
            ->orderBy('brand')
            ->get();

        $out = $rows->map(fn($r) => [
            'id'   => (string) $r->brand_id,
            'name' => (string) $r->brand,
        ])->values();

        return response()->json($out);
    }

    /**
     * Експорт: поки заглушка (щоб маршрут не падав 500).
     * URL: /api/apteks/status/{status}/export/{format}
     */
    public function export(Request $request, $status, $format)
    {
        // На v1 можна або прибрати маршрут, або зробити експорт окремо.
        return response()->json([
            'error'  => 'Export not implemented yet',
            'status' => $status,
            'format' => $format,
        ], 501);
    }

    /**
     * Helper: мапа статусу -> таблиця/вьюха
     */
    private function tableByStatus(string $status): string
    {
        $viewMap = [
            'working'   => 'apteks_working',
            'projected' => 'apteks_projected',
            'closed'    => 'apteks_closed',
            'connected' => 'apteks_connected',
        ];

        if (!array_key_exists($status, $viewMap)) {
            abort(400, 'Invalid status');
        }

        return $viewMap[$status];
    }
}
