<?php

namespace App\Http\Controllers;

use App\Models\PostVotes;
use Illuminate\Http\Request;

class PostVoteController extends Controller
{
    function fetchUserPostsVoted(Request $request){
        try{
            $recived = auth()->userOrFail();
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            error_log($e);
            return response()->json(['error' => $e->getMessage()], 401);
        }
        try{
           
            $votedPosts = PostVotes::where('user_id', $request->user_id)->get();
            return response()->json(['posts_voted' => $votedPosts], 200);

        }catch(Exception $e){
            return response()->json(['message' => $e]);
        }
    }

    function modifyVote(Request $request){
        try{
            $recived = auth()->userOrFail();
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            error_log($e);
            return response()->json(['error' => $e->getMessage()], 401);
        }
        try{
            $post = PostVotes::where([
                ['post_id','=', $request->postId], 
                ['user_id','=', $recived['id']]
            ])->first();
            
            if(empty($post)){
                $post = new PostVotes();
                $post->post_id = $request->postId;
                $post->user_id = $recived['id'];
                $post->vote = $request->vote;
                $post->save();
            }else{
                $post->vote = $request->vote;
                $post->save();
            }
            return response()->json(['message' => 'success'], 200);
        }catch(Exception $e){
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }
}
