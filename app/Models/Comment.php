<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'recipe_id',
        'parent_id',
        'content',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }
    public function likes()
    {
        return $this->hasMany(CommentLike::class, 'comment_id');
    }
    // app/Models/Comment.php
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->latest();
    }
}