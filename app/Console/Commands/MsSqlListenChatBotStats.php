<?php

namespace App\Console\Commands;

use App\Events\ApteksChatBotStatsUpdated;
use App\Models\Apteks\WS\ChatBotStatsCount;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class MsSqlListenChatBotStats extends Command
{
    /**
     * Назва artisan-команди
     */
    protected $signature = 'mssql:listen-chatbot-stats';

    /**
     * Опис команди
     */
    protected $description = 'Слухає MSSQL Service Broker чергу та пушить статистику ChatBot у WebSocket (Reverb)';

    public function handle(): int
    {
        $queue = 'dbo.ChatBotStatsQueue';

        $this->info("MSSQL listener запущено: {$queue}");
        Log::info('MSSQL listener старт', ['queue' => $queue]);

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
                        FROM {$queue}
                    ), TIMEOUT 60000;
                ");

                if (empty($msg)) {
                    continue; // таймаут
                }

                /**
                 * Схлопуємо чергу, щоб після простою не штовхати багато WS-подій
                 */
                DB::connection('sqlsrv')->statement("
                    WHILE (1=1)
                    BEGIN
                        IF NOT EXISTS (SELECT 1 FROM {$queue}) BREAK;
                        WAITFOR (
                            RECEIVE TOP(50) message_body
                            FROM {$queue}
                        ), TIMEOUT 0;
                    END
                ");

                $lastIdRaw = trim((string)($msg[0]->message_body ?? ''));
                $lastId = ctype_digit($lastIdRaw) ? (int)$lastIdRaw : null;

                Log::debug('MSSQL черга ChatBot: отримано повідомлення', [
                    'queue' => $queue,
                    'message_body' => $lastIdRaw,
                    'parsed_id' => $lastId,
                ]);

                /**
                 * ЧИТАННЯ ДАНИХ ЧЕРЕЗ МОДЕЛЬ:
                 * - якщо прийшов id — читаємо активний запис по ньому
                 * - інакше — читаємо останній активний запис
                 */
                $row = null;

                if ($lastId) {
                    $row = ChatBotStatsCount::query()
                        ->whereNull('deleted_at')
                        ->where('id', $lastId)
                        ->first();
                }

                if (!$row) {
                    $row = ChatBotStatsCount::latestActive();
                }

                if (!$row) {
                    $this->warn('Дані не знайдено (таблиця порожня або deleted_at не NULL)');
                    Log::warning('MSSQL listener ChatBot: дані не знайдено', ['id' => $lastId]);
                    continue;
                }

                /**
                 * Уніфікуємо payload під фронт:
                 * in_work -> inWork
                 * created_at -> updated_at
                 */
                $payload = [
                    'queue' => (int)$row->queue,
                    'inWork' => (int)$row->in_work,
                    'total' => (int)$row->total,
                    'employees' => (int)$row->employees,
                    'updated_at' => (string)$row->created_at,
                ];

                /**
                 * Публікуємо оновлення в WebSocket канал chatbot.stats (Reverb)
                 * Подія віддає формат { payload: {...} }
                 */
                broadcast(new ApteksChatBotStatsUpdated($payload));

                $this->line('WS: chatbot.stats updated (id=' . ($row->id ?? 'n/a') . ')');
                Log::info('WS broadcast: chatbot.stats updated', ['id' => $row->id ?? null]);
            } catch (Throwable $e) {
                $this->error('Помилка слухача MSSQL (ChatBot): ' . $e->getMessage());
                Log::error('MSSQL listener ChatBot error', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                sleep(2);
            }
        }
    }
}
