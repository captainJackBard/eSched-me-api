<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{

    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = [
        'id', 
        'activity_id', 
        'title', 
        'percentage',
        'description', 
        'start', 
        'end', 
        'status', 
        'priority',
        'quality',
        'risk',
        'created', 
        'modified'
    ];

    public function users()
    {
        return $this->belongsToMany('\App\User', 'module_tags', 'module_id', 'friend_id');
    }

    public function activity()
    {
        return $this->belongsTo('App\Activity');
    }

    public function submodules()
    {
        return $this->hasMany('App\SubModule');
    }
}