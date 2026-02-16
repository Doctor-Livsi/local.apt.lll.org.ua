<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Apteks\Apteks;
use App\Models\Apteks\ApteksSchedule;
use App\Models\Apteks\ApteksWeekdayLocale;
use Carbon\Carbon;

class ApteksCardController extends Controller
{
    /**
     * Паспорт аптеки (ліва карточка)
     */
    public function passport(int $id)
    {
        $apteka = Apteks::query()
            ->select([
                'id',
                'name',
                'license',
                'firma',
                'brand',
                'phone',
                'closed',
                'plan_open_at',
                'open_at',
                'plan_closed_at',
                'closed_at',
                'address_full',
                'address_region',
                'address_town',
                'address_type',
                'address_street',
                'address_house_number',
                'description',
                'apteka_ip',
                'router_ip',
                'google_x',
                'google_y',
            ])
            ->findOrFail($id);

        return response()->json([
            'data' => $apteka,
        ]);
    }

    /**
     * Графік роботи аптеки за поточний ISO-тиждень
     * URL: /api/apteks/{id}/schedule/week/current
     */
    public function currentWeek(int $id)
    {
        $aptekaId = $id;

        // Поточна дата / час (ISO-логіка)
        $now = Carbon::now('Europe/Zaporozhye');

        $isoYear = (int) $now->isoWeekYear();
        $isoWeek = (int) $now->isoWeek();
        $todayIsoWeekday = (int) $now->dayOfWeekIso; // 1..7 (Пн..Нд)

        // Графік за поточний тиждень
        $schedules = ApteksSchedule::query()
            ->where('apteka_id', $aptekaId)
            ->where('year', $isoYear)
            ->where('week_number', $isoWeek)
            ->orderBy('week_num_day')
            ->get()
            ->keyBy('week_num_day');

        // Дні тижня (локалізація)
        $weekdays = ApteksWeekdayLocale::query()
            ->orderBy('week_num_day')
            ->get();

        // Формування відповіді (7 днів завжди)
        $rows = $weekdays->map(function ($day) use ($schedules, $todayIsoWeekday) {
            $row = $schedules->get((int) $day->week_num_day);

            $time = '';
            $date = null;

            if ($row) {
                // date_at може бути string → без format(), щоб не впасти
                $date = $row->date_at ? (string) $row->date_at : null;

                // MSSQL time: "08:00:00.0000000" → "08:00"
                $start = $row->start_at ? substr((string) $row->start_at, 0, 5) : null;
                $end   = $row->end_at   ? substr((string) $row->end_at,   0, 5) : null;

                if ($start && $end) {
                    $time = "{$start} - {$end}";
                } else {
                    // fallback
                    $time = (string) ($row->schedules ?? '');
                }
            }

            return [
                'week_num_day' => (int) $day->week_num_day,
                'day_name'     => (string) $day->ua_name,
                'date_at'      => $date,
                'time'         => $time,
                'is_today'     => ((int) $day->week_num_day === $todayIsoWeekday),
            ];
        });

        return response()->json([
            'apteka_id'    => $aptekaId,
            'iso_year'     => $isoYear,
            'iso_week'     => $isoWeek,
            'generated_at' => $now->toDateTimeString(),
            'rows'         => $rows,
        ]);
    }

    /**
     * Співробітники (заглушка)
     */
    public function staff(int $id)
    {
        return response()->json(['data' => []]);
    }

    /**
     * Провайдери (заглушка)
     */
    public function providers(int $id)
    {
        return response()->json(['data' => []]);
    }
}
