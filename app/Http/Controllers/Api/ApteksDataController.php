<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ApteksDataController extends Controller
{
    public function getData(Request $request, $status)
    {
        $viewMap = [
            'working' => 'apteks_working',
            'projected' => 'apteks_projected',
            'closed' => 'apteks_closed',
            'connected' => 'apteks_connected'
        ];

        if (!array_key_exists($status, $viewMap)) {
            return response()->json(['error' => 'Invalid status'], 400);
        }

        $table = $viewMap[$status];
        $draw = intval($request->input('draw'));
        $start = intval($request->input('start'));
        $length = intval($request->input('length'));
        $searchValue = $request->input('search.value');

        $query = DB::table($table);

        $region = $request->input('region');
        if (!empty($region)) {
            $query->where('address_region', $region);
        }

        $town = $request->input('town');
        if (!empty($town)) {
            $query->where('address_town', $town);
        }

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                    ->orWhere('apteka_ip', 'like', "%{$searchValue}%")
                    ->orWhere('phone', 'like', "%{$searchValue}%")
                    ->orWhere('address_full', 'like', "%{$searchValue}%");
            });
        }

        $totalFiltered = $query->count();

        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderColumnName = $request->input("columns.$orderColumnIndex.data", 'id');
        $orderDir = $request->input('order.0.dir', 'asc');

        if ($orderColumnName === 'name') {
            $query->orderBy('sort_prefix', $orderDir)
                ->orderBy('sort_number', $orderDir)
                ->orderBy('name', $orderDir);
        } else {
            $query->orderBy($orderColumnName, $orderDir);
        }

        $data = $query
            ->offset($start)
            ->limit($length)
//            ->orderBy($orderColumnName, $orderDir)
            ->get();

        $totalData = DB::table($table)->count();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ]);
    }

    public function getRegions(Request $request, $status)
    {
        $viewMap = [
            'working' => 'apteks_working',
            'projected' => 'apteks_projected',
            'closed' => 'apteks_closed',
            'connected' => 'apteks_connected'
        ];

        if (!array_key_exists($status, $viewMap)) {
            return response()->json(['error' => 'Invalid status'], 400);
        }

        $table = $viewMap[$status];

        $regions = DB::table($table)
            ->select('address_region')
            ->distinct()
            ->orderBy('address_region')
            ->pluck('address_region');

        return response()->json($regions);
    }

    public function getTowns(Request $request, $status)
    {
        $viewMap = [
            'working' => 'apteks_working',
            'projected' => 'apteks_projected',
            'closed' => 'apteks_closed',
            'connected' => 'apteks_connected'
        ];

        if (!array_key_exists($status, $viewMap)) {
            return response()->json(['error' => 'Invalid status'], 400);
        }

        $table = $viewMap[$status];
        $region = $request->input('region');

        if (!$region) {
            return response()->json([], 400);
        }

        $towns = DB::table($table)
            ->where('address_region', $region)
            ->select('address_town')
            ->distinct()
            ->orderBy('address_town')
            ->pluck('address_town');

        return response()->json($towns);
    }
}
