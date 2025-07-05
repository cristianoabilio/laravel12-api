<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'content',
        'except',
        'thumbnail',
        'status',
        'published_at'
    ];
}
