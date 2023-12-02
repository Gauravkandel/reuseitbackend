<?php

namespace App\Http\Controllers;

use App\Events\chatEvent;
use App\Models\message;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['getMessages']]);
    }
    public function message(Request $request)
    {
        $senderId = auth()->user()->id;
        $receiverId = $request->id; // Assuming the receiver ID is always 2 for simplicity
        $roomId = $this->createRoomId($senderId, $receiverId);
        $username = auth()->user()->name;
        $message = new message();
        $message->sender_id = $senderId;
        $message->receiver_id = $receiverId;
        $message->username = $username;
        $message->message = $request->message;
        $message->save();
        $data = message::Where('sender_id', $senderId)->Where('receiver_id', $receiverId)->latest()->first();
        $createdAt = Carbon::parse($data->created_at);
        $timeago = $createdAt->diffForHumans();
        event(new chatEvent($username, $request->input('message'), $roomId, $timeago, $senderId));
        return response()->json(['status' => 'Message sent successfully']);
    }
    public function getMessages($senderId, $receiverId)
    {
        $messages = Message::where(function ($query) use ($senderId, $receiverId) {
            $query->where('sender_id', $senderId)
                ->where('receiver_id', $receiverId);
        })->orWhere(function ($query) use ($senderId, $receiverId) {
            $query->where('sender_id', $receiverId)
                ->where('receiver_id', $senderId);
        })->orderBy('created_at', 'asc')->get();
        $messages->each(function ($message) {
            $message->timeago = Carbon::parse($message->created_at)->diffForHumans();
        });
        return response()->json(['messages' => $messages]);
    }
    public function createRoomId($user1, $user2)
    {
        // Ensure a consistent order for the IDs to prevent duplicates
        $sortedIds = [$user1, $user2];
        sort($sortedIds);
        // Concatenate the sorted user IDs
        return implode('_', $sortedIds);
    }
}
