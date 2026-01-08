<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;
use App\Services\PushNotificationService;

class ChatController extends Controller
{
    // Indian Standard Time timezone
    private $timezone = 'Asia/Kolkata';

    public function index(User $user)
    {
        // Mark all messages from this user as read
        Message::where('sender_id', $user->id)
            ->where('receiver_id', auth()->id())
            ->where('read', false)
            ->update(['read' => true]);

        // Get the last 20 messages (ordered by created_at desc, then reversed for display)
        $messages = Message::where(function ($q) use ($user) {
            $q->where('sender_id', auth()->id())
              ->where('receiver_id', $user->id);
        })->orWhere(function ($q) use ($user) {
            $q->where('sender_id', $user->id)
              ->where('receiver_id', auth()->id());
        })
        ->orderBy('created_at', 'desc')
        ->limit(20)
        ->get()
        ->reverse()
        ->values();

        // Convert timestamps to IST
        $messages->each(function ($msg) {
            $msg->formatted_time = $msg->created_at->setTimezone($this->timezone)->format('h:i A');
        });

        return view('chat.index', [
            'messages' => $messages,
            'user' => $user
        ]);
    }

    public function store(Request $request)
    {
        $message = Message::create([
            'sender_id'   => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'message'     => $request->message,
            'read'        => false,
        ]);

        // Broadcast the message event (if configured)
        try {
            broadcast(new MessageSent($message, auth()->user()))->toOthers();
        } catch (\Exception $e) {
            // Broadcasting not configured, continue without it
        }

        // Send push notification to receiver (lazy-loaded to avoid errors if extensions missing)
        try {
            $receiver = User::find($request->receiver_id);
            if ($receiver) {
                $pushService = app(PushNotificationService::class);
                $pushService->sendToUser(
                    $receiver,
                    auth()->user()->name,
                    $request->message,
                    [
                        'sender_id' => auth()->id(),
                        'sender_name' => auth()->user()->name,
                        'url' => route('chat.show', auth()->id()),
                    ]
                );
            }
        } catch (\Exception $e) {
            // Push notification failed, continue without it
            \Log::warning('Push notification failed: ' . $e->getMessage());
        }

        return response()->json([
            'status' => 'sent',
            'message' => [
                'id' => $message->id,
                'message' => $message->message,
                'sender_id' => $message->sender_id,
                'receiver_id' => $message->receiver_id,
                'read' => (bool) $message->read,
                'created_at' => $message->created_at->setTimezone($this->timezone)->format('h:i A'),
            ]
        ]);
    }

    /**
     * Get messages for polling (returns messages after a certain ID, limited to 20)
     */
    public function getMessages(User $user, Request $request)
    {
        $lastMessageId = $request->query('last_id', 0);
        $timezone = $this->timezone;

        // Mark incoming messages as read
        Message::where('sender_id', $user->id)
            ->where('receiver_id', auth()->id())
            ->where('read', false)
            ->update(['read' => true]);

        $messages = Message::where(function ($q) use ($user) {
            $q->where('sender_id', auth()->id())
              ->where('receiver_id', $user->id);
        })->orWhere(function ($q) use ($user) {
            $q->where('sender_id', $user->id)
              ->where('receiver_id', auth()->id());
        })
        ->where('id', '>', $lastMessageId)
        ->orderBy('created_at')
        ->limit(20)
        ->get()
        ->map(function ($msg) use ($timezone) {
            return [
                'id' => $msg->id,
                'message' => $msg->message,
                'sender_id' => $msg->sender_id,
                'receiver_id' => $msg->receiver_id,
                'read' => (bool) $msg->read,
                'created_at' => $msg->created_at->setTimezone($timezone)->format('h:i A'),
            ];
        });

        return response()->json([
            'messages' => $messages
        ]);
    }

    /**
     * Get read status updates for sent messages
     */
    public function getReadStatus(User $user, Request $request)
    {
        $messageIds = $request->query('ids', '');
        
        if (empty($messageIds)) {
            return response()->json(['statuses' => []]);
        }

        $ids = array_map('intval', explode(',', $messageIds));

        $messages = Message::whereIn('id', $ids)
            ->where('sender_id', auth()->id())
            ->where('receiver_id', $user->id)
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'read' => (bool) $msg->read,
                ];
            });

        return response()->json([
            'statuses' => $messages
        ]);
    }

    /**
     * Delete a message (only for user ID 1) - soft delete
     */
    public function destroy($messageId)
    {
        // Only allow user ID 1 to delete messages
        if (auth()->id() != 1) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $message = Message::find($messageId);
        
        if (!$message) {
            return response()->json([
                'success' => false,
                'message' => 'Message not found'
            ], 404);
        }

        $message->delete(); // Soft delete

        return response()->json([
            'success' => true,
            'message' => 'Message deleted'
        ]);
    }

    /**
     * Delete all messages in a conversation (only for user ID 1) - soft delete
     */
    public function destroyAll(User $user)
    {
        // Only allow user ID 1 to delete messages
        if (auth()->id() != 1) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Soft delete all messages in this conversation
        $deleted = Message::where(function ($q) use ($user) {
            $q->where('sender_id', auth()->id())
              ->where('receiver_id', $user->id);
        })->orWhere(function ($q) use ($user) {
            $q->where('sender_id', $user->id)
              ->where('receiver_id', auth()->id());
        })->delete();

        return response()->json([
            'success' => true,
            'message' => 'All messages deleted',
            'count' => $deleted
        ]);
    }
}
