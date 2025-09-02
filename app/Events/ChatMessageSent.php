<?php

namespace App\Events;

use App\Models\Chat;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class ChatMessageSent implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public $chat;

    public function __construct(Chat $chat)
    {
        $this->chat = $chat;
        Log::info('event' . $chat);
    }

    // public function broadcastOn()
    // {
    //     return new PrivateChannel('chat-conversation.' . $this->chat->receiver_id);
    // }

    public function broadcastOn()
    {
        // Log::info('enter broadcats');
        return new PrivateChannel('chat-conversation.' . $this->chat->conversation_id);
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->chat->message,
            'sender_id' => $this->chat->sender_id,
            'receiver_id' => $this->chat->receiver_id,
            'created_at' => $this->chat->created_at->toDateTimeString(),
        ];
    }

    // public function broadcastAs()
    // {
    //     return 'ChatMessageSent';
    // }
}
