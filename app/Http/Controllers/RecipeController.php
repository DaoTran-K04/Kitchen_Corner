<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Recipe;
use App\Models\RecipeStep;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Collection;

class RecipeController extends Controller
{
    // =========================================================
    // DANH SÁCH CÔNG THỨC (Public)
    // =========================================================
    public function index(Request $request)
    {
        $query = Recipe::with(['user', 'category'])
            ->where('status', 'published');

        // Lọc theo danh mục
        if ($request->filled('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }

        // Lọc theo độ khó
        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        // Lọc theo thời gian nấu
        if ($request->filled('cooking_time')) {
            match ($request->cooking_time) {
                'quick'  => $query->where('cooking_time', '<=', 20),
                'medium' => $query->whereBetween('cooking_time', [21, 60]),
                'long'   => $query->where('cooking_time', '>', 60),
                default  => null,
            };
        }

        // Sắp xếp
        match ($request->get('sort', 'latest')) {
            'popular' => $query->orderByDesc('view_count'),
            'top'     => $query->withCount('likes')->orderByDesc('likes_count'),
            default   => $query->latest(),
        };

        $recipes    = $query->paginate(12)->withQueryString();
        $categories = \Illuminate\Support\Facades\Cache::remember('all_categories_list', 300, function () {
            return Category::withCount('recipes')->orderBy('name')->get();
        });

        return view('recipes.index', compact('recipes', 'categories'));
    }

    // =========================================================
    // CHI TIẾT CÔNG THỨC (Public)
    // =========================================================
    public function show(string $slug)
    {
        // Làm sạch slug: thay thế khoảng trắng bằng dấu gạch ngang
        $cleanSlug = str_replace([' ', '%20'], '-', $slug);
        
        $recipe = Recipe::with([
            'user',
            'category',
            'steps',
            'ingredients',
            'comments' => fn($q) => $q->whereNull('parent_id')
                ->with(['user', 'likes', 'replies.user', 'replies.likes'])
                ->latest(),
        ])
            ->where(function($q) use ($slug, $cleanSlug) {
                $q->where('slug', $slug)
                  ->orWhere('slug', $cleanSlug)
                  ->orWhere('id', $slug);
            })
            ->where(function($q) {
                $q->where('status', 'published')
                  ->orWhere('user_id', Auth::id())
                  ->orWhere(function($sq) {
                      if (Auth::check() && Auth::user()->isAdmin()) {
                          $sq->whereIn('status', ['pending', 'draft', 'published']);
                      } else {
                          $sq->where('id', 0); // No match
                      }
                  });
            })
            ->firstOrFail();

        // Tăng view count
        $recipe->increment('view_count');

        // Kiểm tra user đã like chưa
        $userLiked = false;
        $userSaved = false;
        if (Auth::check()) {
            $userLiked = Like::where('user_id', Auth::id())->where('recipe_id', $recipe->id)->exists();
            $userSaved = Collection::where('user_id', Auth::id())
                ->whereHas('recipes', fn($q) => $q->where('recipe_id', $recipe->id))
                ->exists();
        }

        // Công thức liên quan
        $relatedRecipes = Recipe::where('status', 'published')
            ->where('id', '!=', $recipe->id)
            ->where('category_id', $recipe->category_id)
            ->withCount('likes')
            ->orderByDesc('view_count')
            ->take(4)
            ->get();

        $likeCount    = \Illuminate\Support\Facades\Cache::remember("recipe_likes_{$recipe->id}", 60, fn() => Like::where('recipe_id', $recipe->id)->count());
        $commentCount = Comment::where('recipe_id', $recipe->id)->whereNull('parent_id')->count();

        return view('recipes.show', compact(
            'recipe',
            'relatedRecipes',
            'userLiked',
            'userSaved',
            'likeCount',
            'commentCount'
        ));
    }

    // =========================================================
    // TẠO CÔNG THỨC (Auth)
    // =========================================================
    public function create()
    {
        $categories  = Category::orderBy('name')->get();
        $ingredients = Ingredient::orderBy('name')->get();
        return view('recipes.create', compact('categories', 'ingredients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'category_id'  => 'nullable|exists:categories,id',
            'description'  => 'nullable|string|max:2000',
            'cooking_time' => 'nullable|integer|min:1|max:1440',
            'difficulty'   => 'required|in:easy,medium,hard',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'status'       => 'required|in:draft,published,pending',
            // Steps
            'steps'                => 'nullable|array',
            'steps.*.instruction'  => 'required|string|max:1000',
            'steps.*.image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            // Ingredients
            'ingredients'            => 'nullable|array',
            'ingredients.*.id'       => 'nullable|exists:ingredients,id',
            'ingredients.*.name'     => 'nullable|string|max:100',
            'ingredients.*.quantity' => 'nullable|numeric|min:0',
            'ingredients.*.notes'    => 'nullable|string|max:200',
        ]);

        DB::beginTransaction();
        try {
            // Upload ảnh bìa
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('recipes', 'public');
            }

            // Chống trùng lặp (tránh nhấn 2 lần hoặc back lại trang rồi đăng tiếp)
            $existing = Recipe::where('user_id', Auth::id())
                ->where('title', $validated['title'])
                ->where('created_at', '>', now()->subMinutes(10))
                ->first();

            if ($existing) {
                return redirect()->route('recipes.show', $existing->slug)
                    ->with('info', 'Bạn đã đăng công thức này rồi. Vui lòng kiểm tra lại!');
            }

            // Tạo Recipe
            $recipe = Recipe::create([
                'user_id'      => Auth::id(),
                'category_id'  => $validated['category_id'] ?? null,
                'title'        => $validated['title'],
                'slug'         => $this->generateUniqueSlug($validated['title']),
                'description'  => $validated['description'] ?? null,
                'cooking_time' => $validated['cooking_time'] ?? null,
                'difficulty'   => $validated['difficulty'],
                'image'        => $imagePath,
                'status'       => (Auth::user()->isAdmin()) ? $validated['status'] : ($validated['status'] === 'published' ? 'pending' : 'draft'),
            ]);

            // Lưu các bước (Steps)
            if (!empty($validated['steps'])) {
                foreach ($validated['steps'] as $index => $step) {
                    $stepImagePath = null;
                    if ($request->hasFile("steps.{$index}.image")) {
                        $stepImagePath = $request->file("steps.{$index}.image")->store('recipe-steps', 'public');
                    }
                    RecipeStep::create([
                        'recipe_id'   => $recipe->id,
                        'step_number' => $index + 1,
                        'description' => $step['instruction'],
                        'image'       => $stepImagePath,
                    ]);
                }
            }

            // Lưu nguyên liệu + Tính dinh dưỡng tự động
            $this->syncIngredientsAndCalculateNutrition($recipe, $request->input('ingredients', []));

            DB::commit();

            $msg = $recipe->status === 'pending' 
                ? 'Công thức đã được gửi và đang chờ phê duyệt! Đừng quên kiểm tra tiến độ thử thách của bạn nhé.' 
                : 'Công thức "' . $recipe->title . '" đã được đăng thành công! Hãy xem tiến độ thử thách của bạn có thay đổi không nhé.';

            return redirect()->route('recipes.show', $recipe->slug)
                ->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    // =========================================================
    // CHỈNH SỬA CÔNG THỨC (Auth + Owner)
    // =========================================================
    public function edit(int $id)
    {
        $recipe = Recipe::with(['steps', 'ingredients'])->findOrFail($id);

        // Chỉ chủ sở hữu hoặc admin mới được sửa
        if ($recipe->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Bạn không có quyền chỉnh sửa công thức này.');
        }

        $categories  = Category::orderBy('name')->get();
        $ingredients = Ingredient::orderBy('name')->get();

        return view('recipes.edit', compact('recipe', 'categories', 'ingredients'));
    }

    public function update(Request $request, int $id)
    {
        $recipe = Recipe::findOrFail($id);

        if ($recipe->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'category_id'  => 'nullable|exists:categories,id',
            'description'  => 'nullable|string|max:2000',
            'cooking_time' => 'nullable|integer|min:1|max:1440',
            'difficulty'   => 'required|in:easy,medium,hard',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'status'       => 'required|in:draft,published,pending',
            'steps'                => 'nullable|array',
            'steps.*.instruction'  => 'required|string|max:1000',
            'steps.*.image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'ingredients'            => 'nullable|array',
            'ingredients.*.id'       => 'nullable|exists:ingredients,id',
            'ingredients.*.quantity' => 'nullable|numeric|min:0',
            'ingredients.*.notes'    => 'nullable|string|max:200',
        ]);

        DB::beginTransaction();
        try {
            // Cập nhật ảnh bìa nếu có
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('recipes', 'public');
                $recipe->image = $imagePath;
            }

            $recipe->fill([
                'category_id'  => $validated['category_id'] ?? null,
                'title'        => $validated['title'],
                'slug'         => $recipe->title !== $validated['title']
                    ? $this->generateUniqueSlug($validated['title'], $recipe->id)
                    : $recipe->slug,
                'description'  => $validated['description'] ?? null,
                'cooking_time' => $validated['cooking_time'] ?? null,
                'difficulty'   => $validated['difficulty'],
                'status'       => (Auth::user()->isAdmin()) ? $validated['status'] : ($validated['status'] === 'published' ? 'pending' : 'draft'),
            ]);
            $recipe->save();

            // Cập nhật các bước
            $recipe->steps()->delete();
            if (!empty($validated['steps'])) {
                foreach ($validated['steps'] as $index => $step) {
                    $stepImagePath = null;
                    if ($request->hasFile("steps.{$index}.image")) {
                        $stepImagePath = $request->file("steps.{$index}.image")->store('recipe-steps', 'public');
                    }
                    RecipeStep::create([
                        'recipe_id'   => $recipe->id,
                        'step_number' => $index + 1,
                        'instruction' => $step['instruction'],
                        'image'       => $stepImagePath,
                    ]);
                }
            }

            // Cập nhật nguyên liệu + dinh dưỡng
            $this->syncIngredientsAndCalculateNutrition($recipe, $request->input('ingredients', []));

            DB::commit();

            return redirect()->route('recipes.show', $recipe->slug)
                ->with('success', 'Công thức đã được cập nhật!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    // =========================================================
    // XÓA CÔNG THỨC (Auth + Owner)
    // =========================================================
    public function destroy(int $id)
    {
        $recipe = Recipe::findOrFail($id);

        if ($recipe->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $recipe->delete();

        return redirect()->route('profile', Auth::id())
            ->with('success', 'Đã xóa công thức "' . $recipe->title . '".');
    }

    // =========================================================
    // TÌM KIẾM CÔNG THỨC
    // =========================================================
    public function search(Request $request)
    {
        $keyword    = $request->get('q', '');
        $categoryId = $request->get('category');

        $query = Recipe::with(['user', 'category'])
            ->where('status', 'published');

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%")
                  ->orWhereHas('category', function($cat) use ($keyword) {
                      $cat->where('name', 'like', "%{$keyword}%");
                  })
                  ->orWhereHas('ingredients', function($ing) use ($keyword) {
                      $ing->where('name', 'like', "%{$keyword}%");
                  });
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $recipes    = $query->orderByDesc('view_count')->paginate(12)->withQueryString();
        $categories = \Illuminate\Support\Facades\Cache::remember('all_categories_list', 300, fn() => Category::orderBy('name')->get());
        $total      = $query->count();

        return view('recipes.search', compact('recipes', 'categories', 'keyword', 'total'));
    }

    // =========================================================
    // SMART SEARCH – TÌM THEO NGUYÊN LIỆU CÓ SẴN
    // =========================================================
    public function smartSearch(Request $request)
    {
        $ingredientInput = $request->get('ingredients', '');
        $results = collect();

        if ($ingredientInput) {
            // Tách danh sách nguyên liệu nhập vào (tên)
            $userIngredients = collect(explode(',', $ingredientInput))
                ->map(fn($i) => trim(mb_strtolower($i)))
                ->filter()
                ->values();

            // Lấy tất cả công thức published có chứa ít nhất 1 nguyên liệu khớp với từ khóa
            $recipes = Recipe::with(['ingredients', 'user', 'category'])
                ->where('status', 'published')
                ->whereHas('ingredients', function($q) use ($userIngredients) {
                    $q->where(function($query) use ($userIngredients) {
                        foreach ($userIngredients as $ingredient) {
                            $query->orWhere('name', 'like', "%{$ingredient}%");
                        }
                    });
                })
                ->get();

            // Tính Jaccard Similarity
            $results = $recipes->map(function ($recipe) use ($userIngredients) {
                $recipeIngredients = $recipe->ingredients
                    ->pluck('name')
                    ->map(fn($n) => mb_strtolower($n))
                    ->values()
                    ->toArray();

                if (empty($recipeIngredients)) return null;

                $userSet   = $userIngredients->toArray();
                $recipeSet = $recipeIngredients;

                // Jaccard = |A ∩ B| / |A ∪ B|
                $intersection = array_filter($userSet, function ($ingredient) use ($recipeSet) {
                    foreach ($recipeSet as $ri) {
                        if (str_contains($ri, $ingredient) || str_contains($ingredient, $ri)) {
                            return true;
                        }
                    }
                    return false;
                });

                $union       = array_unique(array_merge($userSet, $recipeSet));
                $similarity  = count($union) > 0 ? round((count($intersection) / count($union)) * 100) : 0;
                $matchCount  = count($intersection);

                return [
                    'recipe'     => $recipe,
                    'similarity' => $similarity,
                    'matches'    => $matchCount,
                ];
            })
            ->filter(fn($r) => $r !== null && $r['matches'] > 0)
            ->sortByDesc('similarity')
            ->values();
        }

        $popularIngredients = Ingredient::orderBy('name')->take(30)->pluck('name')->toArray();

        return view('recipes.smart-search', compact('results', 'ingredientInput', 'popularIngredients'));
    }

    // =========================================================
    // COMMENT TRÊN CÔNG THỨC (Auth)
    // =========================================================
    public function storeComment(Request $request, int $recipeId)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        $recipe = Recipe::where('status', 'published')->findOrFail($recipeId);

        Comment::create([
            'user_id'   => Auth::id(),
            'recipe_id' => $recipe->id,
            'parent_id' => $request->parent_id,
            'content'   => $request->content,
        ]);

        // Cập nhật tiến độ thử thách loại 'bình luận'
        Auth::user()->updateChallengeProgress();

        return back()->with('success', 'Đã thêm bình luận!');
    }

    // =========================================================
    // BOOKMARK / LƯU CÔNG THỨC (Auth)
    // =========================================================
    public function toggleBookmark(Request $request)
    {
        $recipeId   = $request->input('recipe_id');
        $recipe     = Recipe::where('status', 'published')->findOrFail($recipeId);
        $userId     = Auth::id();

        // Tìm hoặc tạo collection mặc định "Đã lưu"
        $collection = Collection::firstOrCreate(
            ['user_id' => $userId, 'name' => 'Đã lưu'],
            ['is_default' => true]
        );

        $exists = $collection->recipes()->where('recipe_id', $recipeId)->exists();

        if ($exists) {
            $collection->recipes()->detach($recipeId);
            $saved = false;
        } else {
            $collection->recipes()->attach($recipeId);
            $saved = true;
        }

        return response()->json(['success' => true, 'saved' => $saved]);
    }

    // =========================================================
    // HELPERS PRIVATE
    // =========================================================

    /**
     * Tính tổng dinh dưỡng và sync nguyên liệu vào recipe
     */
    private function syncIngredientsAndCalculateNutrition(Recipe $recipe, array $rawIngredients): void
    {
        $syncData       = [];
        $totalCalories  = 0;
        $totalProtein   = 0;
        $totalCarbs     = 0;
        $totalFat       = 0;

        foreach ($rawIngredients as $item) {
            $ingredientId = $item['id'] ?? null;
            $name         = $item['name'] ?? null;
            $quantity     = (float) ($item['quantity'] ?? 0);
            $notes        = $item['notes'] ?? null;

            if ($quantity <= 0) continue;

            $ingredient = null;
            if ($ingredientId) {
                $ingredient = Ingredient::find($ingredientId);
            } elseif ($name) {
                // Tự động tạo nguyên liệu mới nếu chưa có
                $ingredient = Ingredient::firstOrCreate(
                    ['name' => $name],
                    ['slug' => Str::slug($name) . '-' . Str::random(4), 'unit' => 'g']
                );
                $ingredientId = $ingredient->id;
            }

            if (!$ingredient) continue;

            $syncData[$ingredientId] = ['quantity' => $quantity, 'notes' => $notes];

            // Tính dinh dưỡng: quantity (theo đơn vị của ingredient)
            $totalCalories += $ingredient->calories_per_unit * $quantity;
            $totalProtein  += $ingredient->protein_per_unit  * $quantity;
            $totalCarbs    += $ingredient->carbs_per_unit    * $quantity;
            $totalFat      += $ingredient->fat_per_unit      * $quantity;
        }

        // Sync nhiều-nhiều
        $recipe->ingredients()->sync($syncData);

        // Cập nhật tổng dinh dưỡng
        $recipe->update([
            'total_calories' => (int) $totalCalories,
            'total_protein'  => (int) $totalProtein,
            'total_carbs'    => (int) $totalCarbs,
            'total_fat'      => (int) $totalFat,
        ]);
    }

    /**
     * Sinh slug duy nhất từ tiêu đề
     */
    private function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $baseSlug = Str::slug($title);
        $slug     = $baseSlug;
        $counter  = 1;

        while (true) {
            $query = Recipe::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
            if (!$query->exists()) break;
            $slug = $baseSlug . '-' . $counter++;
        }

        return $slug;
    }
}
