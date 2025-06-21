<?php

namespace App\Console\Commands;

use App\Services\TestEventService;
use Illuminate\Console\Command;

class TestEventCommand extends Command
{
    protected $signature = 'event:test {event_id=1} {channel=game} {type=game}';
    protected $description = 'Send a test event';

    public function __construct(
        private TestEventService $eventService
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $eventId = $this->argument('event_id');
        $channel = $this->argument('channel');
        $type = $this->argument('type');

        $data = [
            'message' => 'Test event from artisan command',
            'timestamp' => now()->toIso8601String()
        ];

        $this->eventService->broadcastEvent($eventId, $channel, $type, $data);

        $this->info("Event sent to {$channel}.{$eventId} with type '{$type}'");
        $this->table(
            ['Event ID', 'Type', 'Data'],
            [[$eventId, $type, json_encode($data)]]
        );
    }
}