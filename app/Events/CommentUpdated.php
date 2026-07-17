<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $slug,
        public string $action,
        public array $comment
    ) {}

    public function broadcastOn(): array
    {
        return [new Channel('webinar.' . $this->slug . '.comments')];
    }

    public function broadcastAs(): string
    {
        return 'comment.updated';
    }
}
