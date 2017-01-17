<?php

namespace App\Http\Controllers;

use App\Events\ChatEvent;
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
