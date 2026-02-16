<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Apteks\WS\ApteksConnectionsCount;
use Illuminate\Http\JsonResponse;

class ApteksCounterController extends Controller
{
    public function show(): JsonResponse
    {
        $row = ApteksConnectionsCount::latestActive();

        // ВАЖНО: если записи нет — даём нули,
        // но если запись есть — отдаём из базы.
        if (!$row) {
            return response()->json([
                'disconnected_min'  => 0,
                'disconnected_lvl1' => 0,
                'disconnected_lvl2' => 0,
                'disconnected_lvl3' => 0,
                'disconnected_max'  => 0,
                'total_apteks'      => 0,
                'connected_apteks'  => 0,
                'created_at'        => null,
            ]);
        }

        return response()->json([
            'disconnected_min'  => (int) $row->disconnected_min,
            'disconnected_lvl1' => (int) $row->disconnected_lvl1,
            'disconnected_lvl2' => (int) $row->disconnected_lvl2,
            'disconnected_lvl3' => (int) $row->disconnected_lvl3,
            'disconnected_max'  => (int) $row->disconnected_max,
            'total_apteks'      => (int) $row->total_apteks,
            'connected_apteks'  => (int) $row->connected_apteks,
            'created_at'        => $row->created_at?->toDateTimeString(),
        ]);
    }
}
