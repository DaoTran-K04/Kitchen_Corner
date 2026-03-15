<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::withCount('books')->latest()->paginate(10);
        // Lưu ý: Bạn cần tạo view 'admin.categories.index' sau này
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:2|max:100'
        ], [
            'name.required' => 'Tên danh mục là bắt buộc!',
            'name.min' => 'Tên danh mục phải có ít nhất 2 ký tự!',
            'name.max' => 'Tên danh mục không được quá 100 ký tự!'
        ]);

        $slug = Str::slug($request->name);

        // Kiểm tra xem có danh mục đã soft-delete với name hoặc slug này không
        $existingTrashed = Category::withTrashed()
            ->where(function ($query) use ($request, $slug) {
                $query->where('name', $request->name)
                    ->orWhere('slug', $slug);
            })
            ->first();

        if ($existingTrashed) {
            if ($existingTrashed->trashed()) {
                // Khôi phục danh mục đã xóa và cập nhật thông tin
                $existingTrashed->restore();
                $existingTrashed->update([
                    'name' => $request->name,
                    'slug' => $slug,
                    'description' => $request->description
                ]);

                AdminActivityLog::log(
                    'restore',
                    "Khôi phục danh mục: {$existingTrashed->name}",
                    Category::class,
                    $existingTrashed->id,
                    null,
                    $existingTrashed->toArray()
                );

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'category' => $existingTrashed,
                        'message' => 'Đã khôi phục danh mục đã xóa trước đó!'
                    ]);
                }
                return back()->with('success', 'Đã khôi phục danh mục đã xóa trước đó!');
            } else {
                // Danh mục đang hoạt động - báo lỗi trùng
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['name' => ['Tên danh mục hoặc slug đã tồn tại!']]
                    ], 422);
                }
                return back()->withErrors(['name' => 'Tên danh mục hoặc slug đã tồn tại!']);
            }
        }

        $category = Category::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description
        ]);

        AdminActivityLog::log(
            'create',
            "Thêm danh mục mới: {$category->name}",
            Category::class,
            $category->id,
            null,
            $category->toArray()
        );

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'category' => $category]);
        }
        return back()->with('success', 'Thêm danh mục thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Category $category)
    {
        $categoryData = $category->toArray();
        $categoryName = $category->name;

        $category->delete();

        AdminActivityLog::log(
            'delete',
            "Xóa danh mục: {$categoryName}",
            Category::class,
            $categoryData['id'],
            $categoryData,
            null
        );

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'Đã xóa danh mục!');
    }
}
