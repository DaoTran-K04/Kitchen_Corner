<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Recipe extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'description',
        'cooking_time',
        'difficulty',
        'total_calories',
        'total_protein',
        'total_carbs',
        'total_fat',
        'image',
        'view_count',
        'is_featured',
        'is_premium',
        'status',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(RecipeStep::class);
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'recipe_ingredients')
            ->withPivot('quantity', 'notes')
            ->withTimestamps();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class, 'collection_recipes')
            ->withTimestamps();
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function getThumbnailAttribute()
    {
        $value = $this->image;
        
        // Nếu không có ảnh, lấy ảnh ngẫu nhiên từ Unsplash theo ID (đảm bảo không trùng lặp)
        if (!$value) {
            $keywords = ['food', 'recipe', 'cooking', 'delicious', 'meal'];
            $randomKeyword = $keywords[$this->id % count($keywords)];
            return "https://images.unsplash.com/photo-1490645935967-10de6ba17061?q=80&w=1000&auto=format&fit=crop&sig={$this->id}";
        }

        if (str_starts_with($value, 'http')) {
            // Thêm sig để tránh cache trùng ảnh nếu link giống nhau
            if (str_contains($value, 'unsplash.com')) {
                return $value . (str_contains($value, '?') ? '&' : '?') . "sig={$this->id}";
            }
            return $value;
        }

        return asset('storage/' . $value);
    }


    public function getDescriptionAttribute($value)
    {
        if (!$value) {
            return $value;
        }
        
        return preg_replace('/\[TheMealDB\]\s*\[MealID:\d+\]\s*/i', '', $value);
    }
}
