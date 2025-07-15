<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeCommentStatusValidator;
use App\Http\Requests\StoreCommentValidator;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $comments = Comment::get();

        return response()->json([
            'status' => 'success',
            'count' => count($comments),
            'data' => $comments
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentValidator $request)
    {
        $data = [
            'post_id' => $request->post_id,
            'parent_id' => $request->parent_id ?? null,
            'user_id' => Auth::user()->id,
            'content' => $request->content
        ];

        Comment::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Comment created and waiting for admin approval.'
        ], 201);
    }

    public function changeStatus(ChangeCommentStatusValidator $request, $commentId)
    {
        $comment = Comment::find($request->comment_id);

        if (! $comment) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Comment not found.'
            ], 404);
        }

        $comment->status = $request->status;
        $comment->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Comment updated successfully.'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $comments = Comment::where('post_id', $id)
            ->where('status', 'approved')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $comments,
            'count' => count($comments)
        ], 200);
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
        $comment = Comment::find($id);

        if (! $comment) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Comment not found.'
            ], 404);
        }

        Comment::destroy($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Comment deleted successfully.'
        ], 201);
    }
}
