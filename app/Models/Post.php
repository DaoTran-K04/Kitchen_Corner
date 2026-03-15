<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'book_id',
        'title',
        'slug',
        'thumbnail',
        'excerpt',
        'content',
        'rating',
        'status',       // draft, pending, published, rejected, hidden, pending_delete
        'published_at',
        'view_count',
    ];

    protected $casts = [
        'rating' => 'float',
        'published_at' => 'datetime',
        'view_count' => 'integer',
    ];

    // Quan hệ
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    // Users đã lưu bài viết này
    public function savedByUsers()
    {
        return $this->belongsToMany(User::class, 'saved_posts')
            ->withTimestamps();
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function reports()
    {
        return $this->hasMany(PostReport::class);
    }

    public function scopeMostLiked($query)
    {
        return $query->withCount('likes')->orderByDesc('likes_count');
    }
}
