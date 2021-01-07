<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{

    public function getNotifications(Request $request){
        try{
            $recived = auth()->userOrFail();
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            return response()->json(['error' => $e->getMessage()], 401);
        }
        try{
            $result = DB::table('notifications')->leftJoin('users', function($join){
                $recived = auth()->userOrFail();
                $join->on('notifications.from', '=', 'users.id');
            })->select('notifications.id_user', 'notifications.message', 'notifications.from','notifications.read',
                'notifications.firstName', 'notifications.type', 'notifications.lastName', 'users.profile_image')
                ->where('notifications.id_user', '=', $recived['id'])
                ->orderBy('notifications.id', 'DESC')->get();
            return response()->json(['notifications' => $result], 200);
        }catch(Exception $e){
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function getUnreadNotifications(Request $request){
        try{
            $recived = auth()->userOrFail();
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            return response()->json(['error' => $e->getMessage()], 401);
        }
        try{
            $result = DB::table('notifications')->leftJoin('users', function($join){
                $recived = auth()->userOrFail();
                $join->on('notifications.from', '=', 'users.id');
            })->select('notifications.id_user', 'notifications.message', 'notifications.from','notifications.read',
                'notifications.firstName', 'notifications.lastName', 'users.profile_image', 'notifications.type')
                ->where('notifications.id_user', '=', $recived['id'])->where('notifications.read', '=', 0)
                ->orderBy('notifications.id', 'DESC')->get();
            return response()->json(['notifications' => $result], 200);
        }catch(Exception $e){
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function markReadNotifications(){
        try{
            $recived = auth()->userOrFail();
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            return response()->json(['error' => $e->getMessage()], 401);
        }
        try{
            $notifications = Notification::where('id_user', '=', $recived['id'])->where('read', '=', 0)->update(['read' => 1]);
            return response()->json(['notifications' => $notifications], 200);
        }catch(Exception $e){
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
