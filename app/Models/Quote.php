<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'author',
        'source',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Lấy 1 quote ngẫu nhiên đang hoạt động
     */
    public static function random(): ?self
    {
        return static::where('is_active', true)->inRandomOrder()->first();
    }
}
