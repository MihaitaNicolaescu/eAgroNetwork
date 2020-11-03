<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;

class UserController extends Controller
{
    public function updateUserProfilePhoto(Request $request){

        $imageName = $request->image->getClientOriginalName(). '.jpg';
        try{
            $request->image->move(public_path('../../src/assets/profiles'), $imageName);
            $user = User::where('id', $request->id)->first();
            $user->profile_image = $imageName;
            $user->save();
        }catch(Exception $e){
            console.log($e);
        }
        return response()->json(['message' => 'Image uploaded!']);
    }

    public function updateUserData(Request $request){
        try{
            $user = User::where('email', $request->email)->first();
            $user->firstName = $request->firstName;
            $user->lastName = $request->lastName;
            $user->email = $request->email;
            $user->birthday = $request->birthday;
            $user->save();
        }
        catch(Exception $error){
            return response() -> json([
                'message' => $error->getMessage()
            ]); 
        }
    }

    public function fetchUserData(Request $request){
        $user = User::where('email', $request->email)->first();
        return response()->json($user);
    }
    public function fetchProfile(Request $request){
        $user = User::where('id', $request->id)->first();
        return response()->json($user);
    }
    public function createUser(Request $request){
       // \Log::debug(json_encode(['request' => $request]));
        try{
            $user = new User();
            $user->firstName = $request->firstName;
            $user->lastName = $request->lastName;
            $user->email = $request->email;
            $user->password = $request->password;
            $user->birthday = $request->birthday;
            $user->profile_image = "default.jpg";
            $user->save();
            return response()->json([
                'message' => 'success'
            ]);
        }
        catch(Exception $e){
            $e->getMessage();
        }
    }

    public function loginUser(Request $request){
        try{
            $email = $request->email;
            $password = $request->password;
            $user = new User();
            $user = User::where('email', $email)->first();
            if($user->password == $password){
                return response() ->json([
                    'message' => 'success'
                ]);
            }else{
                return response() -> json([
                    'message' => 'failed'
                ]);
            }
        }catch(Exception $e){
            $e->getMessage();
        }
    }
}
