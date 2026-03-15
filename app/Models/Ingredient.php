<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ingredient extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'unit',
        'calories_per_unit',
        'protein_per_unit',
        'carbs_per_unit',
        'fat_per_unit',
        'icon',
    ];

    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class, 'recipe_ingredients')
            ->withPivot('quantity', 'notes')
            ->withTimestamps();
    }
}
