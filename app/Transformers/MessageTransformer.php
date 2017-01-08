<?php
namespace App\Transformers;

use App\PrivateMessage;
use App\User;
use League\Fractal;

class MessageTransformer extends Fractal\TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'sender', 'receiver'
    ];

    protected $defaultIncludes = [
        'sender','receiver'
    ];



    public function transform(PrivateMessage $msg)
    {
        return [
            'id' => $msg->id,
            'sender_id' => $msg->sender_id,
            'receiver_id' => $msg->receiver_id,
            'message' => $msg->message,
            'created' => (string)$msg->created,
        ];
    }

    public function includeSender(PrivateMessage $msg)
    {
        $sender = User::findOrFail($msg->sender_id);
        return $this->item($sender, new UserTransformer());
    }

    public function includeReceiver(PrivateMessage $msg)
    {
        $receiver = User::findOrFail($msg->receiver_id);
        return $this->item($receiver, new UserTransformer());
    }

}