<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Traits\apiresponse;
use DB;
use Illuminate\Http\Request;
use App\Events\ChatMessageSent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class ChatController extends Controller
{
    use apiresponse;
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);

        $receiverId = $request->receiver_id;

        // Generate unique conversation ID (for 1-to-1 chat)
        $conversationId = implode('-', [
            min(Auth::id(), $receiverId),
            max(Auth::id(), $receiverId),
        ]);

        $chat = Chat::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $receiverId,
            'message' => $request->message,
            'conversation_id' => $conversationId,
        ]);

        // Broadcast the event
        broadcast(new ChatMessageSent($chat))->toOthers();

        return $this->success($chat, 'Message sent successfully', 200);
    }

    public function fetchMessages($id)
    {
        $receiverId = $id;
        $conversationId = implode('-', [min(Auth::user()->id, $receiverId), max(Auth::user()->id, $receiverId)]);

        $messages = Chat::where('conversation_id', $conversationId)
            ->orderBy('created_at', 'asc')
            ->get();

        return $this->success($messages, 'Data Fetch Success', 200);
    }

    public function chatContacts()
    {
        $userId = Auth::id();

        $latestConversations = Chat::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('conversation_id')
            ->map(function ($messages) use ($userId) {
                $lastMessage = $messages->last();
                $otherUserId = $lastMessage->sender_id === $userId ? $lastMessage->receiver_id : $lastMessage->sender_id;
                $user = \App\Models\User::find($otherUserId);

                return [
                    'user' => [
                        'id' => $user->id,
                        'avatar' => asset('uploads/avatars/' . $user->avatar),
                        'username' => $user->username,
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'last_message' => [
                        'id' => $lastMessage->id,
                        'sender_id' => $lastMessage->sender_id,
                        'receiver_id' => $lastMessage->receiver_id,
                        'message' => $lastMessage->message,
                        'conversation_id' => $lastMessage->conversation_id,
                        'created_at' => Carbon::parse($lastMessage->created_at)->diffForHumans(),
                    ]
                ];
            })
            ->values();

        return $this->success($latestConversations, 'Chat contacts fetched', 200);
    }

    public function topChatUsers()
    {
        $userId = Auth::id();

        $topUsers = Chat::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('conversation_id')
            ->map(function ($messages) use ($userId) {
                $lastMessage = $messages->last();
                $otherUserId = $lastMessage->sender_id === $userId ? $lastMessage->receiver_id : $lastMessage->sender_id;
                $user = \App\Models\User::find($otherUserId);

                return [
                    'user' => [
                        'id' => $user->id,
                        'avatar' => asset('uploads/avatars/' . $user->avatar),
                        'username' => $user->username,
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'last_message' => [
                        'id' => $lastMessage->id,
                        'sender_id' => $lastMessage->sender_id,
                        'receiver_id' => $lastMessage->receiver_id,
                        'message' => $lastMessage->message,
                        'conversation_id' => $lastMessage->conversation_id,
                        'created_at' => Carbon::parse($lastMessage->created_at)->diffForHumans(),
                    ]
                ];
            })
            ->sortByDesc(fn ($chat) => $chat['last_message']['created_at'])
            ->values()
            ->take(5);

        return $this->success($topUsers, 'Top 5 recent chat users fetched', 200);
    }
}
