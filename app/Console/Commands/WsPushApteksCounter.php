<?php

namespace App\Console\Commands;

use App\Events\ApteksConnectionsCounterUpdated;
use Illuminate\Console\Command;

class WsPushApteksCounter extends Command
{
    protected $signature = 'ws:push-apteks-counter {disconnected_total?}';
    protected $description = 'Broadcast test apteks counter payload via Reverb';

    public function handle(): int
    {
        $disc = (int)($this->argument('disconnected_total') ?? random_int(0, 30));

        $payload = [
            'id' => random_int(1, 999999),
            'total' => 1,
            'total_apteks' => 100,
            'connected_apteks' => max(0, 100 - $disc),
            'disconnected_total' => $disc,
            'disconnected_min' => random_int(0, 3),
            'disconnected_lvl1' => random_int(0, 5),
            'disconnected_lvl2' => random_int(0, 5),
            'disconnected_lvl3' => random_int(0, 5),
            'disconnected_max' => random_int(0, 2),
            'created_at' => now()->toDateTimeString(),
        ];

        broadcast(new ApteksConnectionsCounterUpdated($payload));

        $this->info('Broadcast sent: disconnected_total=' . $disc);
        return self::SUCCESS;
    }
}
