<?php



namespace App\Http\Controllers;



use App\Models\Comment;

use Illuminate\Support\Facades\File;

use Illuminate\Http\Request;

use App\Models\Post;

use App\Models\Notification;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;



class PostController extends Controller
{
    function deletePost(Request $request){
        try{
            $recived = auth()->userOrFail();
            try{
                $post = Post::where('id', '=', $request->postID)->first();
                $image_path = "../../backend/public/storage/posts/" . $post->filename;
                $comments = Comment::where('post_id', '=', $request->postID)->delete();
                if(File::exists($image_path)) {
                    File::delete($image_path);
                }
                $post->delete();
                return response()->json([],200);
            }catch(Exception $e){
                return response()->json(['message' => $e]);
            }
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            error_log($e);
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }



    function deleteUserPost(Request $request){
        try{
            $recived = auth()->userOrFail();
            try{
                $post = Post::where('id', '=', $request->postID)->first();
                $image_path = "../../backend/public/storage/posts/" . $post->filename;
                error_log($image_path);
                $comments = Comment::where('post_id', '=', $request->postID)->delete();
                if(File::exists($image_path)) {
                    File::delete($image_path);
                }
                $post->delete();
                $notification = new Notification();
                $notification->id_user = $request->userID;
                $notification->from = $recived['id'];
                $notification->message = "Un administrator ti-a sters postarea.
                 \nMotivul: ". $request->reason;
                $notification->read = 0;
                $notification->type = -122;
                $notification->firstName = '';
                $notification->lastName = '';
                $notification->save();
                return response()->json([],200);
            }catch(Exception $e){
                return response()->json(['message' => $e]);
            }
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            error_log($e);
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }



    function modifyDescriptionPost(Request $request){
        try{
            $recived = auth()->userOrFail();
            try{
                $post = Post::where('id', '=', $request->postID)->first();
                $post->description = $request->description;
                $post->save();
                return response()->json([],200);
            }catch(Exception $e){
                return response()->json(['message' => $e]);
            }
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            error_log($e);
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }



    function getPost(Request $request){
        try{
            $recived = auth()->userOrFail();
            try{
                $result = DB::table('posts')->leftJoin('post_votes', function($join)
                {
                    $recived = auth()->userOrFail();
                    $join->on('posts.id', '=', 'post_votes.post_id')->where('post_votes.user_id', '=', $recived['id']);
                })
                    ->join('users', function($join)
                    {
                        $recived = auth()->userOrFail();
                        $join->on('users.id', '=', 'posts.user_id');
                    })
                    ->select('posts.id', 'posts.user_id', 'posts.description', 'vote', 'posts.votes', 'posts.link',
                        'posts.filename', 'users.lastName', 'users.firstName', 'users.link_profile')
                    ->where('posts.id', '=', $request->postID)->first();
                if($result != NULL) return response()->json(['post' => $result,], 200, [], JSON_NUMERIC_CHECK);
                else return response()->json(['post' => "INVALID_POSTING"], 200);
            }catch(Exception $e) {
                return response()->json(['message' => $e]);
            }
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            error_log($e);
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }



    function addPost(Request $request){
        $host_backend = env('HOST_BACKEND');
        try{
            $recived = auth()->userOrFail();
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            error_log($e);
            return response()->json(['error' => $e->getMessage()], 401);
        }
        try{
            $lastPost = Post::orderBy('id', 'desc')->first();
            if($lastPost != null){
                $idCount = $lastPost->id;
                $idCount++;
                error_log($idCount);
                $imageName = $request->image->getClientOriginalName(). "_".$idCount . '.jpg';
            }
            else $imageName = $request->image->getClientOriginalName(). "_0". '.jpg';
            $request->image->move('../../backend/public/storage/posts', $imageName);
            $link = "/storage/posts/" . $imageName;
            $post = new Post();
            $post->has_photo = true;
            $post->filename = $imageName;
            $post->user_id = $request->user_id;
            $post->description = $request->description;
            $post->link = $link;
            $post->save();
            return response()->json(['message'=> 'success']);
        }catch(Exception $e){

            return response()->json(['error'=>$e->getMessage()]);

        }

    }





    function fetchUserPosts(Request $request){
        try{
            $recived = auth()->userOrFail();
            try{
                $result = DB::table('posts')->leftJoin('post_votes', function($join)
                {
                    $recived = auth()->userOrFail();
                    $join->on('posts.id', '=', 'post_votes.post_id')->where('post_votes.user_id', '=', $recived['id']);
                })
                ->select('posts.id', 'posts.user_id', 'posts.description', 'vote', 'posts.votes', 'posts.link'
                    ,  'posts.filename')
                ->where('posts.user_id', '=', $request->user_id)
                ->orderBy('posts.id', 'desc')->get();
                return response()->json(['posts' => $result], 200, [], JSON_NUMERIC_CHECK);
            }catch(Exception $e){
                return response()->json(['message' => $e]);
            }
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            error_log($e);
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }



    function fetchFallowPosts(Request $request){
        try{
            $recived = auth()->userOrFail();
            try{ // we have a problem sir
                $arrayRecived = json_decode($request->fallowList);
                $result = DB::table('posts')->leftJoin('post_votes', function($join)
                {
                    $recived = auth()->userOrFail();
                    $join->on('posts.id', '=', 'post_votes.post_id')->where('post_votes.user_id', '=', $recived['id']);
                })
                ->join('users', function($join)
                {
                    $recived = auth()->userOrFail();
                    $join->on('users.id', '=', 'posts.user_id');
                })
                ->select('posts.id', 'posts.user_id', 'posts.description', 'vote', 'posts.votes', 'posts.link',
                    'posts.filename', 'users.lastName', 'users.firstName', 'users.link_profile')
                ->whereIn('posts.user_id', $arrayRecived)->orWhere('posts.user_id', $recived['id'])
                ->orderBy('posts.created_at', 'desc')->get();
                return response()->json(['posts' => $result], 200, [], JSON_NUMERIC_CHECK);
            }catch(Exception $e){
                return response()->json(['message' => $e]);
            }
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            error_log($e);
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }



    function postVote(Request $request){
        try{
            $recived = auth()->userOrFail();
        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
            error_log($e);
            return response()->json(['error' => $e->getMessage()], 401);
        }
        try{
            $post = Post::where('id', $request->postId)->first();
            $post->votes += $request->vote;
            $post->save();
        }catch(Exception $e){
            return response()->json(['message' => $e]);
        }
    }
}

