<?php

namespace App\Services;

use App\Events\TestEvent;

class TestEventService
{
    public function broadcastEvent(int $eventId, string $channel, string $type, array $data = []): void
    {
        broadcast(new TestEvent($eventId, $channel, $type, $data));
    }
}
