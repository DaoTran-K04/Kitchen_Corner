<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'post_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ: Like thuộc về 1 Review (Dù DB tên là post_id)
    public function review()
    {
        // belongsTo(Model Đích, 'tên_cột_khóa_ngoại', 'tên_cột_khóa_chính_bảng_kia')
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }
}