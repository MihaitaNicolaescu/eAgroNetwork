<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function getFallow(Request $request){
        try{
            $recived = auth()->userOrFail();
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            return response()->json(['error' => $e->getMessage()], 401);
        }
        try{
            $array = User::where('id', '=', $recived['id'])->first();
            $fallowList = json_decode($array->fallowed);
            $isFallower = false;
            //de modificat foreach-ul
            foreach($fallowList as $userId){
                if($userId == $request->id_user){
                    $isFallower = true;
                    break;
                }
            }
            return response()->json($isFallower);
        }catch(Exception $e){
            return response()->json(['message' => 'Error on accesing database!'], 417);
        }
    }
    public function deleteFallower(Request $request){
        try{
            $recived = auth()->userOrFail();
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            return response()->json(['error' => $e->getMessage()], 401);
        }
        try{
            $array = User::where('id', '=', $recived['id'])->first();
            $fallowList = json_decode($array->fallowed);
            $key = array_search($request->fallowId, $fallowList);
            
            
            unset($fallowList[$key]);
            $fallowList = array_values($fallowList);
            $array->fallowed = json_encode($fallowList);
            $array->save();
        }catch(Exception $e){    
            return response()->json(['message' => 'Error on save data in database!'], 417);
        }
    }
    public function addFallower(Request $request){
        try{
            $recived = auth()->userOrFail();
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            return response()->json(['error' => $e->getMessage()], 401);
        }
        try{
            $fallowList = json_decode($recived['fallowed']);
            if($fallowList == null){
                $fallowList = [];
            }
            array_push($fallowList, $request->fallowId);
            $fallowList = json_encode($fallowList);
            $user = User::where('id', '=', $recived['id'])->first();
            $user->fallowed = $fallowList;
            $user->save();
            $recived['fallowed'] = $fallowList;
        }catch(Exception $e){    
            return response()->json(['message' => 'Error on save data in database!'], 417);
        }
    }

    public function updateUserProfilePhoto(Request $request){
        try{
            $recived = auth()->userOrFail();
            error_log($recived);
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            return response()->json(['error' => $e->getMessage()], 401);
        }
        $imageName = $request->image->getClientOriginalName(). '.jpg';
        error_log($request->token);
        try{
            $request->image->move(public_path('../../src/assets/profiles'), $imageName);
            $user = User::where('id', $recived['id'])->first();
            $user->profile_image = $imageName;
            $user->save();
            return response()->json(['message' => 'Image uploaded!'], 200); 
        }catch(Exception $e){
            return response()->json(['message' => 'Error on save data in database!'], 417);
        }
    }

    public function updateUserData(Request $request){
        try{
            $recived = auth()->userOrFail();
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            error_log($e);
            return response()->json(['error' => $e->getMessage()], 401);
        }
        try{
            $user = User::where('id', $recived['id'])->first();
            $user->firstName = $request->firstName;
            $user->lastName = $request->lastName;
            $user->email = $request->email;
            $user->birthday = $request->birthday;
            $user->save();
            return response() -> json(['message' => 'Succes'], 200);  
        }catch(Exception $e){
            return response()->json(['message' => 'Error on save data in database!'], 417);
        }
    }

    public function fetchUserData(Request $request){
        try{
            $recived = auth()->userOrFail();
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            error_log($e);
            return response()->json(['error' => $e->getMessage()], 401);
        }
        $user = User::where('id', $recived['id'])->first();
        return response()->json($user);
    }

    public function fetchFallowList(Request $request){
        try{
            $recived = auth()->userOrFail();
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            error_log($e);
            return response()->json(['error' => $e->getMessage()], 401);
        }
        $user = User::where('id', $recived['id'])->first();
        $fallowList = json_decode($user->fallowed);
        return response()->json($fallowList);
    }

    public function fetchProfile(Request $request){
        $user = User::where('id', $request->id)->first();
        return response()->json($user);
    }
    public function createUser(Request $request){
        try{
            $user = new User();
            $user->firstName = $request->firstName;
            $user->lastName = $request->lastName;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
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
            if(Hash::check($password, $user->password)){
                $credentials = $request->only(['email', 'password']);
                if (!$token = auth('api')->attempt($credentials)) {
                    return response()->json(['error' => 'Unauthorized'], 401);
                }        
                return response()->json(['token' => $token, 'admin' => $user->admin], 200);
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
