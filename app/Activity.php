<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{

    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = [
        'id', 
        'user_id', 
        'title', 
        'desc',
        'budget',
        'vendor',
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

    public function modules()
    {
        return $this->hasMany('App\Module');
    }

    public function locations() {
        return $this->hasMany('App\Location');
    }
}