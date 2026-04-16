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
    use Dispatchable, SerializesModels;

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
}
