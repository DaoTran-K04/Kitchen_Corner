<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
//phi chuẩn hóa từ khóa trong model
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
        'source',
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

    public function getImageAttribute($value)
    {
        return $value;
    }

    public function getThumbnailAttribute()
    {
        return $this->image;
    }


    public function getDescriptionAttribute($value)
    {
        if (!$value) {
            return $value;
        }
        
        $value = preg_replace('/\[TheMealDB\]\s*\[MealID:\d+\]\s*/i', '', $value);
        $value = str_ireplace([
            'mang hương vị đặc trưng TheMealDB.', 
            'mang hương vị đặc trưng TheMealDB', 
            'TheMealDB'
        ], '', $value);

        return trim($value);
    }
}
