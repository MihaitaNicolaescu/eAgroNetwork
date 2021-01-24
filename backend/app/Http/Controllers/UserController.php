<?php
namespace App\Http\Controllers;

use App\Models\Recovery;
use App\Models\User;
use App\Models\Notification;
use http\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    //Resetare parola
    public function resetPassword(Request $request){
        try{

            $user = User::where('email', '=', $request->email)->first();
            $user->password = Hash::make($request->newPassword);
            $user->save();

            Recovery::where('email', '=', $request->email)->delete();
            return response()->json([
                'message' => 'success'
            ]);
        }
        catch(Exception $e){
            $e->getMessage();
            return response() -> json(['message' => $e->getMessage()]);
        }
    }

        public function verifyRecovery(Request $request){
            try{
                $email = $request->email;
                $code = $request->code;
                $recovery = Recovery::where('email', '=', $email)->first();
                if($recovery->code === $code) {
                    return response()->json([
                        'message' => 'success'
                    ]);

                }else{
                    return response()->json([
                        'message' => 'INVALID'
                    ]);
                }
            }
            catch(Exception $e){
                $e->getMessage();
                return response() -> json(['message' => $e->getMessage()]);
            }
        }
        public function recoveryPassword(Request $request){
            $host_frontend = env('HOST_FRONTEND');
            try{
                $email = $request->email;
                $user = User::where('email', $email)->first();
                if(!is_null($user)){
                    $recovery = Recovery::where('email', '=', $email)->first();
                    if(is_null($recovery)){
                        $code = $this->generateRandomString();
                        $recovery = new Recovery();
                        $recovery->code = $code;
                        $recovery->email = $email;
                        $recovery->save();
                    }else{
                        $code = $recovery->code;

                    }
                    $link= $host_frontend ."/recoverypassword/". $request->email . "/".$code;
                    $this->sendEmailRecovery($user->email, $user->firstName, $user->lastName, $link);
                }
                return response()->json([], 200);
            }
            catch(Exception $e){
                $e->getMessage();
                return response() -> json(['message' => $e->getMessage()]);
            }
        }

        public function sendEmailRecovery($email, $firstName, $lastName, $link){
            $to_name = $lastName;
            $to_email = $email;
            $to_link = $link;
            $data = array('name'=>"eAgro Network(sender_name)", "body" => "Recuperare parola", 'firstName' => $lastName, 'link'=>$link );

            Mail::send('email.recoveryPassword', $data, function($message) use ($to_name, $to_email, $to_link) {
                $message->to($to_email, $to_name, $to_link)
                    ->subject("Resetarea parola pe eAgro Network");
                $message->from("eagronetwork@gmail.com","eAgro Network");
            });
        }
    //
    public function sendEmail($email, $firstName, $lastName, $link){
        $to_name = $lastName;
        $to_email = $email;
        $to_link = $link;
        $data = array('name'=>"eAgro Network(sender_name)", "body" => "Bine ai venit!", 'firstName' => $lastName, 'link'=>$link );

        Mail::send('email.emailVerify', $data, function($message) use ($to_name, $to_email, $to_link) {
            $message->to($to_email, $to_name, $to_link)
                ->subject("Confirmare cont eAgro Network");
            $message->from("eagronetwork@gmail.com","eAgro Network");
        });
    }
    public function giveProducer(Request $request){
        try{
            $recived = auth()->userOrFail();
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            return response()->json(['error' => 'INVALID_TOKEN'], 401);
        }
        try{
            $user = User::where('id', '=', $request->producer_id)->first();
            $user->producer = 1;
            $user->save();
            return response()->json(['message' => 'SUCCESS']);
        }catch(Exception $e){
            return response()->json(['message' => 'Error on accesing database!'], 417);
        }
    }

    public function cancelProducer(Request $request){
        try{
            $recived = auth()->userOrFail();
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            return response()->json(['error' => 'INVALID_TOKEN'], 401);
        }
        try{
            $user = User::where('id', '=', $request->producer_id)->first();
            $user->producer = 0;
            $user->save();
            return response()->json(['message' => 'SUCCESS']);
        }catch(Exception $e){
            return response()->json(['message' => 'Error on accesing database!'], 417);
        }
    }

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
            $notification = Notification::where('from', '=', $recived['id'])->where('type', '=', 1)->where('id_user', '=', $request->fallowId)->first();
            $notification->delete();
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
            $notification = new Notification();
            $notification->id_user = $request->fallowId;
            $notification->from = $recived['id'];
            $notification->message = "A inceput sa te urmareasca.";
            $notification->read = 0;
            $notification->type = 1;
            $notification->firstName = $user->firstName;
            $notification->lastName = $user->lastName;
            $notification->save();
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
            $link = "/storage/profiles/" . $imageName;
            $request->image->move('../../backend/public/storage/profiles/', $imageName);
            $user = User::where('id', $recived['id'])->first();
            $user->profile_image = $imageName;
            $user->link_profile = $link;
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
            $user->judet = $request->judet;
            $user->birthday = $request->birthday;
            $user->save();
            return response() -> json(['message' => 'Succes'], 200);
        }catch(Exception $e){
            return response()->json(['message' => 'Error on save data in database!'], 417);
        }
    }

    public function localProducers(Request $request){
        try{
            $recived = auth()->userOrFail();
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            error_log($e);
            return response()->json(['error' => $e->getMessage()], 401);
        }
        try{
            $user = User::where('producer', 1)->where('judet','like','%'.$recived['judet'].'%')->where('id', '!=', $recived['id'])
                ->select('firstName', 'lastName', 'id', 'link_profile')->get();
            return response() -> json(['producers' => $user], 200);
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
        return response()->json($user, 200);
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
        if($user==null){
            return response()->json('INVALID_USER');
        }else{
            return response()->json($user);
        }
    }
    public  function generateRandomString($length = 20) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }


    public function resendEmail(Request $request){
        $host_frontend = env('HOST_FRONTEND');
        try{
            $email = $request->email;
            $user = User::where('email', $email)->first();
            $special_code = $user->code_verify;
            $link=$host_frontend . "/verifica/". $request->email . "/".$special_code;
            $this->sendEmail($user->email, $user->firstName, $user->lastName, $link);
            return response()->json([
                'message' => 'success'
            ]);
        }
        catch(Exception $e){
            $e->getMessage();
            return response() -> json(['message' => $e->getMessage()]);
        }
    }

    public function verifyAccount(Request $request){
        try{
            $email = $request->email;
            $code = $request->code;
            $user = User::where('email', '=', $email)->first();
            if($user->code_verify === $code) {
                $user->verified = 1;
                $user->save();
                return response()->json([
                    'message' => 'success'
                ]);

            }else{
                return response()->json([
                    'message' => 'INVALID'
                ]);
            }
        }
        catch(Exception $e){
            $e->getMessage();
            return response() -> json(['message' => $e->getMessage()]);
        }
    }

    public function createUser(Request $request){
        $host_frontend = env('HOST_FRONTEND');
        try{
            $special_code = $this->generateRandomString();
            $user = new User();
            $user->firstName = $request->firstName;
            $user->lastName = $request->lastName;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->birthday = $request->birthday;
            $user->profile_image = "default.jpg";
            $user->admin = 0;
            $user->producer = 0;
            $user->notifications = 0;
            $user->fallowed = '[]';
            $user->banned = 0;
            $user->reason = '';
            $user->warnings = 0;
            $user->code_verify = $special_code;
            $user->judet = $request->judet;
            $user->save();
            $link= $host_frontend ."/verifica/" . $request->email . "/".$special_code;
            $this->sendEmail($request->email, $request->firstName, $request->lastName, $link);
            return response()->json([
                'message' => 'success'
            ]);
        }
        catch(Exception $e){
            $e->getMessage();
            return response() -> json(['message' => $e->getMessage()]);
        }
    }

    public function loginUser(Request $request){
        try{
            $email = $request->email;
            $password = $request->password;
            $user = new User();
            $user = User::where('email', $email)->first();
            if(empty($user)){
                return response() -> json(['message' => "failed"]);
            }else{
                if(Hash::check($password, $user->password)){
                    if($user->banned === 1){
                        $credentials = $request->only(['email', 'password']);
                        return response()->json(['message' => 'IS_BANNED', 'info' => " Nu puteti sa accesati contul deoarece s-a descoperit incalcarea\n regulamentului si am fost nevoiti sa va restrictionam accesul\n in aplicatie!\n\n\nMotivul restrictiei: " . $user->reason], 200);

                    }elseif($user->verified == 0) {
                        return response()->json(['message' => 'NOT_VERIFY']);
                    }else{
                        $credentials = $request->only(['email', 'password']);
                        if (!$token = auth('api')->attempt($credentials)) {
                            return response()->json(['error' => 'Unauthorized'], 401);
                        }
                        return response()->json(['token' => $token, 'admin' => $user->admin], 200);
                    }
                }else{
                    return response() -> json([
                        'message' => 'failed'
                    ]);
                }
            }
        }catch(Exception $e){
            $e->getMessage();
            return response() -> json(['message' => $e->getMessage()]);
        }
    }

    public function banUser(Request $request){
        try{
            $recived = auth()->userOrFail();
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            error_log($e);
            return response()->json(['error' => $e->getMessage()], 401);
        }
        try{
            $user = User::where('id', $request->userID)->first();
            $user->banned = 1;
            $user->warnings = 0;
            $user->reason = $request->reason;
            $user->save();
            return response() -> json(['message' => 'Succes'], 200);
        }catch(Exception $e){
            return response()->json(['message' => 'Error on save data in database!'], 417);
        }
    }

    public function warnUser(Request $request){
        try{
            $recived = auth()->userOrFail();
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            error_log($e);
            return response()->json(['error' => $e->getMessage()], 401);
        }
        try{
            $user = User::where('id', $request->userID)->first();
            $user->warnings += 1;
            if($user->warnings === 3){
                $user->reason = "3/3 Averizari.";
                $user->warnings = 0;
                $user->banned = 1;
            }else{
                $notification = new Notification();
                $notification->id_user = $request->userID;
                $notification->from = $recived['id'];
                $notification->message = "Un administrator te-a sanctionat cu un advertisment pentru incalcarea
                 regulamentului. Aveti " . $user->warnings ."/3 avertizari. La 3 avertizari o sa primiti interdictie
                 in aplicatie.\nMotivul: ". $request->reason;
                $notification->read = 0;
                $notification->type = -124;
                $notification->firstName = '';
                $notification->lastName = '';
                $notification->save();
            }
            $user->save();
            return response() -> json(['message' => 'Succes'], 200);
        }catch(Exception $e){
            return response()->json(['message' => 'Error on save data in database!'], 417);
        }
    }

    public function unbanUser(Request $request){
        try{
            $recived = auth()->userOrFail();
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            error_log($e);
            return response()->json(['error' => $e->getMessage()], 401);
        }
        try{
            $user = User::where('id', $request->userID)->first();
            $user->banned = 0;
            $user->reason = '';
            $user->save();
            return response() -> json(['message' => 'Succes'], 200);
        }catch(Exception $e){
            return response()->json(['message' => 'Error on save data in database!'], 417);
        }
    }
}
