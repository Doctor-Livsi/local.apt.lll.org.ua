<?php

namespace App\Console\Commands;

use App\Events\ChatBotStatsUpdated;
use Illuminate\Console\Command;

class WsPushChatBotStats extends Command
{
    protected $signature = 'ws:push-chatbot-stats {queue?} {inWork?} {total?} {employees?}';
    protected $description = 'Надіслати тестову статистику ChatBot через Reverb (dev)';

    public function handle(): int
    {
        $queue = (int)($this->argument('queue') ?? random_int(0, 50));
        $inWork = (int)($this->argument('inWork') ?? random_int(0, 10));
        $total = (int)($this->argument('total') ?? ($queue + $inWork + random_int(0, 20)));
        $employees = (int)($this->argument('employees') ?? random_int(1, 20));

        $payload = [
            'queue' => $queue,
            'inWork' => $inWork,
            'total' => $total,
            'employees' => $employees,
            'updated_at' => now()->toDateTimeString(),
        ];

        broadcast(new ChatBotStatsUpdated($payload));

        $this->info('Надіслано: ' . json_encode($payload, JSON_UNESCAPED_UNICODE));
        return self::SUCCESS;
    }
}
