<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class ApteksConnectionsCountRepository
{
    /**
     * Отримати останній актуальний запис лічильника аптек (MSSQL).
     */
    public function latest(): ?object
    {
        return DB::connection('sqlsrv')->selectOne("
            SELECT TOP(1)
                id,
                total,
                total_apteks,
                connected_apteks,
                disconnected_total,
                disconnected_min,
                disconnected_lvl1,
                disconnected_lvl2,
                disconnected_lvl3,
                disconnected_max,
                created_at
            FROM dbo.apteks_connections_count
            WHERE deleted_at IS NULL
            ORDER BY id DESC
        ");
    }
}
