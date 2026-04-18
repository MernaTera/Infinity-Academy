<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WaitingListUpdated implements ShouldBroadcast
{
    public $waiting;

    public function __construct($waiting)
    {
        $this->waiting = $waiting;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('waiting-list'),
        ];
    }

    public function broadcastAs()
    {
        return 'WaitingListUpdated';
    }
}
