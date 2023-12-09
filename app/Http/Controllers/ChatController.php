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
        $sender = auth()->user();
        $sender_id = auth()->user()->id;
        $receiverId = $request->id;
        $receiverdata = User::find($request->id);
        // Assuming the receiver ID is always 2 for simplicity
        $roomId = $this->createRoomId($sender_id, $receiverId);
        $username = auth()->user()->name;
        $message = new message();
        $message->sender_id = $sender_id;
        $message->receiver_id = $receiverId;
        $message->username = $username;
        $message->message = $request->message;
        $message->save();
        $data = message::Where('sender_id', $sender_id)->Where('receiver_id', $receiverId)->latest()->first();
        $createdAt = Carbon::parse($data->created_at);
        $timeago = $createdAt->diffForHumans();
        event(new chatEvent($username, $request->input('message'), $roomId, $timeago, $sender_id, $sender->Profile_image, $receiverdata->Profile_image));
        return response()->json(['status' => 'Message sent successfully']);
    }
    public function getMessages($senderId, $receiverId)
    {
        $senderdata = User::find($senderId);
        $receiverdata = User::find($receiverId);

        $messages = Message::where(function ($query) use ($senderId, $receiverId) {
            $query->where('sender_id', $senderId)
                ->where('receiver_id', $receiverId);
        })->orWhere(function ($query) use ($senderId, $receiverId) {
            $query->where('sender_id', $receiverId)
                ->where('receiver_id', $senderId);
        })->orderBy('created_at', 'asc')->get();
        $messages->each(function ($message) use ($receiverdata, $senderdata) {
            $message->timeago = Carbon::parse($message->created_at)->diffForHumans();
            $message->sender_image = $senderdata->Profile_image;
            $message->receiver_image = $receiverdata->Profile_image;
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
