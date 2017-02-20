<?php
/**
 * Created by PhpStorm.
 * User: Djadjar Binks
 * Date: 2/19/2017
 * Time: 6:58 PM
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class GroupChat extends Model
{
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = [
        'id',
        'group_name',
    ];

    public function messages()
    {
        return $this->hasMany('App\Message');
    }
}