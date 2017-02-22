<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{

    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = [
        'id',
        'activity_id',
        'location',
        'long',
        'lat',
        'status',
        'date'
    ];

    public function activity()
    {
        return $this->belongsTo('App\Activity', 'activity_id');
    }
}