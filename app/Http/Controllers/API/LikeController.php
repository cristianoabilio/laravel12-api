<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReactionValidator;
use App\Models\Like;
use Illuminate\Http\Request;
class LikeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * like and dislike reaction
     */
    public function react(StoreReactionValidator $request)
    {
        $userId = auth()->id();
        $postId = $request->post_id;
        $status = $request->status;

        $like = Like::where('user_id', $userId)->where('post_id', $postId)->first();

        if ($like) {
            if ($like->status == $status) {
                // same reaction than remove reaction
                $like->delete();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Reaction removed.'
                ], 201);
            }

            $like->status = $status;
            $like->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Reaction updated.'
            ], 201);
        }

        Like::create([
            'user_id' => $userId,
            'post_id' => $postId,
            'status' => $status
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Reaction added.'
        ], 201);

    }

    public function reactions(Request $request, $postId)
    {
        $likesCount = Like::where('post_id', $postId)->where('status', 1)->count();
        $dislikesCount = Like::where('post_id', $postId)->where('status', 2)->count();

        return response()->json([
            'status' => 'success',
            'likes' => $likesCount,
            'dislikes' => $dislikesCount,
            'post_id' => $postId
        ], 200);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
