<?php

namespace Modules\Wpbox\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;



class ContactReplies implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    

    public $user;
    public $message;
    public $contact;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user,$message,$contact)
    {
        $this->user = $user;
        $this->message = $message;
        $this->contact = [
            'id'=>$contact->id
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel("chat.".$this->message->contact_id);
    }

    public function broadcastAs()
    {
        return 'general';
    }
}
