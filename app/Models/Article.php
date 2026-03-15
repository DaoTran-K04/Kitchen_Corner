<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'thumbnail',
        'tag',
        'excerpt',
        'content',
        'is_featured',
        'is_active',
        'user_id'
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Quan hệ: Bài viết thuộc về 1 người dùng (Admin/Tác giả)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}