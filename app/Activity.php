<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'id', 
        'user_id', 
        'title', 
        'desc', 
        'start', 
        'end', 
        'status', 
        'priority', 
        'created', 
        'modified'
    ];

    public function users()
    {
        return $this->belongsToMany('\App\User', 'activity_tags', 'activity_id', 'friend_id');
    }
}