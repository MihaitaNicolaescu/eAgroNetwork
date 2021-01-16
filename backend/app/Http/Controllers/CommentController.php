<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    public function deleteComment(Request $request){
        try{
            $recived = auth()->userOrFail();
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            error_log($e);
            return response()->json(['error' => $e->getMessage()], 401);
        }
        try{
            $comment = DB::table('comments')->where('id', '=' ,$request->commentID)->delete();
            return response()->json(['message' => 'Success']);
        }catch(Exception $e){
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function addComment(Request $request){
        try{
            $recived = auth()->userOrFail();
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            error_log($e);
            return response()->json(['error' => $e->getMessage()], 401);
        }
        try{
            $comment = new Comment();
            $comment->user_id = $recived['id'];
            $comment->post_id = $request->postID;
            $comment->comment = $request->comment;
            $comment->save();
            return response()->json(['message' => 'Success']);
        }catch(Exception $e){
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function getComments(Request $request){
        try{
            $recived = auth()->userOrFail();
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            error_log($e);
            return response()->json(['error' => $e->getMessage()], 401);
        }
        try{
            $result = DB::table('comments')->leftJoin('users', function($join)
            {
                $recived = auth()->userOrFail();
                $join->on('comments.user_id', '=', 'users.id');

            })
                ->select('comments.id', 'comments.user_id', 'comments.post_id', 'comments.comment', 'users.firstName',
                    'users.lastName', 'users.link_profile')
                ->where('comments.post_id', '=', $request->postID)->orderBy('comments.id', 'desc')->get();
            return response()->json(['comments' => $result]);
        }catch(Exception $e){
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
