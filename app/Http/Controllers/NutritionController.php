<?php

namespace App\Http\Controllers;

use App\Services\SpoonacularService;
use Illuminate\Http\Request;

class NutritionController extends Controller
{
    protected $spoonacularService;

    public function __construct(SpoonacularService $spoonacularService)
    {
        $this->spoonacularService = $spoonacularService;
    }

    public function calculate(Request $request)
    {
        $input = $request->input('query'); // e.g., "200g thịt bò"
        
        // Simple regex to parse quantity and name
        // e.g. "200g thịt bò" -> quantity = 200, unit = 'g', name = 'thịt bò'
        if (preg_match('/(\d+)\s*(g|ml|kg)?\s+(.+)/i', $input, $matches)) {
            $amount = (float) $matches[1];
            $unit = strtolower($matches[2] ?? 'g');
            $ingredientName = trim($matches[3]);

            if ($unit === 'kg') {
                $amount *= 1000;
            }

            $ingredientInfo = $this->spoonacularService->getNutritionInfo($ingredientName);

            if ($ingredientInfo) {
                // Info in DB is based on 100g
                $multiplier = $amount / 100;

                $totalCalories = round($ingredientInfo->calories * $multiplier);
                $totalProtein = round($ingredientInfo->protein * $multiplier, 1);
                $totalCarbs = round($ingredientInfo->carbs * $multiplier, 1);
                $totalFat = round($ingredientInfo->fat * $multiplier, 1);

                return response()->json([
                    'success' => true,
                    'data' => [
                        'ingredient' => $ingredientName,
                        'amount' => $amount,
                        'unit' => 'g',
                        'calories' => $totalCalories,
                        'protein' => $totalProtein,
                        'carbs' => $totalCarbs,
                        'fat' => $totalFat,
                        'image_url' => $ingredientInfo->image_url
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => "Không tìm thấy thông tin dinh dưỡng cho '{$ingredientName}'."
            ], 404);
        }

        return response()->json([
            'success' => false,
            'message' => 'Vui lòng nhập đúng định dạng (VD: 200g thịt bò).'
        ], 400);
    }
}
