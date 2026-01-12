<?php

namespace App\Models\Apteks;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Apteks extends Model
{
    // Мы не используем стандартную таблицу
    protected $table = null;

    public static function getViewDataByStatus(string $status)
    {
        $views = [
            'working' => 'apteks_working',
            'projected' => 'apteks_projected',
            'connected' => 'apteks_connected',
            'closed' => 'apteks_closed',
        ];

        if (!array_key_exists($status, $views)) {
            abort(404, 'Invalid status');
        }

        return DB::table($views[$status])
            ->orderBy('sort_prefix')
            ->orderBy('sort_number')
            ->get();
    }
}
