<?php

namespace App\Console\Commands;

use App\Events\ApteksEmployeesWorkingUpdated;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class MsSqlListenEmployeesWorking extends Command
{
    protected $signature = 'mssql:listen-employees-working {--queue=dbo.ApteksEmployeesWorkingQueue} {--batch=50} {--timeout=60000}';
    protected $description = 'Слухає MSSQL Service Broker чергу та пушить оновлення on_working співробітників у WebSocket (Reverb)';

    public function handle(): int
    {
        $queue   = (string)$this->option('queue');
        $batch   = max(1, (int)$this->option('batch'));
        $timeout = max(1000, (int)$this->option('timeout')); // ms

        $this->info("MSSQL listener запущено: {$queue}");
        Log::info('MSSQL listener старт (employees working)', [
            'queue' => $queue,
            'batch' => $batch,
            'timeout_ms' => $timeout,
        ]);

        while (true) {
            try {
                // 1) Якщо черги ще немає — просто чекаємо, не падаємо
                $exists = DB::connection('sqlsrv')->selectOne("
                    SELECT OBJECT_ID(N'{$queue}', N'SQ') AS queue_object_id
                ");

                if (empty($exists) || empty($exists->queue_object_id)) {
                    $this->line("Черга ще не створена: {$queue} (очікуємо...)");
                    sleep(5);
                    continue;
                }

                // 2) Чекаємо повідомлення пачкою і обробляємо КОЖНЕ.
                //    ВАЖЛИВО: НІЧОГО НЕ "схлопуємо", інакше губляться повідомлення.
                $rows = DB::connection('sqlsrv')->select("
                    WAITFOR (
                        RECEIVE TOP({$batch})
                            CAST(message_body AS nvarchar(4000)) AS message_body
                        FROM {$queue}
                    ), TIMEOUT {$timeout};
                ");

                if (empty($rows)) {
                    continue; // таймаут
                }

                foreach ($rows as $row) {
                    $body = trim((string)($row->message_body ?? ''));

                    /**
                     * Очікуємо JSON від тригера:
                     * {"apteka_id":3068,"code":185592,"on_working":1}
                     */
                    $data = json_decode($body, true);

                    if (!is_array($data)) {
                        Log::warning('Employees queue: invalid json message_body', [
                            'queue' => $queue,
                            'message_body' => $body,
                        ]);
                        continue;
                    }

                    // Нормалізація
                    $payload = [
                        'apteka_id'  => (int)($data['apteka_id'] ?? 0),
                        'code'       => (int)($data['code'] ?? 0),
                        'on_working' => (bool)($data['on_working'] ?? false),
                        'updated_at' => now()->toDateTimeString(),
                    ];

                    if ($payload['apteka_id'] <= 0 || $payload['code'] <= 0) {
                        Log::warning('Employees queue: invalid payload', [
                            'queue' => $queue,
                            'payload' => $payload,
                            'raw' => $data,
                        ]);
                        continue;
                    }

                    broadcast(new ApteksEmployeesWorkingUpdated($payload));
                    $this->line('WS: listen-employees-working (apteka_id=' . ($payload['apteka_id'] ?? 'n/a') . '; employees-code=' . ($payload['code'] ?? 'n/a') . '; on_working=' . ($payload['on_working'] ?? 'n/a') .')');
                    Log::info('WS broadcast employees working', $payload);
                }
            } catch (Throwable $e) {
                // Важливо: не падаємо, якщо MSSQL "ляг" або звʼязок відвалився
                Log::error('MSSQL listener employees error', [
                    'message' => $e->getMessage(),
                ]);

                $this->error('Помилка слухача MSSQL (employees): ' . $e->getMessage());
                sleep(3);
            }
        }
    }
}
