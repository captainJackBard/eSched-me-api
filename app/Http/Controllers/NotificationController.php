<?php

namespace App\Http\Controllers;

use App\Notification;
use App\Http\Controllers\Controller;
use Auth;

class NotificationController  extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications;
        return response()->json($notifications);
    }

    public function delete($id)
    {
        $user = Auth::user();
        if($notif = Notification::where('id', $id)->where('user_id', $user->id)->firstOrFail()) {
            if($notif->delete()) {
                return response()->json("Notification Deleted!");
            }
        }
        return response()->json('You\'re not allowed to do that.');
    }

    public function deleteAll()
    {
        $user = Auth::user();
        if(Notification::where('user_id', $user->id)->delete()) {
            return response()->json('All Notifications Deleted!');    
        }
        return response()->json('You\'re not allowed to do that.');
    }
}
