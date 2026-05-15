<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityTitle;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;

class ActivityTitleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $titles = ActivityTitle::orderBy('priority', 'desc')->paginate(15);
        return view('admin.activity_titles.index', compact('titles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.activity_titles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:activity_titles,name',
            'icon' => 'nullable|string|max:255',
            'color' => 'required|string|max:20',
            'min_posts' => 'required|integer|min:0',
            'min_books' => 'required|integer|min:0',
            'priority' => 'required|integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $title = ActivityTitle::create($validated);

        // Log
        AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'create',
            'description' => 'Tạo danh hiệu hoạt động: ' . $title->name,
            'model_type' => ActivityTitle::class,
            'model_id' => $title->id,
            'new_values' => $title->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.activity-titles.index')
            ->with('success', 'Đã tạo danh hiệu mới thành công!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ActivityTitle $activityTitle)
    {
        return view('admin.activity_titles.edit', compact('activityTitle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ActivityTitle $activityTitle)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:activity_titles,name,' . $activityTitle->id,
            'icon' => 'nullable|string|max:255',
            'color' => 'required|string|max:20',
            'min_posts' => 'required|integer|min:0',
            'min_books' => 'required|integer|min:0',
            'priority' => 'required|integer|min:0',
        ]);

        $oldValues = $activityTitle->toArray();
        $validated['is_active'] = $request->has('is_active');

        $activityTitle->update($validated);

        // Log
        AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'update',
            'description' => 'Cập nhật danh hiệu hoạt động: ' . $activityTitle->name,
            'model_type' => ActivityTitle::class,
            'model_id' => $activityTitle->id,
            'old_values' => $oldValues,
            'new_values' => $activityTitle->fresh()->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.activity-titles.index')
            ->with('success', 'Đã cập nhật danh hiệu thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, ActivityTitle $activityTitle)
    {
        $oldValues = $activityTitle->toArray();
        $name = $activityTitle->name;

        $activityTitle->delete();

        // Log
        AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'delete',
            'description' => 'Xóa danh hiệu hoạt động: ' . $name,
            'model_type' => ActivityTitle::class,
            'model_id' => $oldValues['id'],
            'old_values' => $oldValues,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.activity-titles.index')
            ->with('success', 'Đã xóa danh hiệu thành công!');
    }
}
