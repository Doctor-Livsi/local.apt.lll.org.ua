<?php

namespace App\Console\Commands;

use App\Events\ApteksEmployeesWorkingUpdated;
use Illuminate\Console\Command;

class WsPushEmployeesWorking extends Command
{
    protected $signature = 'ws:push-employees-working {apteka_id?} {code?} {on_working?}';
    protected $description = 'Надіслати тестовий апдейт on_working співробітника через Reverb (dev)';

    public function handle(): int
    {
//        $aptekaId   = (int)($this->argument('apteka_id') ?? 3068);
        $code = (int)($this->argument('code') ?? random_int(1, 999999));
        $onWorkingArg = $this->argument('on_working');

        $onWorking = is_null($onWorkingArg)
            ? (bool)random_int(0, 1)
            : (bool)((int)$onWorkingArg);

        $payload = [
            'apteka_id'   => $aptekaId,
            'code' => $code,
            'on_working'  => $onWorking,
            'updated_at'  => now()->toDateTimeString(),
        ];

        try {
            broadcast(new ApteksEmployeesWorkingUpdated($payload));

            $this->line(
                'WS: employee.working.updated ' .
                '(apteka_id=' . $payload['apteka_id'] .
                ', code=' . $payload['code'] .
                ', on_working=' . (int)$payload['on_working'] . ')'
            );

            Log::info('WS broadcast: employee.working.updated', [
                'apteka_id' => $payload['apteka_id'],
                'code' => $payload['code'],
                'on_working' => (int)$payload['on_working'],
            ]);
            $this->line('WS broadcast: employee.working.updated', [
                'apteka_id' => $payload['apteka_id'],
                'code' => $payload['code'],
                'on_working' => (int)$payload['on_working'],
            ]);
        } catch (Throwable $e) {
            $this->error('Помилка WS broadcast (EmployeesWorking): ' . $e->getMessage());

            Log::error('WS broadcast EmployeesWorking error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $payload,
            ]);

            // щоб не зʼїдати CPU якщо Reverb/WS впав
            usleep(200000); // 200ms
        }


        $this->info('Надіслано: ' . json_encode($payload, JSON_UNESCAPED_UNICODE));
        return self::SUCCESS;
    }
}
