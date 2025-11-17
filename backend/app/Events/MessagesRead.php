<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessagesRead implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $matchId;
    public int $userId;

    public function __construct(int $matchId, int $userId)
    {
        $this->matchId = $matchId;
        $this->userId = $userId;
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('match.' . $this->matchId)];
    }

    public function broadcastAs(): string
    {
        return 'MessagesRead';
    }
}