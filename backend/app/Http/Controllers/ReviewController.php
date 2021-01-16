<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{

    function getMyReview(Request $request){
        try{
            $recived = auth()->userOrFail();
            try{
                //$review = DB::table('reviews')->where('reviewer_id', '=', $request->userID)->first();


                $review = Review::where('user_id', '=', $request->userPageID)->where('reviewer_id', '=', $request->userID)->first();
                return response()->json(['review' => $review],200, [], JSON_NUMERIC_CHECK);

            }catch(Exception $e){
                return response()->json(['message' => $e]);
            }
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            error_log($e);
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    function getReviews(Request $request){
        try{
            $recived = auth()->userOrFail();
            try{
                $reviews = DB::table('reviews')->leftJoin('users', function($join)
                {
                    $recived = auth()->userOrFail();
                    $join->on('reviews.reviewer_id', '=', 'users.id');

                })
                    ->select('reviews.id', 'reviews.user_id', 'reviews.reviewer_id', 'reviews.rating',
                        'reviews.message', 'users.lastName', 'users.firstName', 'users.link_profile')
                    ->where('reviews.user_id', '=', $request->userID)->get();


                //$reviews = Review::where('user_id', '=', $request->userID)->orderBy('id', 'desc')->get();
                return response()->json(['reviews' => $reviews],200, [], JSON_NUMERIC_CHECK);

            }catch(Exception $e){
                return response()->json(['message' => $e]);
            }
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            error_log($e);
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    function sendReview(Request $request){
        try{
            $recived = auth()->userOrFail();
            try{
                $review = Review::where('reviewer_id', '=', $recived['id'])->where('user_id', '=', $request->for)->first();
                if(is_null($review)){
                    $review = new Review();
                    $review->reviewer_id = $recived['id'];
                    $review->user_id = $request->for;
                    $review->message = $request->message . " ";
                    $review->rating = $request->scor;
                    $review->save();

                    $user = User::where('id', '=', $recived['id'])->first();
                    $notification = new Notification();
                    $notification->id_user = $request->for;
                    $notification->from = $recived['id'];
                    $notification->message = "A adaugat o recenzie la profilul tau!";
                    $notification->read = 0;
                    $notification->type = 1;
                    $notification->firstName = $user->firstName;
                    $notification->lastName = $user->lastName;
                    $notification->save();
                }else{
                    $review->message = $request->message. " ";
                    $review->rating = $request->scor;
                    $review->save();

                    $user = User::where('id', '=', $recived['id'])->first();
                    $notification = new Notification();
                    $notification->id_user = $request->for;
                    $notification->from = $recived['id'];
                    $notification->message = "A modificat recenzia lasata la profilul tau!";
                    $notification->read = 0;
                    $notification->type = 1;
                    $notification->firstName = $user->firstName;
                    $notification->lastName = $user->lastName;
                    $notification->save();
                }
                return response()->json([],200);

            }catch(Exception $e){
                return response()->json(['message' => $e]);
            }
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            error_log($e);
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }
}
