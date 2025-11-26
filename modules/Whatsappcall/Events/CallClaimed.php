<?php

namespace Modules\Whatsappcall\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class CallClaimed implements ShouldBroadcast
{
    use SerializesModels;

    public int $companyId;
    public array $payload;

    public function __construct(int $companyId, array $payload)
    {
        $this->companyId = $companyId;
        $this->payload = $payload;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('whatsappcall.' . $this->companyId);
    }

    public function broadcastAs(): string
    {
        return 'claimed';
    }

    public function broadcastWith(): array
    {
        return $this->payload;
    }
}

