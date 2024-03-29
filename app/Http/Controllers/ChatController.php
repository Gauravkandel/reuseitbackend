<?php

namespace App\Http\Controllers;

use App\Events\chatEvent;
use App\Events\NotifyEvent;
use App\Events\TypingEvent;
use App\Models\message;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        if ($request->file('msg_image') != null) {
            $message_img = $request->file('msg_image');
            $msg_image = time() . $message_img->getClientOriginalName();
            $message_img->move(public_path('msg_images'), $msg_image);
            $message->msg_image = $msg_image;
        } else {
            $msg_image = "";
        }
        $message->sender_id = $sender_id;
        $message->receiver_id = $receiverId;
        $message->username = $username;
        if ($request->message != null) {
            $msgs = $request->message;
            $message->message = $request->message;
        } else {
            $msgs = "";
            $message->message = "";
        }
        $message->save();
        $data = message::Where('sender_id', $sender_id)->Where('receiver_id', $receiverId)->latest()->first();
        $createdAt = Carbon::parse($data->created_at);
        $timeago = $createdAt->diffForHumans();
        event(new chatEvent($username, $msgs, $roomId, $timeago, $sender_id, $sender->Profile_image, $receiverdata->Profile_image, $msg_image));
        return response()->json(['status' => 'Message sent successfully']);
    }
    public function notityUser()
    {
        $user = auth()->user();
        $roomId =  $user->id;
        $notifications = $user->notifications()->get();
        $notificationData = $notifications->pluck('data')->map(function ($item) {
            return $item[0]; // Check if the key exists
        });

        $notify['data'] = $notificationData->toArray();
        $notify['count'] = $user->unreadNotifications->count();
        event(new NotifyEvent($roomId, $notify));
        return response()->json(["notification" => $notify]);
    }
    public function markAsRead()
    {
        $user = auth()->user();
        $user->unreadNotifications->markAsRead();
        $roomId =  $user->id;
        $notifications = $user->notifications()->get();
        $notificationData = $notifications->pluck('data')->map(function ($item) {
            return $item[0]; // Check if the key exists
        });
        $notify['data'] = $notificationData->toArray();
        $notify['count'] = $user->unreadNotifications->count();
        event(new NotifyEvent($roomId, $notify));
    }
    public function getMessages($senderId, $receiverId)
    {
        $senderdata = User::find($senderId);
        $receiverdata = User::find($receiverId);
        $messages_stat = message::where('sender_id', $receiverId)->where('receiver_id', $senderId)->get();
        foreach ($messages_stat as $message) {
            if ($message->msg_status === 0) {
                $message->msg_status = 1;
                $message->save();
            }
        }
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
    public function getUsers()
    {
        $authUser = auth()->user();
        $latestMessages = message::whereIn('id', function ($query) use ($authUser) {
            $query->select(DB::raw('MAX(id)'))
                ->from('messages')
                ->where(function ($subquery) use ($authUser) {
                    $subquery->where('sender_id', $authUser->id)
                        ->orWhere('receiver_id', $authUser->id);
                })->groupBy(DB::raw('CASE WHEN sender_id = ' . $authUser->id . ' 
                THEN receiver_id ELSE sender_id END'));
        })->orderBy('created_at', 'desc')->get();
        foreach ($latestMessages as $messages) {

            if ($messages->sender_id === auth()->id()) {
                $userdata = User::find($messages->receiver_id);
                $unseenMessages = message::where('receiver_id', $messages->sender_id)
                    ->where('sender_id', $messages->receiver_id)
                    ->where('msg_status', 0)
                    ->get();
            } else {
                $userdata = User::find($messages->sender_id);
                $unseenMessages = message::where('receiver_id', $messages->receiver_id)
                    ->where('sender_id', $messages->sender_id)
                    ->where('msg_status', 0)
                    ->get();
            }
            $messages->unseen_msg = count($unseenMessages);
            $messages->otherUserdata = $userdata;
            $messages->authUserData = auth()->user();
        }

        // Return the response
        return response()->json($latestMessages);
    }
    public function InitUser()
    {
        $authUser = auth()->user();
        $topuser = message::where('sender_id', $authUser->id)->orWhere('receiver_id', $authUser->id)->latest()->first();
        if ($topuser->sender_id === auth()->id()) {
            $id = $topuser->receiver_id;
        } else {
            $id = $topuser->sender_id;
        }
        return response()->json($id);
    }
    public function ChatProfile($id)
    {
        $chatUserdata = User::find($id);
        $chatUserdata->Timeago =  Carbon::parse($chatUserdata->ActiveTime)->diffForHumans();
        return response()->json($chatUserdata);
    }
    public function getMessageCount()
    {
        $count = message::where('receiver_id', auth()->id())->where('msg_status', 0)->count();
        return response()->json(['count' => $count]);
    }
    public function typing(Request $request)
    {
        $receiverId = $request->id;
        $roomId = $this->createRoomId(auth()->id(), $receiverId);

        broadcast(new TypingEvent($roomId, true, auth()->id()))->toOthers();

        return response()->json(['status' => 'Typing event sent']);
    }

    public function notTyping(Request $request)
    {
        $receiverId = $request->id;
        $roomId = $this->createRoomId(auth()->id(), $receiverId);

        broadcast(new TypingEvent($roomId, false, auth()->id()))->toOthers();

        return response()->json(['status' => 'Not typing event sent']);
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
