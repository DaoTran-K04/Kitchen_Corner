<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class IngredientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Ingredient::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $ingredients = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.ingredients.index', compact('ingredients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.ingredients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required', 'string', 'max:50',
                \Illuminate\Validation\Rule::unique('ingredients', 'name')->whereNull('deleted_at')
            ],
            'unit' => 'required|string|max:20',
            'calories_per_unit' => 'nullable|integer|min:0',
            'protein_per_unit' => 'nullable|integer|min:0',
            'carbs_per_unit' => 'nullable|integer|min:0',
            'fat_per_unit' => 'nullable|integer|min:0',
        ]);

        $slug = Str::slug($request->name);
        
        // Ensure slug uniqueness
        $originalSlug = $slug;
        $counter = 1;
        while (Ingredient::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        Ingredient::create([
            'name' => $request->name,
            'slug' => $slug,
            'unit' => $request->unit,
            'calories_per_unit' => $request->calories_per_unit ?? 0,
            'protein_per_unit' => $request->protein_per_unit ?? 0,
            'carbs_per_unit' => $request->carbs_per_unit ?? 0,
            'fat_per_unit' => $request->fat_per_unit ?? 0,
        ]);

        return redirect()->route('admin.ingredients.index')
            ->with('success', 'Đã thêm nguyên liệu mới thành công!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ingredient $ingredient)
    {
        return view('admin.ingredients.edit', compact('ingredient'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ingredient $ingredient)
    {
        $request->validate([
            'name' => [
                'required', 'string', 'max:50',
                \Illuminate\Validation\Rule::unique('ingredients', 'name')->ignore($ingredient->id)->whereNull('deleted_at')
            ],
            'unit' => 'required|string|max:20',
            'calories_per_unit' => 'nullable|integer|min:0',
            'protein_per_unit' => 'nullable|integer|min:0',
            'carbs_per_unit' => 'nullable|integer|min:0',
            'fat_per_unit' => 'nullable|integer|min:0',
        ]);

        $slug = Str::slug($request->name);
        if ($slug !== $ingredient->slug) {
            $originalSlug = $slug;
            $counter = 1;
            while (Ingredient::where('slug', $slug)->where('id', '!=', $ingredient->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $ingredient->update([
            'name' => $request->name,
            'slug' => $slug,
            'unit' => $request->unit,
            'calories_per_unit' => $request->calories_per_unit ?? 0,
            'protein_per_unit' => $request->protein_per_unit ?? 0,
            'carbs_per_unit' => $request->carbs_per_unit ?? 0,
            'fat_per_unit' => $request->fat_per_unit ?? 0,
        ]);

        return redirect()->route('admin.ingredients.index')
            ->with('success', 'Đã cập nhật nguyên liệu thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ingredient $ingredient)
    {
        $ingredient->delete();
        
        return redirect()->route('admin.ingredients.index')
            ->with('success', 'Đã xóa nguyên liệu thành công!');
    }
}
