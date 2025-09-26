<?php

namespace App\Modules\Common\Services;

use App\Modules\Common\Events\TestEvent;

class TestEventService
{
    public function broadcastEvent(int $eventId, string $channel, string $type, array $data = []): void
    {
        broadcast(new TestEvent($eventId, $channel, $type, $data));
    }
}
