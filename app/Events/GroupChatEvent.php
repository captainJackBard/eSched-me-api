<?php
/**
 * Created by PhpStorm.
 * User: Djadjar Binks
 * Date: 2/19/2017
 * Time: 6:55 PM
 */

namespace App\Events;


use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Message;

class GroupChatEvent extends Event implements ShouldBroadcast
{
    public $message;
    /**
     * GroupChatEvent constructor.
     */
    public function __construct(Message $msg)
    {
        $this->message = $msg;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['groupchat.'.$this->message->group_chat_id];
    }
}