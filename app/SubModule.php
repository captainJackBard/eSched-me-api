<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubModule extends Model
{

    public $timestamps = false;

    protected $fillable = [
        'id', 
        'module_id', 
        'title', 
        'percentage',
        'description', 
        'status', 
    ];


    public function module()
    {
        return $this->belongsTo('App\Module');
    }
}