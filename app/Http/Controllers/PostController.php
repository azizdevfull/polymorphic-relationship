<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostStoreRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\Image;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::all();

        return $posts;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostStoreRequest $request)
    {
        $post = Post::create(['name' => $request->name]);

        $image = new Image(['url' => $request->image_url]);
        $post->images()->save($image);

        return response()->json(['message' => 'Post created successfully', 'data' => new PostResource($post)], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json([
                'message' => 'Post not found',
            ], 404);
        }

        return response()->json([
            'post' => new PostResource($post),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostUpdateRequest $request, string $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'message' => 'Post not found',
            ], 404);
        }

        $post->update(['name' => $request->name]);

        if ($request->has('image_url')) {
            $image = $post->images()->firstOrNew(['imageable_id' => $post->id, 'imageable_type' => Post::class]);
            $image->url = $request->image_url;
            $image->save();
        }

        return response()->json(['message' => 'Post updated successfully', 'data' => new PostResource($post)]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'message' => 'Post not found',
            ], 404);
        }
        $post->images()->delete();
        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }
}
