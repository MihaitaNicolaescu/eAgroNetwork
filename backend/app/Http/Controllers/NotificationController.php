<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function getUnreadNotifications(Request $request){
        try{
            $recived = auth()->userOrFail();
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            return response()->json(['error' => $e->getMessage()], 401);
        }
        try{
            $notifications = Notification::where('id_user', '=', $recived['id'])->where('read', '=', 0)->orderBy('id', 'DESC')->get();
            return response()->json(['notifications' => $notifications], 200);
        }catch(Exception $e){
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
