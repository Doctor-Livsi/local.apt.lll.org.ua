<?php

namespace App\Models\Apteks\WS;

use Illuminate\Database\Eloquent\Model;

class ChatBotStatsCount extends Model
{
    /**
     * Підключення до MSSQL
     */
    protected $connection = 'sqlsrv';

    /**
     * Таблиця в MSSQL
     */
    protected $table = 'chatbot_stats_counts';

    /**
     * Timestamps Laravel не використовуємо
     */
    public $timestamps = false;

    protected $fillable = [
        'queue',
        'in_work',
        'total',
        'employees',
        'created_at',
        'deleted_at',
    ];

    /**
     * Останній актуальний запис (deleted_at IS NULL)
     */
    public static function latestActive(): ?self
    {
        return self::query()
            ->whereNull('deleted_at')
            ->orderByDesc('id')
            ->first();
    }
}
