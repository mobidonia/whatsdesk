<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewClient
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;

    public $vendor;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user, $vendor)
    {
        $this->user = $user;
        $this->vendor = $vendor;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn(): array
    {
        return new PrivateChannel('channel-name');
    }
}
