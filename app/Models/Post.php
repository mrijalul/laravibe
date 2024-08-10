<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $table    = 'posts';
    protected $fillable = ['title', 'body', 'slug', 'user_id', 'category_id', 'meta_title', 'meta_description', 'views'];
}
