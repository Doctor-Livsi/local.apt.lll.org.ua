<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ApteksConnectionsCounterUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(public array $payload) {}

    public function broadcastOn(): Channel
    {
        // Для dev: публичный канал без авторизации
        return new Channel('apteks.counter');
    }
    public function broadcastWith(): array
    {
        return [
            'payload' => $this->payload,
        ];
    }

    public function broadcastAs(): string
    {
        return 'updated';
    }
}
