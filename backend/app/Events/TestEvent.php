<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TestEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $eventId;
    public $channel;
    public $type;
    public $data;

    public function __construct(int $eventId, string $channel, string $type, array $data = [])
    {
        $this->eventId = $eventId;
        $this->channel = $channel;
        $this->type = $type;
        $this->data = $data;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel("{$this->channel}.{$this->eventId}")
        ];
    }

    public function broadcastAs(): string
    {
        return 'TestEvent';
    }
}
