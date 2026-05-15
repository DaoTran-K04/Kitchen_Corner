<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentLike extends Model
{
    use HasFactory;

    protected $table = 'comment_likes'; // Quan trọng: Khai báo đúng tên bảng

    protected $fillable = ['user_id', 'comment_id', 'is_like'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}