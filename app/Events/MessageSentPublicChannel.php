<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSentPublicChannel implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notif_title;
    public $notif_detail;
    public $notif_created_at;

    public function __construct($notif_title, $notif_detail, $notif_created_at)
    {
        $this->notif_title = $notif_title;
        $this->notif_detail = $notif_detail;
        $this->notif_created_at = $notif_created_at;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('channel-public'),
        ];
    }

    public function broadcastWith()
    {
        return [
            'notif_title' => $this->notif_title,
            'notif_detail' => $this->notif_detail,
            'notif_created_at' => $this->notif_created_at
        ];
    }
}
