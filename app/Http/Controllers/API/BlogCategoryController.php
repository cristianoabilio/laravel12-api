<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryValidator;
use App\Http\Requests\UpdateCategoryValidator;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class BlogCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = BlogCategory::get();

        return response()->json([
            'status' => 'success',
            'count' => count($categories),
            'data' => $categories,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryValidator $request)
    {
        $data['name'] = $request->name;
        $data['slug'] = Str::slug($request->name);

        BlogCategory::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Category created successfully.'
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
    public function update(UpdateCategoryValidator $request, string $id)
    {
        $category = BlogCategory::find($id);

        if (! $category) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Category not found.'
            ], 404);
        }

        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Category updated successfully.'
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = BlogCategory::find($id);

        if (! $category) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Category not found.'
            ], 404);
        }

        BlogCategory::destroy($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Category deleted successfully.'
        ], 201);
    }
}
