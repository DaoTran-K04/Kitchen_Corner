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
            ->withCount(['likes', 'comments'])
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

        $recipes    = $query->paginate(15)->withQueryString();
        $categories = \Illuminate\Support\Facades\Cache::remember('all_categories_list', 300, function () {
            return Category::withCount('recipes')->orderBy('name')->get();
        });

        // Nếu là AJAX request (Infinite Scroll), trả về JSON
        if ($request->ajax()) {
            $html = '';
            foreach ($recipes as $recipe) {
                $html .= view('partials.recipe-card', compact('recipe'))->render();
            }
            return response()->json([
                'html'         => $html,
                'current_page' => $recipes->currentPage(),
                'last_page'    => $recipes->lastPage(),
                'has_more'     => $recipes->hasMorePages(),
            ]);
        }

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
                ->with(['user.activeBadges', 'user.avatarFrames', 'likes', 'replies.user.activeBadges', 'replies.user.avatarFrames', 'replies.likes'])
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
                ->whereHas('recipes', fn($q) => $q->where('recipes.id', $recipe->id))
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

        $paginatedComments = Comment::where('recipe_id', $recipe->id)
            ->whereNull('parent_id')
            ->with(['user.activeBadges', 'user.avatarFrames', 'likes', 'replies.user.activeBadges', 'replies.user.avatarFrames', 'replies.likes'])
            ->latest()
            ->paginate(5)
            ->fragment('comments');

        if (request()->ajax() && request()->has('page')) {
            return view('partials.recipe-comments-list', compact('paginatedComments', 'recipe'))->render();
        }

        return view('recipes.show', compact(
            'recipe',
            'relatedRecipes',
            'userLiked',
            'userSaved',
            'likeCount',
            'commentCount',
            'paginatedComments'
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
            $missingImageWarning = false;
            
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $projectRef = 'uxkrgbnmvnzunxgkaunt';
                $serviceRole = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InV4a3JnYm5tdm56dW54Z2thdW50Iiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc4MDM2Mjc1MiwiZXhwIjoyMDk1OTM4NzUyfQ.MNXPQcNUWESNjC-3Lflqj-jBB4rNpW-R_TrPFIXCCtI';
                $baseUrl = "https://{$projectRef}.supabase.co/storage/v1/object/recipes/{$fileName}";
                
                try {
                    $response = \Illuminate\Support\Facades\Http::withToken($serviceRole)
                        ->withBody(file_get_contents($file->getRealPath()), $file->getMimeType())
                        ->post($baseUrl);
                        
                    if ($response->successful()) {
                        $imagePath = "https://{$projectRef}.supabase.co/storage/v1/object/public/recipes/{$fileName}";
                    } else {
                        $imagePath = $file->store('recipes', 'public');
                    }
                } catch (\Exception $e) {
                    $imagePath = $file->store('recipes', 'public');
                }
            } else {
                $missingImageWarning = true;
                $localImages = glob(public_path('assets/recipes/*.{jpg,jpeg,png}'), GLOB_BRACE);
                if (!empty($localImages)) {
                    $imagePath = 'assets/recipes/' . basename($localImages[array_rand($localImages)]);
                }
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

            if ($missingImageWarning) {
                $msg .= ' (Lưu ý: Vì bạn không tải ảnh lên, hệ thống đã tự động gán ảnh ngẫu nhiên. Bạn có thể sửa lại sau!)';
            }

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
            $missingImageWarning = false;
            
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $projectRef = 'uxkrgbnmvnzunxgkaunt';
                $serviceRole = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InV4a3JnYm5tdm56dW54Z2thdW50Iiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc4MDM2Mjc1MiwiZXhwIjoyMDk1OTM4NzUyfQ.MNXPQcNUWESNjC-3Lflqj-jBB4rNpW-R_TrPFIXCCtI';
                $baseUrl = "https://{$projectRef}.supabase.co/storage/v1/object/recipes/{$fileName}";
                
                try {
                    $response = \Illuminate\Support\Facades\Http::withToken($serviceRole)
                        ->withBody(file_get_contents($file->getRealPath()), $file->getMimeType())
                        ->post($baseUrl);
                        
                    if ($response->successful()) {
                        $recipe->image = "https://{$projectRef}.supabase.co/storage/v1/object/public/recipes/{$fileName}";
                    } else {
                        $recipe->image = $file->store('recipes', 'public');
                    }
                } catch (\Exception $e) {
                    $recipe->image = $file->store('recipes', 'public');
                }
            } elseif (empty($recipe->image)) {
                $missingImageWarning = true;
                $localImages = glob(public_path('assets/recipes/*.{jpg,jpeg,png}'), GLOB_BRACE);
                if (!empty($localImages)) {
                    $recipe->image = 'assets/recipes/' . basename($localImages[array_rand($localImages)]);
                }
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

            $msg = 'Công thức đã được cập nhật!';
            if ($missingImageWarning) {
                $msg .= ' (Lưu ý: Vì công thức chưa có ảnh bìa, hệ thống đã tự động gán ảnh ngẫu nhiên. Bạn có thể thay đổi sau!)';
            }

            return redirect()->route('recipes.show', $recipe->slug)
                ->with('success', $msg);

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
            ->withCount(['likes', 'comments'])
            ->where('status', 'published');

        if ($keyword) {
            $lowerKeyword = mb_strtolower($keyword, 'UTF-8');
            $query->where(function ($q) use ($lowerKeyword) {
                $q->whereRaw('LOWER(title) COLLATE utf8mb4_bin LIKE ?', ["%{$lowerKeyword}%"])
                  ->orWhereRaw('LOWER(description) COLLATE utf8mb4_bin LIKE ?', ["%{$lowerKeyword}%"])
                  ->orWhereHas('category', function($cat) use ($lowerKeyword) {
                      $cat->whereRaw('LOWER(name) COLLATE utf8mb4_bin LIKE ?', ["%{$lowerKeyword}%"]);
                  })
                  ->orWhereHas('ingredients', function($ing) use ($lowerKeyword) {
                      $ing->whereRaw('LOWER(name) COLLATE utf8mb4_bin LIKE ?', ["%{$lowerKeyword}%"]);
                  });
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $recipes    = $query->orderByDesc('view_count')->paginate(15)->withQueryString();
        $categories = \Illuminate\Support\Facades\Cache::remember('all_categories_list', 300, fn() => Category::orderBy('name')->get());
        $total      = $query->count();

        return view('recipes.search', compact('recipes', 'categories', 'keyword', 'total'));
    }

    // =========================================================
    // SMART SEARCH – TỦ LẠNH AI (Cá Nhân Hóa)
    // =========================================================
    public function smartSearch(Request $request)
    {
        $ingredientInput = $request->get('ingredients', '');
        $userId = Auth::id();

        // Lấy sẵn các quan hệ và số lượt likes
        $baseQuery = Recipe::with(['ingredients', 'user', 'category'])
            ->withCount(['likes', 'comments'])
            ->where('status', 'published');

        // --- A. LUÔN LẤY AI RECOMMENDED RECIPES (Dành riêng cho bạn) ---
        $aiResults = collect();
        if ($userId) {
            $recipesForAi = (clone $baseQuery)->orderByDesc('view_count')->take(50)->get();
            $aiResults = $this->calculateAiMatchScores($recipesForAi, $userId, null, '');
            $aiResults = $aiResults->filter(fn($r) => $r['similarity'] > 15)
                               ->sortByDesc('similarity')
                               ->take(12)
                               ->values();
        } else {
            $recipesForAi = (clone $baseQuery)->orderByDesc('view_count')->take(12)->get();
            $aiResults = $this->calculateAiMatchScores($recipesForAi, null, null, '');
        }

        // --- B. LẤY KẾT QUẢ TÌM KIẾM (Nếu có input) ---
        $searchResults = collect();
        $aiOverviewHtml = null;
        $externalResults = [];
        if ($ingredientInput) {
            $userIngredients = collect(explode(',', $ingredientInput))
                ->map(fn($i) => trim(mb_strtolower($i)))
                ->filter()
                ->values();

            // Gọi MockAiService để mở rộng từ khóa (Semantic Expansion)
            $mockAi = new \App\Services\MockAiService();
            $expandedKeywords = $mockAi->expandKeywords($userIngredients->toArray());

            $recipesForSearch = (clone $baseQuery)
                ->where(function($query) use ($expandedKeywords) {
                    $query->whereHas('ingredients', function($q) use ($expandedKeywords) {
                        $q->where(function($q2) use ($expandedKeywords) {
                            foreach ($expandedKeywords as $keyword) {
                                $lowerKeyword = mb_strtolower($keyword, 'UTF-8');
                                $q2->orWhereRaw('LOWER(name) COLLATE utf8mb4_bin LIKE ?', ["%{$lowerKeyword}%"]);
                            }
                        });
                    })
                    ->orWhere(function($q3) use ($expandedKeywords) {
                        foreach ($expandedKeywords as $keyword) {
                            $lowerKeyword = mb_strtolower($keyword, 'UTF-8');
                            $q3->orWhereRaw('LOWER(title) COLLATE utf8mb4_bin LIKE ?', ["%{$lowerKeyword}%"]);
                        }
                    });
                })
                ->get();

            // Jaccard similarity vẫn sẽ tính toán dựa trên danh sách từ khóa đã được mở rộng
            $searchResults = $this->calculateAiMatchScores($recipesForSearch, $userId, collect($expandedKeywords), $ingredientInput);
            
            // Tạo câu chào AI Overview
            $aiOverviewHtml = $mockAi->generateOverview($ingredientInput, $searchResults->count());

            // Lấy thêm kết quả từ nguồn ngoài (Mock Google Search)
            $externalResults = $mockAi->fetchExternalResults($ingredientInput);
        }

        $popularIngredients = Ingredient::orderBy('name')->take(30)->pluck('name')->toArray();

        return view('recipes.smart-search', compact('aiResults', 'searchResults', 'ingredientInput', 'popularIngredients', 'aiOverviewHtml', 'externalResults'));
    }

    /**
     * AI Hybrid Recommendation Engine
     * Tính điểm dựa trên: Sở thích (30%) + Tương tác/Độ phổ biến (20%) + Khớp nguyên liệu (50%)
     */
    private function calculateAiMatchScores($recipes, $userId, $userIngredients = null, $ingredientInput = '')
    {
        // 1. Phân tích Profile của User (Sở thích)
        $userCategories = [];
        
        if ($userId) {
            // Cache sở thích category trong 30 phút để tối ưu hiệu năng
            $userCategories = \Illuminate\Support\Facades\Cache::remember('user_fav_categories_' . $userId, 1800, function() use ($userId) {
                $likedCategoryIds = Like::where('likes.user_id', $userId)
                    ->join('recipes', 'likes.recipe_id', '=', 'recipes.id')
                    ->pluck('recipes.category_id')->toArray();
                    
                $savedCategoryIds = Collection::where('collections.user_id', $userId)
                    ->join('collection_recipes', 'collections.id', '=', 'collection_recipes.collection_id')
                    ->join('recipes', 'collection_recipes.recipe_id', '=', 'recipes.id')
                    ->pluck('recipes.category_id')->toArray();
                    
                $allFav = array_merge($likedCategoryIds, $savedCategoryIds);
                if (empty($allFav)) return [];
                
                $counts = array_count_values($allFav);
                arsort($counts); // Sắp xếp theo số lần xuất hiện nhiều nhất
                return array_keys(array_slice($counts, 0, 3)); // Lấy Top 3 thể loại yêu thích
            });
        }

        // 2. Tính điểm cho từng công thức
        $results = $recipes->map(function ($recipe) use ($userIngredients, $userCategories, $ingredientInput, $userId) {
            $score = 0;
            $matchCount = 0;
            
            // --- A. Jaccard Similarity cho Nguyên liệu (Max 50đ) ---
            if ($userIngredients && $userIngredients->count() > 0) {
                $recipeIngredients = $recipe->ingredients->pluck('name')->map(fn($n) => mb_strtolower($n))->toArray();
                if (!empty($recipeIngredients)) {
                    $userSet = $userIngredients->toArray();
                    
                    $intersection = array_filter($userSet, function ($ingredient) use ($recipeIngredients) {
                        foreach ($recipeIngredients as $ri) {
                            if (str_contains($ri, $ingredient) || str_contains($ingredient, $ri)) {
                                return true;
                            }
                        }
                        return false;
                    });
                    
                    $union = array_unique(array_merge($userSet, $recipeIngredients));
                    $jaccard = count($union) > 0 ? (count($intersection) / count($union)) : 0;
                    
                    $score += round($jaccard * 50);
                    $matchCount = count($intersection);
                }
            } else {
                // Nếu ko có input nguyên liệu, tự động cấp một số điểm nền tảng (Base 20đ)
                $score += 20; 
            }
            
            // --- B. Sở thích Thể loại (Max 30đ) ---
            if (in_array($recipe->category_id, $userCategories)) {
                $score += 30;
            } elseif (count($userCategories) == 0) {
                // User mới chưa có sở thích: Tạo sở thích giả lập dựa trên ID để mỗi user thấy kết quả khác nhau
                $pseudoFavCategory = ($userId % 8) + 1; // Giả lập 1 category yêu thích ngẫu nhiên dựa trên ID
                
                if ($recipe->category_id == $pseudoFavCategory) {
                    $score += 25; // Ưu tiên category giả lập này
                } else {
                    // Cộng điểm đa dạng dựa trên sự kết hợp giữa Recipe ID và User ID (từ 0 đến 10 điểm)
                    $score += 10 + (($recipe->id + $userId) % 10);
                }

                // Vẫn ưu tiên nhẹ cho các món dễ nấu
                if ($recipe->difficulty === 'easy') {
                    $score += 5;
                }
            }
            
            // --- C. Độ Phổ Biến (Max 20đ) ---
            // 1000 views = 10đ, 50 likes = 10đ
            $viewScore = min(10, ($recipe->view_count / 100));
            $likeScore = min(10, ($recipe->likes_count / 5));
            $score += round($viewScore + $likeScore);
            
            // --- D. Khớp Tiêu Đề (Bonus) ---
            if ($ingredientInput && str_contains(mb_strtolower($recipe->title), mb_strtolower($ingredientInput))) {
                $score += 80;
            }
            
            // Đảm bảo score tối đa 99% để trông thực tế hơn với người dùng
            $score = min(99, $score);
            
            return [
                'recipe'     => $recipe,
                'similarity' => $score,
                'matches'    => $matchCount,
            ];
        });

        // 3. Lọc và Sắp xếp
        if ($userIngredients) {
            return $results->filter(fn($r) => $r !== null && ($r['matches'] > 0 || $r['similarity'] >= 80))
                           ->sortByDesc('similarity')
                           ->values();
        }
        
        return $results;
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
