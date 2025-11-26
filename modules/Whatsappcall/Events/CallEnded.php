<?php

namespace Modules\Whatsappcall\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class CallEnded implements ShouldBroadcast
{
    use SerializesModels;

    /** @var int */
    public $companyId;

    /** @var array */
    public $payload;

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
        return 'ended';
    }

    public function broadcastWith(): array
    {
        return $this->payload;
    }
}

