<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;

class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::OrderBy("id", "DESC")->paginate(10);

        $outPut = [
            "message" => "posts",
            "results" => $posts
        ];

        return response()->json($posts, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $post = Post::create($input);

        return response()->json($post, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::find($id);

        if (!$post) {
            abort(404);
        }

        return response()->json($post, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();

        $post = Post::find($id);

        if (!$post) {
            abort(404);
        }

        $post->fill($input);
        $post->save();

        return response()->json($post, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);

        if (!$post) {
            abort(404);
        }

        $post->delete();
        $message = ['message' => 'deleted successfully', 'post_id' => $id];

        return response()->json($message, 200);
    }

    /**
     * Display comments for the post.
     *
     * @param  int  $postId
     * @return \Illuminate\Http\Response
     */
    public function comments($postId)
    {
        $post = Post::find($postId);

        if (!$post) {
            abort(404);
        }

        return response()->json($post->comments()->with('user:id,name,email')->get(), 200);
    }

    /**
     * Store a comment for the post.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $postId
     * @return \Illuminate\Http\Response
     */
    public function commentStore(Request $request, $postId)
    {
        $post = Post::find($postId);

        if (!$post) {
            abort(404);
        }

        $comment = $post->comments()->create($request->all());

        return response()->json($comment, 201);
    }

    /**
     * Remove a comment for the post.
     *
     * @param  int  $postId
     * @param  int  $commentId
     * @return \Illuminate\Http\Response
     */
    public function commentDestroy($postId, $commentId)
    {
        $comment = Comment::where('post_id', $postId)->where('id', $commentId)->first();

        if (!$comment) {
            abort(404);
        }

        $comment->delete();

        return response()->json(['message' => 'deleted successfully', 'comment_id' => $commentId], 200);
    }
}
