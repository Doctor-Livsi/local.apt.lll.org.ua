<?php

namespace App\Console\Commands;

use App\Events\ApteksConnectionsCounterUpdated;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class MsSqlListenApteksCounter extends Command
{
    /**
     * Назва artisan-команди
     */
    protected $signature = 'mssql:listen-apteks-counter';

    /**
     * Опис команди
     */
    protected $description = 'Слухає MSSQL Service Broker чергу та пушить оновлення лічильника аптек у WebSocket (Reverb)';

    public function handle(): int
    {
        $this->info('MSSQL listener запущено: dbo.ApteksConnectionsQueue');
        Log::info('MSSQL listener старт', ['queue' => 'dbo.ApteksConnectionsQueue']);

        while (true) {
            try {
                /**
                 * Очікуємо повідомлення з черги до 60 секунд.
                 * Це НЕ polling: процес спить, поки не прийде повідомлення.
                 */
                $msg = DB::connection('sqlsrv')->select("
                    WAITFOR (
                        RECEIVE TOP(1)
                            CAST(message_body AS nvarchar(4000)) AS message_body
                        FROM dbo.ApteksConnectionsQueue
                    ), TIMEOUT 60000;
                ");

                if (empty($msg)) {
                    // Таймаут — просто продовжуємо цикл
                    continue;
                }

                /**
                 * Схлопуємо чергу:
                 * якщо під час простою накопичилося багато повідомлень,
                 * ми не шлемо 100 подій у WS, а відправляємо ОДНЕ актуальне оновлення.
                 */
                DB::connection('sqlsrv')->statement("
                    WHILE (1=1)
                    BEGIN
                        IF NOT EXISTS (SELECT 1 FROM dbo.ApteksConnectionsQueue) BREAK;
                        WAITFOR (
                            RECEIVE TOP(50) message_body
                            FROM dbo.ApteksConnectionsQueue
                        ), TIMEOUT 0;
                    END
                ");

                $lastIdRaw = trim((string)($msg[0]->message_body ?? ''));
                $lastId = ctype_digit($lastIdRaw) ? (int)$lastIdRaw : null;

                Log::debug('MSSQL черга: отримано повідомлення', [
                    'message_body' => $lastIdRaw,
                    'parsed_id' => $lastId,
                ]);

                /**
                 * Беремо актуальні дані:
                 * - якщо прийшов id — читаємо запис по ньому
                 * - інакше — беремо останній запис
                 */
                if ($lastId) {
                    $data = DB::connection('sqlsrv')->selectOne("
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
                        WHERE deleted_at IS NULL AND id = ?
                    ", [$lastId]);
                } else {
                    $data = DB::connection('sqlsrv')->selectOne("
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

                if (!$data) {
                    $this->warn('Дані не знайдено (таблиця порожня або deleted_at не NULL)');
                    Log::warning('MSSQL listener: дані не знайдено', ['id' => $lastId]);
                    continue;
                }

                /**
                 * Публікуємо оновлення в WebSocket канал apteks.counter (Reverb)
                 * Подія віддає формат { payload: {...} }
                 */
                broadcast(new ApteksConnectionsCounterUpdated((array)$data));

                $this->line('WS: apteks.counter updated (id=' . ($data->id ?? 'n/a') . ')');
                Log::info('WS broadcast: apteks.counter updated', ['id' => $data->id ?? null]);
            } catch (Throwable $e) {
                $this->error('Помилка слухача MSSQL: ' . $e->getMessage());
                Log::error('MSSQL listener error', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                // Невелика пауза, щоб не крутитися при проблемах з конектом
                sleep(2);
            }
        }

        // сюди не дійдемо, але для типів повернення — ок
        // return self::SUCCESS;
    }
}
