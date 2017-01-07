<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PrivateMessage extends Model
{

    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = [
        'id',
        'sender_id', 
        'receiver_id',
        'message'
    ];

    public function receiver()
    {
        return $this->belongsTo('\App\User', 'receiver_id');
    }

    public function sender()
    {
        return $this->belongsTo('\App\User', 'sender_id');
    }
}