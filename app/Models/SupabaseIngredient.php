<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//phi chuẩn hóa từ khóa trong model
class SupabaseIngredient extends Model
{
    protected $connection = 'supabase';

    protected $fillable = [
        'vietnamese_name',
        'english_name',
        'calories',
        'protein',
        'carbs',
        'fat',
        'image_url',
    ];
}
