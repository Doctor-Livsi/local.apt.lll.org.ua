<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Apteks\WS\ChatBotStatsCount;
use Illuminate\Http\JsonResponse;

class ChatBotStatsController extends Controller
{
    /**
     * Повертає останню актуальну статистику ChatBot (для initial-load віджета)
     */
    public function show(): JsonResponse
    {
        $row = ChatBotStatsCount::latestActive();

        if (!$row) {
            return response()->json([
                'queue' => 0,
                'inWork' => 0,
                'total' => 0,
                'employees' => 0,
                'updated_at' => null,
            ]);
        }

        return response()->json([
            'queue' => (int)$row->queue,
            'inWork' => (int)$row->in_work,
            'total' => (int)$row->total,
            'employees' => (int)$row->employees,
            'updated_at' => (string)$row->created_at,
        ]);
    }
}
