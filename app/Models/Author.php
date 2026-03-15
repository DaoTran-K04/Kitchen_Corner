<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Author extends Model
{
    use HasFactory, SoftDeletes;

    // Timestamps: chỉ cần updated_at cho deleted_at hoạt động
    public $timestamps = true;

    protected $fillable = [
        'name',
        'slug',
        'photo',
        'bio',
        'birth_year',
        'death_year',
        'nationality',
    ];

    /**
     * Lấy tất cả sách của tác giả này dựa trên cột `author_name` (cũ)
     * Giữ để tương thích các nơi cũ còn dùng kiểu lưu tên tác giả trong `books.author_name`.
     */
    public function books()
    {
        return $this->hasMany(Book::class, 'author_name', 'name');
    }

    /**
     * Liên kết nhiều-nhiều (sử dụng bảng pivot `author_book`) nếu sách được gắn nhiều tác giả
     */
    public function booksPivot()
    {
        return $this->belongsToMany(Book::class, 'author_book', 'author_id', 'book_id');
    }

    /**
     * Đếm số lượng sách của tác giả (ưu tiên pivot nếu có)
     */
    public function getBooksCountAttribute()
    {
        // Nếu có bản ghi pivot thì dùng pivot, nếu không quay về lượt đếm cũ
        return $this->booksPivot()->count() ?: $this->books()->count();
    }

    /**
     * Tạo slug tự động từ tên
     */
    public static function generateSlug($name)
    {
        return \Str::slug($name);
    }
}
