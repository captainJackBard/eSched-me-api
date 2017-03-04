<?php
/**
 * Created by PhpStorm.
 * User: Djadjar Binks
 * Date: 2/19/2017
 * Time: 8:28 PM
 */

namespace App;


use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = [
        'id',
        'message',
        'sender_id',
        'group_chat_id'
    ];

    protected $appends = [
        'user'
    ];

    public function group()
    {
        return $this->belongsTo('App\GroupChat');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'sender_id');
    }

    public function getUserAttribute($value)
    {
        $user = User::findOrFail($this->sender_id);
        return $user;
    }
}