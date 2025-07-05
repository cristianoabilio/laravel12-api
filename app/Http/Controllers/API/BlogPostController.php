<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BlogPostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = BlogPost::get();

        return response()->json([
            'status' => 'success',
            'data' => $posts,
            'count' => count($posts)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
            'category_id' => 'required|numeric',
            'title' => 'required',
            'content' => 'required',
            'thumbnail' => 'nullable|image|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => $validator->errors()
            ], 400);
        }

        // check if the user is the same of the one logged in
        $loggedUser = Auth::user();

        if ($loggedUser->id != $request->user_id) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Unauthorized access.'
            ], 400);
        }

        // check if the category id is valid
        $category = BlogCategory::find($request->category_id);

        if (! $category) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Category not found.'
            ], 404);
        }

        $imagePath = null;
        if ($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()) {
            $file = $request->file('thumbnail');

            // generate an unique file name
            $fileName = time().'_'.$file->getClientOriginalName();

            // move file into storage
            $file->move(public_path('storage/posts'), $fileName);

            // image path into database
            $imagePath = 'storage/posts/'.$fileName;
        }

        $data['title'] = $request->title;
        $data['slug'] = Str::slug($request->title);
        $data['user_id'] = $request->user_id;
        $data['category_id'] = $request->category_id;
        $data['content'] = $request->content;
        $data['thumbnail'] = $fileName ?? null;

        if (Auth::user()->role == 'admin') {
            $data['except'] = 'published';
            $data['published_at'] = date('Y-m-d H:i:s');
        }

        BlogPost::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Post created successfully.'
        ], 201);
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
        $post = BlogPost::find($id);

        if (! $post) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Post not found.'
            ], 404);
        }
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
            'category_id' => 'required|numeric',
            'title' => 'required',
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => $validator->errors()
            ], 400);
        }

        // check if the user is the same of the one logged in
        $loggedUser = Auth::user();

        if ($loggedUser->id != $request->user_id) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Unauthorized access.'
            ], 400);
        }

        // check if the category id is valid
        $category = BlogCategory::find($request->category_id);

        if (! $category) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Category not found.'
            ], 404);
        }

        $post->title = $request->title;
        $post->slug = Str::slug($request->title);
        $post->category_id = $request->category_id;
        $post->user_id = $request->user_id;
        $post->content = $request->content;
        // $post->excerpt = $request->excerpt;
        $post->status = $request->status;
        $post->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Blog posts updated successfully.'
        ], 201);

    }

    public function blogPostImage(Request $request, int $id)
    {
        $post = BlogPost::find($id);

        if (! $post) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Post not found.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
            'thumbnail' => 'nullable|image|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => $validator->errors()
            ], 400);
        }

        $loggedUser = Auth::user();

        if ($loggedUser->id != $request->user_id) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Unauthorized access.'
            ], 400);
        }

        $imagePath = null;
        if ($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()) {
            $file = $request->file('thumbnail');

            // generate an unique file name
            $fileName = time().'_'.$file->getClientOriginalName();

            // move file into storage
            $file->move(public_path('storage/posts'), $fileName);

            // image path into database
            $imagePath = 'storage/posts/'.$fileName;
        }

        $post->thumbnail = $imagePath ?? $post->thumbnail;
        $post->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Image updated successfully.'
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = BlogPost::find($id);

        if (! $post) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Blog Post not found.'
            ], 404);
        }

        $post->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Post deleted successfully.'
        ], 201);
    }
}
