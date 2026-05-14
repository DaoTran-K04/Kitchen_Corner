<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Models\Category;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    public function index(Request $request)
    {
        $query = Recipe::with(['user', 'category'])->withCount(['comments', 'likes']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $recipes    = $query->latest()->paginate(20)->withQueryString();
        $categories = Category::orderBy('name')->get();

        if ($request->ajax()) {
            return view('admin.recipes.table', compact('recipes', 'categories'))->render();
        }

        return view('admin.recipes.index', compact('recipes', 'categories'));
    }

    public function show(Recipe $recipe)
    {
        $recipe->load(['user', 'category', 'steps', 'ingredients', 'comments.user']);
        return view('admin.recipes.show', compact('recipe'));
    }

    public function approve(Recipe $recipe)
    {
        if ($recipe->status !== 'published') {
            $recipe->update(['status' => 'published']);
            
            // Gamification: Update user challenges when a recipe is published
            if ($recipe->user) {
                $recipe->user->updateChallengeProgress();
                
                // Gửi thông báo cho người dùng
                try {
                    $recipe->user->notify(new \App\Notifications\RecipeApprovedNotification($recipe));
                } catch (\Exception $e) {
                    \Log::error("Lỗi gửi thông báo phê duyệt: " . $e->getMessage());
                }
            }
        }
        
        return back()->with('success', 'Công thức "' . $recipe->title . '" đã được duyệt.');
    }

    public function feature(Recipe $recipe)
    {
        $recipe->update(['is_featured' => !$recipe->is_featured]);
        $msg = $recipe->is_featured ? 'Đã đặt nổi bật.' : 'Đã bỏ nổi bật.';
        return back()->with('success', $msg);
    }

    public function reject(Request $request, Recipe $recipe)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $recipe->update(['status' => 'draft']);

        $isViolation = $request->has('is_violation');
        
        if ($recipe->user) {
            if ($isViolation) {
                $recipe->user->increment('violation_count');
            }

            try {
                $recipe->user->notify(new \App\Notifications\RecipeRejectedNotification($recipe, $request->reason, $isViolation));
            } catch (\Exception $e) {
                \Log::error("Lỗi gửi thông báo từ chối: " . $e->getMessage());
            }
        }

        return back()->with('success', 'Đã từ chối công thức "' . $recipe->title . '".');
    }

    public function destroy(Recipe $recipe)
    {
        $title = $recipe->title;
        $recipe->delete();
        return redirect()->route('admin.recipes.index')
            ->with('success', 'Đã xóa công thức "' . $title . '".');
    }
}
