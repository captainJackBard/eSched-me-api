<?php

namespace App\Http\Controllers;

use App\Events\ChatEvent;
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

        $data = [
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'message' => $request->input('message'),
        ];

        if($chat = PrivateMessage::create($data) ) {
            event(new ChatEvent($chat));
            return response()->json('chat session initialized');
        }
    }

    public function myMessages()
    {
        $user = Auth::user();
        $messages = $user->sentMessages->merge($user->receivedMessages)->where('parent_id', null);
        return response()->json($messages);
    }

    public function showMessageThread($id)
    {
        $user = Auth::user();
        $parent_message = $user->sentMessages->merge($user->receivedMessages)->where('id', $id);
        $messages = $user->sentMessages->merge($user->receivedMessages)->where('parent_id', $id);
        return response()->json($messages->merge($parent_message));
    }
}
