<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{

    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = [
        'id',
        'user_id',
        'link',
        'message',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo('\App\User', 'user_id');
    }
}