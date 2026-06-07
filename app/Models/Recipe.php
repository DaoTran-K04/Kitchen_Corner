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

    public function getThumbnailAttribute()
    {
        $value = $this->image;
        
        // Nếu không có ảnh, lấy ảnh ngẫu nhiên từ danh sách (đảm bảo không trùng lặp toàn bộ)
        if (!$value) {
            $photoIds = [
                '1490645935967-10de6ba17061', // salad
                '1473093295043-cdd812d0e601', // pasta
                '1495521821757-a1efb6729352', // fruits
                '1504630083234-14187a9df0f5', // steak
                '1476224203421-9ce22365c465', // bread
                '1484723091782-4def3715a3ba', // dessert
                '1455619452474-d2be8b1e70cd'  // curry/soup
            ];
            $photoId = $photoIds[$this->id % count($photoIds)];
            return "https://images.unsplash.com/{$photoId}?q=80&w=1000&auto=format&fit=crop";
        }

        if (str_starts_with($value, 'http') || str_starts_with($value, 'data:')) {
            // Thêm sig để tránh cache trùng ảnh nếu link giống nhau
            if (str_contains($value, 'unsplash.com')) {
                return $value . (str_contains($value, '?') ? '&' : '?') . "sig={$this->id}";
            }
            return $value;
        }

        // Định tuyến toàn bộ ảnh sang Supabase
        $projectRef = 'uxkrgbnmvnzunxgkaunt';
        $supabaseBase = "https://{$projectRef}.supabase.co/storage/v1/object/public/";

        // Chuyển "assets/recipes/..." thành "recipes/..."
        if (str_starts_with($value, 'assets/')) {
            $value = str_replace('assets/', '', $value);
        }

        // Đảm bảo ảnh lưu cục bộ trước đây cũng nằm trong bucket recipes/
        if (!str_starts_with($value, 'recipes/')) {
            $value = 'recipes/' . $value;
        }

        return $supabaseBase . ltrim($value, '/');
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
