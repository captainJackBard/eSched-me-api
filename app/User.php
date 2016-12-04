<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Model implements JWTSubject, AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'first_name', 'last_name', 'img_name', 'skills', 'about_me', 'occupation', 'fuid'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function activities()
    {
        return $this->belongsToMany('App\Activity', 'activity_tags', 'friend_id', 'activity_id');
    }
    
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function friends()
    {
        return $this->belongsToMany('App\User', 'relationship', 'user_id', 'friend_id')
            ->withPivot('status');
    }

    public function friendsOfMine()
    {
        return $this->belongsToMany('App\User', 'relationship', 'user_id', 'friend_id')
            ->wherePivot('status', '=', 'accepted')
            ->withPivot('status');
    }

    public function friendOf()
    {
        return $this->belongsToMany('App\User', 'relationship', 'friend_id', 'user_id')
            ->wherePivot('status', '=', 'accepted')
            ->withPivot('status');
    }

    public function myRequests()
    {
        return $this->belongsToMany('App\User', 'relationship', 'user_id', 'friend_id')
            ->wherePivot('status', '=', 'pending')
            ->withPivot('status');
    }

    public function requestOf()
    {
        return $this->belongsToMany('App\User', 'relationship', 'friend_id', 'user_id')
            ->wherePivot('status', '=', 'pending')
            ->withPivot('status');
    }

    public function addFriend(User $user)
    {
        $this->friends()->attach([$user->id => ['status' =>'pending']]);
    }

    public function removeFriend(User $user)
    {
        $this->friends()->detach($user->id);
    }

    public function personalActivities()
    {
        $this->hasMany('App\PersonalActivity');
    }
}
