<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Events\ChatEvent;
use App\Events\GroupChatEvent;
use App\GroupChat;
use App\Message;
use App\Transformers\MessageTransformer;
use App\Transformers\UserTransformer;
use App\User;
use App\PrivateMessage;
use App\Http\Controllers\Controller;
use App\Transformers\ActivityTransformer;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Auth;

class ChatController extends Controller
{

    protected $fractal;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->fractal = new Manager();
        if (isset($_GET['include'])) {
            $this->fractal->parseIncludes($_GET['include']);
        }
    }

    public function message(Request $request, $id)
    {
        $receiver = User::findOrFail($id);
        $sender = Auth::user();

        $parent_chat = PrivateMessage::where(function ($query) use ($id, $sender) {
            $query->whereIn('sender_id', [$id,$sender->id])
                ->whereIn('receiver_id', [$id, $sender->id]);
        })->where('parent_id', null)->first();
        $chat = new PrivateMessage();
        if($parent_chat) {
            $chat->parent_id = $parent_chat->id;
        }

        $chat->sender_id = $sender->id;
        $chat->receiver_id = $id;
        $chat->message = $request->input('message');

        $data = [
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'message' => $request->input('message'),
        ];

        if($chat->save()) {
            $chat->sender = $sender;
            $chat->receiver = User::findOrFail($id);
            $chat->parent = $parent_chat;
            event(new ChatEvent($chat));
            return response()->json($chat);
        }
    }

    public function groupMessage(Request $request)
    {
        $sender = Auth::user();
        $message = new Message();
        $message->message = $request->input('message');
        $message->sender_id = $sender->id;
        if($request->input('group_chat_id')) {
            $group_chat = GroupChat::findOrFail($request->input('group_chat_id'));
            $message->group_chat_id = $group_chat->id;
        } else if($request->input('group_name')){
            $group_name = $request->input('group_name');
            $group_chat = GroupChat::create();
            $message->group_chat_id = $group_chat->id;
            $message->sender_id = -1;
            $message->save();
            $group_chat->group_name = $group_name;
            $group_chat->message_id = $message->id;
            $group_chat->save();
            return response()->json($group_chat);
        }
        $message->save();
        event(new GroupChatEvent($message));
        return response()->json($message);
        // TODO: Add Pusher.com Calls
    }

    public function getGroupMessage()
    {
        $user = Auth::user();
        $chats = [];
        $user->acceptedActivities->each(function ($activity) use (&$chats) {
            $chats[] = $activity->groupChat;
        });
        return response()->json($chats);
    }

    public function getGroupMessageDetails($id)
    {
        $user = Auth::user();
        $group_chat = GroupChat::findOrFail($id);
        $messages = $group_chat->messages;
        return response()->json($messages);
    }

    public function addUserToGroupMessage(Request $request, $id)
    {
        $group_chat = GroupChat::findOrFail($id);
        $user = User::findOrFail($request->user_id);
        $message = Message::create([
            'message' => $user->first_name . ' ' . $user->last_name . ' is added to the group chat!',
            'sender_id' => $user->id,
            'group_chat_id' => $group_chat->id,
        ]);
        return response()->json($message);
    }

    public function myMessages()
    {
        $user = Auth::user();
        $messages = $user->sentMessages->merge($user->receivedMessages)->where('parent_id', null);
        $data = fractal()->collection($messages, new MessageTransformer())->toArray();
        return response()->json($data);
    }

    public function showMessageThread($id)
    {
        $user = Auth::user();
        $parent_message = $user->sentMessages->merge($user->receivedMessages)->where('id', $id);
        $messages = $user->sentMessages->merge($user->receivedMessages)->where('parent_id', $id);
        $messages = $messages->merge($parent_message);
        $data = fractal()->collection($messages, new MessageTransformer())->toArray();
        return response()->json($data);
    }
}
