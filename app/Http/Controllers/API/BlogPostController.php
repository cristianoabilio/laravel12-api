<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBlogPostValidator;
use App\Http\Requests\UpdateBlogPostImageValidator;
use App\Http\Requests\UpdateBlogPostValidator;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Seo;
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
        $posts = BlogPost::with('seo_data')->get();

        return response()->json([
            'status' => 'success',
            'data' => $posts,
            'count' => count($posts)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBlogPostValidator $request)
    {
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
        }

        if (Auth::user()->role == 'admin' || Auth::user()->role == 'author') {
            $data['published_at'] = date('Y-m-d H:i:s');
        }
        $blogPost = BlogPost::create($data);
        $postId = $blogPost->id;

        $seoData = [
            'post_id' => $postId,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords
        ];

        Seo::create($seoData);

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
    public function update(UpdateBlogPostValidator $request, string $id)
    {
        $post = BlogPost::find($id);

        if (! $post) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Post not found.'
            ], 404);
        }

        // check if the user is the same of the one logged in
        $loggedUser = Auth::user();

        // check if the category id is valid
        $category = BlogCategory::find($request->category_id);

        if (! $category) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Category not found.'
            ], 404);
        }

        // check additional conditional to restrict authorized edit
        if ($loggedUser->id === $post->user_id || Auth::user()->role == 'admin') {
            $post->title = $request->title;
            $post->slug = Str::slug($request->title);
            $post->category_id = $request->category_id;
            $post->user_id = $request->user_id;
            $post->content = $request->content;
            // $post->excerpt = $request->excerpt;
            $post->status = $request->status;
            $post->save();

            $seoData = Seo::wherePostId($post->id)->first();

            $seoData->meta_title = $request->meta_title;
            $seoData->meta_description = $request->meta_description;
            $seoData->meta_keywords = $request->meta_keywords;
            $seoData->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Blog posts updated successfully.'
            ], 201);
        }
        return response()->json([
            'status' => 'fail',
            'message' => 'You are not allowed to perform this task.'
        ], 403);
    }

    public function blogPostImage(UpdateBlogPostImageValidator $request, int $id)
    {
        $post = BlogPost::find($id);

        if (! $post) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Post not found.'
            ], 404);
        }

        $loggedUser = Auth::user();

        $imagePath = null;

        if ($loggedUser->id === $post->user_id || Auth::user()->role == 'admin') {
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
        return response()->json([
            'status' => 'fail',
            'message' => 'You are not allowed to perform this task.'
        ], 403);
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

        // check if the user is the same of the one logged in
        $loggedUser = Auth::user();

        if ($loggedUser->id === $post->user_id || Auth::user()->role == 'admin') {
            $post->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Post deleted successfully.'
            ], 201);
        }

        return response()->json([
            'status' => 'fail',
            'message' => 'You are not allowed to perform this task.'
        ], 403);
    }
}
