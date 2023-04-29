<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function list()
    {
        $posts = PostResource::collection(Post::all());

        return response()->json([
            'data' => $posts,
        ]);
    }

    public function toggleReaction(Request $request)
    {

        $input = $request->all();
        $validator = Validator::make($input, [
            'post_id' => 'required|int|exists:posts,id',
            'like' => 'required|boolean',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 500,
                'message' => $validator->errors(),
            ]);   
        }

        $post = Post::find($request->post_id);

        if (! $post) {
            return response()->json([
                'status' => 404,
                'message' => 'model not found',
            ]);
        }

        if ($post->user_id == auth()->id()) {
            return response()->json([
                'status' => 500,
                'message' => 'You cannot like your post',
            ]);
        }

        $like = Like::where('post_id', $request->post_id)->where('user_id', auth()->id())->first();

        //if like is already exist
        if ($like && $like->post_id == $request->post_id) {

            if(! $request->like){
                $like->delete();

                return response()->json([
                    'status' => 200,
                    'message' => 'You unlike this post successfully',
                ]);

            }

            return response()->json([
                'status' => 500,
                'message' => 'You already liked this post',
            ]);

        } 

        //create new like
        Like::create([
            'post_id' => $request->post_id,
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'You like this post successfully',
        ]);

    }
}
