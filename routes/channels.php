<?php

use App\Models\Chat;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat-conversation.{conversationId}', function ($user, $conversationId) {
    Log::info("Checking auth for conversation: {$conversationId} by user: {$user->id}");

    // Check if the user is either sender or receiver in this conversation
    return Chat::where('conversation_id', $conversationId)
        ->where(function ($query) use ($user) {
            $query->where('sender_id', $user->id)
                ->orWhere('receiver_id', $user->id);
        })
        ->exists();
});
