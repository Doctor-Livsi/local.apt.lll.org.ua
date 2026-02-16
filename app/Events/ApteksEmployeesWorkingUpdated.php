<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ApteksEmployeesWorkingUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $payload;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    public function broadcastOn(): Channel
    {
        $aptekaId = (int)($this->payload['apteka_id'] ?? 0);
        return new Channel("apteks.{$aptekaId}.employees");
    }

    public function broadcastAs(): string
    {
        return 'employee.working.updated';
    }

    public function broadcastWith(): array
    {
        // Отдаём ровно то, что нужно фронту
        return [
            'apteka_id'   => (int)($this->payload['apteka_id'] ?? 0),
            'code'        => (int)($this->payload['code'] ?? 0),
            'on_working'  => (bool)($this->payload['on_working'] ?? false),
            'updated_at'  => (string)($this->payload['updated_at'] ?? now()->toDateTimeString()),
        ];
    }
}
