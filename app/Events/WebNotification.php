<?php

namespace App\Events;

use App\Models\Company;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $company;

    protected $type;

    public $message;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Company $company, $message, $type)
    {
        //init
        $this->company = $company;
        $this->message = $message;
        $this->type = $type;

    }

    //get company
    public function getCompany()
    {
        return $this->company;
    }

    //get type
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn(): array
    {
        return new Channel('user.'.$this->company->user->id);
    }

    public function broadcastAs()
    {
        return 'general';
    }
}
