<?php

namespace App\Events;

use App\Chat;

class ChatEvent extends Event
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
}
