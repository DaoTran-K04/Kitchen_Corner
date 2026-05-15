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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getThumbnailAttribute($value)
    {
        if (!$value) {
            return "https://images.unsplash.com/photo-1512621776951-a57141f2eefd?q=80&w=1000&auto=format&fit=crop&sig=art-{$this->id}";
        }

        if (str_starts_with($value, 'http')) {
            return $value;
        }

        return asset('storage/' . $value);
    }

}