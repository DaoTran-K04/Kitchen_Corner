<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BannerController extends Controller
{
    // Hiển thị danh sách Banner
    public function index(Request $request)
    {
        $sortField = $request->input('sort', 'order');
        $sortDirection = $request->input('direction', 'asc');

        // Validate sort field
        $allowedSorts = ['order', 'title', 'created_at'];
        if (!in_array($sortField, $allowedSorts)) {
            $sortField = 'order';
        }

        // Validate direction
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        $banners = Banner::orderBy($sortField, $sortDirection)->get();

        return view('admin.banners.index', compact('banners', 'sortField', 'sortDirection'));
    }

    // Hiển thị form tạo mới
    public function create()
    {
        return view('admin.banners.create');
    }

    // Lưu banner mới
    public function store(Request $request)
    {
        // Validate - yêu cầu ảnh hoặc URL ảnh
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'image_url' => 'nullable|url',
            'order' => 'integer|min:0',
        ]);

        // Kiểm tra phải có ít nhất 1 nguồn ảnh
        if (!$request->hasFile('image') && !$request->filled('image_url')) {
            return back()->withInput()->with('error', 'Vui lòng tải ảnh từ máy hoặc nhập URL ảnh!');
        }

        $data = $request->except(['image', 'image_url']);
        $data['is_active'] = $request->has('is_active');

        // Xử lý thứ tự hiển thị
        $requestedOrder = (int) $request->input('order', 0);

        if ($requestedOrder <= 0) {
            // Nếu không chọn thứ tự (= 0) thì tự động thêm vào cuối
            $maxOrder = Banner::max('order') ?? 0;
            $data['order'] = $maxOrder + 1;
        } else {
            // Nếu chọn thứ tự cụ thể, đẩy các banner có order >= xuống 1 bậc
            Banner::where('order', '>=', $requestedOrder)->increment('order');
            $data['order'] = $requestedOrder;
        }

        // Xử lý ảnh: ưu tiên file upload, nếu không có thì dùng URL
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('banners', 'public');
        } elseif ($request->filled('image_url')) {
            $data['image'] = $request->input('image_url');
        }

        Banner::create($data);

        return redirect()->route('admin.banners.index')->with('success', 'Thêm Banner thành công!');
    }

    // Hiển thị form chỉnh sửa (Quan trọng cho nút Sửa ngoài trang chủ)
    public function edit($id)
    {
        $banner = Banner::findOrFail($id);
        // Bạn cần tạo view: resources/views/admin/banners/edit.blade.php
        return view('admin.banners.edit', compact('banner'));
    }

    // Cập nhật banner
    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'image_url' => 'nullable|url',
            'order' => 'integer|min:0',
        ]);

        $data = $request->except(['image', 'image_url']);
        $data['is_active'] = $request->has('is_active'); // Checkbox

        // Xử lý thứ tự hiển thị nếu có thay đổi
        $oldOrder = $banner->order;
        $newOrder = (int) $request->input('order', $oldOrder);

        if ($newOrder !== $oldOrder && $newOrder > 0) {
            if ($newOrder < $oldOrder) {
                // Di chuyển lên: đẩy các banner từ newOrder đến oldOrder-1 xuống 1 bậc
                Banner::where('order', '>=', $newOrder)
                    ->where('order', '<', $oldOrder)
                    ->where('id', '!=', $banner->id)
                    ->increment('order');
            } else {
                // Di chuyển xuống: đẩy các banner từ oldOrder+1 đến newOrder lên 1 bậc
                Banner::where('order', '>', $oldOrder)
                    ->where('order', '<=', $newOrder)
                    ->where('id', '!=', $banner->id)
                    ->decrement('order');
            }
            $data['order'] = $newOrder;
        }

        // Xử lý ảnh: ưu tiên file upload, sau đó URL ảnh
        if ($request->hasFile('image')) {
            // Xóa ảnh cũ nếu nó nằm trong storage
            if ($banner->image && !Str::startsWith($banner->image, 'http')) {
                Storage::delete('public/' . $banner->image);
            }
            $data['image'] = $request->file('image')->store('banners', 'public');
        } elseif ($request->filled('image_url')) {
            // Xóa ảnh cũ nếu nó nằm trong storage
            if ($banner->image && !Str::startsWith($banner->image, 'http')) {
                Storage::delete('public/' . $banner->image);
            }
            $data['image'] = $request->input('image_url');
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'Cập nhật Banner thành công!');
    }

    // Xóa banner
    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);
        $deletedOrder = $banner->order;
        $bannerData = $banner->toArray();

        // Xóa ảnh nếu nằm trong storage (chỉ khi xóa cứng)
        // Với soft delete, giữ ảnh để có thể restore

        $banner->delete();

        // Ghi log để có thể khôi phục
        \App\Models\AdminActivityLog::log(
            'delete',
            "Xóa Banner: {$bannerData['title']}",
            Banner::class,
            $bannerData['id'],
            $bannerData,
            null
        );

        // Giảm order của các banner có order lớn hơn banner vừa xóa
        Banner::where('order', '>', $deletedOrder)->decrement('order');

        return redirect()->back()->with('success', 'Đã xóa Banner!');
    }
}