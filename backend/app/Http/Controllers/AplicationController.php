<?php

namespace App\Http\Controllers;

use App\Models\Aplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AplicationController extends Controller
{
    public function enableEditApplication(Request $request){
        try{
            $recived = auth()->userOrFail();
        }catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            return response()->json(['error' => $e->getMessage()]);
        }
        try{
            $application = Aplication::where('user_id', '=', $recived['id'])->first();
            $application->status = 0;
            $application->save();
            return response()->json(['message' => 'SUCCESS']);
        }catch(Exception $e){
            return response()->json(['message' => $e->getMessage() ]);
        }
    }

    public function rejectApplication(Request $request){
        try{
            $recived = auth()->userOrFail();
        }catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            return response()->json(['error' => $e->getMessage()]);
        }
        try{
            $application = Aplication::where('user_id', '=', $request->user_id)->first();
            $application->pending = 0;
            $application->status = -1;
            $application->motiv_respingere = $request->motiv;
            $application->save();
            return response()->json(['message' => 'SUCCESS']);
        }catch(Exception $e){
            return response()->json(['message' => $e->getMessage() ]);
        }
    }

    public function acceptApplication(Request $request){
        try{
            $recived = auth()->userOrFail();
        }catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            return response()->json(['error' => $e->getMessage()]);
        }
        try{
            $application = Aplication::where('user_id', '=', $request->user_id)->first();
            $application->pending = 0;
            $application->status = 1;
            $application->save();
            $user = User::where('id', $request->user_id)->first();
            $user->producer = 1;
            $user->save();
            return response()->json(['message' => 'SUCCESS']);
        }catch(Exception $e){
            return response()->json(['message' => $e->getMessage() ]);
        }
    }

    public function getApplication(Request $request){
        try{
            $recived = auth()->userOrFail();
        }catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            return response()->json(['error' => $e->getMessage()]);
        }
        try{
            $result = Aplication::where('user_id', '=', $request->id)->first();

            if(!is_null($result)) return response()->json(['application' => $result], 200);
            else return response()->json(['application' => false], 200);
        }catch(Exception $e){
            return response()->json(['message' => $e->getMessage() ]);
        }
    }

    public function getApplications(){
        try{
            $recived = auth()->userOrFail();
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            return response()->json(['error' => $e->getMessage()]);
        }
        try{
            $usersList = Aplication::select('user_id')->where('pending', '=', 1)->get();
            $usersList = $usersList->pluck('user_id')->toArray();
            $userData = User::whereIn('id',  $usersList)->get();
            return response()->json(['users' => $userData]);
        }catch(Exception $e){
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function deleteApplication(){
        try{
            $recived = auth()->userOrFail();
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            return response()->json(['error' => $e->getMessage()]);
        }
        try{
            Aplication::where('user_id', $recived['id'])->delete();
            return response()->json(['message' => 'APPLICATION_DELETED']);
        }catch(Exception $e){
            return response()->json(['error' =>  $e->getMessage()]);
        }
    }
    public function checkForApplication(Request $request){
        try{
            $recived = auth()->userOrFail();
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            return response()->json(['error' => $e->getMessage()], 401);
        }
        try{
            $application = Aplication::where('user_id', $recived['id'])->first();
            if(is_null($application)) return response()->json(['message' => "NO_APPLICATION"]);
            else return response()->json(['data' => $application]);
        }catch(Exception $e){
            return response()->json(['error' =>  $e->getMessage()]);
        }
    }

    public function uploadForm(Request $request){
        try{
            $recived = auth()->userOrFail();
            error_log($recived);
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            return response()->json(['error' => $e->getMessage()], 401);
        }
        $copie_ci = $request->copie_ci->getClientOriginalName();
        $certificat = $request->copie_certificat->getClientOriginalName();
        var_dump($copie_ci);
        var_dump($certificat);
        error_log($request->token);
        try{
            $request->copie_ci->move(public_path('../../src/assets/applications'), $copie_ci);
            $request->copie_certificat->move(public_path('../../src/assets/applications'), $certificat);

            return response()->json(['message' => 'Image uploaded!'], 200);
        }catch(Exception $e){
            return response()->json(['message' => 'Error on save data in database!'], 417);
        }

    }

    public function addApplication(Request $request){
        try{
            $recived = auth()->userOrFail();
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            return response()->json(['message' => 'INVALID_TOKEN'], 401);
        }
        try{
            if($request->existApp == false){
                $aplication = new Aplication();
            }else{
                $aplication = Aplication::where('user_id', '=', $recived['id'])->first();
            }
            $aplication->user_firstName = $request->firstName;
            $aplication->user_lastName = $request->lastName;
            $aplication->user_email = $request->email;
            $aplication->user_id = $recived['id'];
            $aplication->judet = $request->judet;
            $aplication->localitate = $request->localitate;
            $aplication->adresa = $request->adresa;
            $aplication->serie_ci = $request->serie_ci;
            $aplication->numar_serie_ci = $request->numar_ci;
            $aplication->cnp = $request->cnp;
            $aplication->nr_certificat = $request->nr_certificat;
            $aplication->primaria = $request->primaria;
            if($request->altele !== null) $aplication->alte_precizari = $request->altele;
            $aplication->copie_ci = "copie_ci_" . $recived['id'] . ".jpg";
            $aplication->copie_certificat = "copie_certificat_" . $recived['id'] . ".jpg";
            $aplication->status = 0;
            $aplication->pending = 1;
            $aplication->save();
            return response()->json(['message'=> 'APPLICATION_ADDED']);
        }catch(Exception $e){
            return response()->json(['message' => $e->getMessage()]);
        }
    }
}
