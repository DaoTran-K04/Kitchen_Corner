<?php

namespace App\Services\AI;

use App\Models\User;
use App\Models\ViewHistory;
use App\Models\SearchHistory;

class UserTasteProfileService
{
    /**
     * Builds a simplified taste profile for a user based on interactions.
     * Removes all PII to safely send to AI.
     */
    public function getProfile(?User $user): array
    {
        if (!$user) {
            return [];
        }

        $profile = [
            'recent_searches' => [],
            'favorite_ingredients' => [],
            'diet_tendency' => 'chưa rõ'
        ];

        // 1. Recent Searches
        $recentSearches = SearchHistory::where('user_id', $user->id)
            ->orderBy('searched_at', 'desc')
            ->limit(5)
            ->pluck('keyword')
            ->toArray();
        $profile['recent_searches'] = $recentSearches;

        // 2. Favorite Cuisines/Categories from ViewHistory & Likes
        // (Assuming a simple aggregation here. In a real scenario, group by category_id)
        $likedRecipes = $user->likes()->with('recipe.category', 'recipe.ingredients')->get();
        $cats = [];
        $ingredients = [];
        
        foreach ($likedRecipes as $like) {
            if ($like->recipe && $like->recipe->category) {
                $cats[] = $like->recipe->category->name;
            }
            if ($like->recipe) {
                foreach ($like->recipe->ingredients as $ing) {
                    $ingredients[] = $ing->name;
                }
            }
        }

        // Get top 3 categories
        if (!empty($cats)) {
            $catCounts = array_count_values($cats);
            arsort($catCounts);
            $profile['favorite_cuisines'] = array_slice(array_keys($catCounts), 0, 3);
        }

        // Get top 5 ingredients
        if (!empty($ingredients)) {
            $ingCounts = array_count_values($ingredients);
            arsort($ingCounts);
            $profile['favorite_ingredients'] = array_slice(array_keys($ingCounts), 0, 5);
        }

        // 3. Diet Tendency logic (heuristics)
        $lowCalorieCount = 0;
        foreach ($likedRecipes as $like) {
            if ($like->recipe && $like->recipe->total_calories < 400) {
                $lowCalorieCount++;
            }
        }

        if ($likedRecipes->count() > 0 && ($lowCalorieCount / $likedRecipes->count()) > 0.5) {
            $profile['diet_tendency'] = 'thích món healthy, ít calo';
        } else {
            $profile['diet_tendency'] = 'thích ăn đa dạng, ngon miệng';
        }

        return $profile;
    }
}
