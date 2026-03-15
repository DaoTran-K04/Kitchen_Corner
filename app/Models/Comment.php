<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'post_id', // Cột này đang lưu ID của Sách (hoặc Post)
        'content',
        'rating',
        // ... các cột khác
    ];

    // Quan hệ với User (người bình luận)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // --- THÊM ĐOẠN NÀY ĐỂ SỬA LỖI ---
    public function book()
    {
        // Khai báo: Comment thuộc về Book thông qua cột 'post_id'
        return $this->belongsTo(Book::class, 'post_id');
    }
    
    // Nếu bạn cũng muốn lấy comment theo Post, có thể giữ thêm cái này (tuỳ chọn)
    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
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