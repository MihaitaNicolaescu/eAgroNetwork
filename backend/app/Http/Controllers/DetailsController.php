<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use App\Product;
use Spatie\Searchable\Search;

class DetailsController extends Controller
{
    public function search(Request $request){ // functie pentru cautarea unui user folosita in home page (cautare dinamica)
        $result = (new Search())->registerModel(User::class, ['firstName', 'lastName'])->search($request->input('query'));

        return response()->json($result);
    }

    public function pagination(Request $request){//functie pentru paginare
        try{
            if($request->searchedUser == ''){
                $users = User::paginate(10);
                return response()->json($users, 200);
            }else{
                $users = User::where('firstName','like','%'.$request->searchedUser.'%')->orWhere('lastName','like', '%'.$request->searchedUser.'%')->orWhere('email', 'like', '%'.$request->searchedUser.'%')->paginate(10);
                return response()->json($users, 200);
            }
        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function verifyData(Request $request){ //functie pentru verificarea token-ului si a unui user daca e admin
        try{
            $recived = auth()->userOrFail();
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            return response()->json(['error' => $e->getMessage()], 401);
        }
        try{
            $isAdmin;
            $user = User::where('id', $recived['id'])->first();
            error_log($user->admin);
            if($user->admin === 1) return response() -> json(['isAdmin' => 1], 200);
            else return response()-> json(['isAdmin' => 0],200);
        }catch(Exception $e){
            return response()->json(417);
        }
    }

    public function deleteUser(Request $request){
        try{
            $user = User::where('id', $request->id)->first();
            error_log($request->id);
            $user->delete();
            return response()->json(['message' => 'success']);
        }catch(Exception $e){
            return response()->json(['message' => $e]);
        }
    }
}
