<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestApiController extends Controller
{
    public function test(): string
    {
        return response()->json([
            'success' => true,
            'message' => 'Your first API route is working!'
        ]);
    }
}
