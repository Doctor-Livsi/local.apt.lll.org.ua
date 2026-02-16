<?php

namespace App\Models\Apteks\WS;

use Illuminate\Database\Eloquent\Model;

class ApteksConnectionsCount extends Model
{
    /**
     * Підключення до MSSQL
     */
    protected $connection = 'sqlsrv';

    /**
     * Таблиця в MSSQL
     */
    protected $table = 'apteks_connections_count';

    /**
     * Timestamps Laravel не використовуємо
     */
    public $timestamps = false;

    protected $casts = [
        'disconnected_min'  => 'integer',
        'disconnected_lvl1' => 'integer',
        'disconnected_lvl2' => 'integer',
        'disconnected_lvl3' => 'integer',
        'disconnected_max'  => 'integer',
        'total_apteks'      => 'integer',
        'connected_apteks'  => 'integer',
        'created_at'        => 'datetime',
    ];

    public static function latestActive(): ?self
    {
        return self::query()
            // если есть deleted_at — раскомментируй
            // ->whereNull('deleted_at')
            ->orderByDesc('id')
            ->first();
    }
}
