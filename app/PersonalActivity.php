<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PersonalActivity extends Model
{

    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = [
        'id', 
        'user_id', 
        'title', 
        'description', 
        'reminder_date', 
        'status',
        'created', 
        'modified'
    ];

    public function user()
    {
        return $this->belongsToMany('App\User');
    }
}