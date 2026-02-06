<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatBotStatsUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(public array $payload) {}

    public function broadcastOn(): Channel
    {
        return new Channel('chatbot.stats');
    }

    public function broadcastAs(): string
    {
        return 'updated';
    }

    public function broadcastWith(): array
    {
        return ['payload' => $this->payload];
    }
}
