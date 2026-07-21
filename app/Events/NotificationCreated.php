<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationCreated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public array $data;
    public int $employeeId;

    public function __construct(array $data, int $employeeId)
    {
        $this->data       = $data;
        $this->employeeId = $employeeId;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('employee.' . $this->employeeId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'NotificationCreated';
    }

    public function broadcastWith(): array
    {
        return $this->data;
    }
}