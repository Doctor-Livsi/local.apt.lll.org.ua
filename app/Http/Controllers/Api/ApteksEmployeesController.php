<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Apteks\WS\ApteksEmployee;
use Illuminate\Http\Request;

class ApteksEmployeesController extends Controller
{
    public function show(Request $request, int $aptekaId)
    {
        $rows = ApteksEmployee::query()
            ->where('apteka_id', $aptekaId)
            ->whereNull('deleted_at')
            ->orderByRaw('ISNULL(sort, 777) ASC')
            ->orderBy('fio_ua')
            ->get([
                'id',
                'apteka_id',
                'fio_ua',
                'fio_ru',
                'code',
                'position',
                'phone',
                'sort',
                'on_working',
                'updated_at',
            ]);

        return response()->json([
            'data' => $rows,
        ]);
    }
}
