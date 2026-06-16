<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//phi chuẩn hóa từ khóa trong model
class SupabaseRecipe extends Model
{
    protected $connection = 'supabase';
    protected $table = 'supabase_recipes';

    protected $fillable = [
        'meal_id',
        'name',
        'category',
        'area',
        'instructions',
        'image_url',
        'youtube_url',
        'ingredients_json',
    ];

    protected $casts = [
        'ingredients_json' => 'array',
    ];
}
