<?php

namespace App\Events;

use App\Chat;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ChatEvent extends Event implements ShouldBroadcast
{

	public $message;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Chat $message)
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        // TODO: Implement broadcastOn() method.
        return new PrivateChannel('message.'.$this->message->id);
    }
}
