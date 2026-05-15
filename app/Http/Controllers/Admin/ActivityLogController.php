<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\Recipe;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Hiển thị danh sách lịch sử hoạt động
     */
    public function index(Request $request)
    {
        $query = AdminActivityLog::with('admin')->latest();

        // Lọc theo admin
        if ($request->filled('admin_id')) {
            $query->where('admin_id', $request->admin_id);
        }

        // Lọc theo loại action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Lọc theo ngày
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Tìm kiếm theo mô tả
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $logs = $query->paginate(20)->withQueryString();

        // Lấy danh sách admins để filter
        $admins = User::where('role', 'admin')->get();

        // Các loại action
        $actions = AdminActivityLog::distinct()
            ->whereNotIn('action', ['cleanup', 'restore', 'force_delete'])
            ->pluck('action');

        // AJAX request - return JSON
        if ($request->ajax() || $request->has('ajax')) {
            $paginationHtml = '';
            if ($logs->hasPages()) {
                $paginationHtml = '<div class="p-4 border-t border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-700">'
                    . $logs->links('vendor.pagination.admin')->toHtml()
                    . '</div>';
            }
            return response()->json([
                'table' => view('admin.activity-logs._table', compact('logs'))->render(),
                'pagination' => $paginationHtml
            ]);
        }

        return view('admin.activity-logs.index', compact('logs', 'admins', 'actions'));
    }

    /**
     * Xem chi tiết một log
     */
    public function show(AdminActivityLog $activityLog)
    {
        $activityLog->load('admin');
        return view('admin.activity-logs.show', compact('activityLog'));
    }

    /**
     * Xóa log cũ (chỉ giữ lại trong vòng X ngày)
     */
    public function cleanup(Request $request)
    {
        $days = $request->input('days', 90);

        $deleted = AdminActivityLog::where('created_at', '<', now()->subDays($days))->delete();

        // Ghi log hành động dọn dẹp
        AdminActivityLog::log(
            'cleanup',
            "Dọn dẹp {$deleted} log cũ hơn {$days} ngày",
        );

        return back()->with('success', "Đã xóa {$deleted} log cũ!");
    }

    /**
     * Khôi phục từ Activity Log (restore từ old_values)
     */
    public function restore(AdminActivityLog $activityLog)
    {
        // Chỉ khôi phục được action delete có old_values
        if ($activityLog->action !== 'delete' || !$activityLog->old_values) {
            return back()->with('error', 'Không thể khôi phục log này. Chỉ hỗ trợ khôi phục các mục đã xóa.');
        }

        $modelClass = $activityLog->model_type;
        $oldValues = $activityLog->old_values;

        // Kiểm tra model class hợp lệ
        if (!class_exists($modelClass)) {
            return back()->with('error', 'Không tìm thấy loại dữ liệu: ' . $modelClass);
        }

        try {
            // Kiểm tra xem model có sử dụng SoftDeletes không
            $usesSoftDeletes = in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive($modelClass));

            // Kiểm tra trùng lặp trước khi restore (check name hoặc title)
            $duplicateField = null;
            $duplicateValue = null;

            if (isset($oldValues['name'])) {
                $duplicateField = 'name';
                $duplicateValue = $oldValues['name'];
            } elseif (isset($oldValues['title'])) {
                $duplicateField = 'title';
                $duplicateValue = $oldValues['title'];
            }

            if ($duplicateField && $duplicateValue) {
                $existingActive = $modelClass::where($duplicateField, $duplicateValue)->first();
                if ($existingActive) {
                    return back()->with('error', "Không thể khôi phục! Đã tồn tại \"{$duplicateValue}\" đang hoạt động.");
                }
            }

            $existingRecord = null;
            if ($usesSoftDeletes) {
                // Chỉ dùng withTrashed nếu model có SoftDeletes
                $existingRecord = $modelClass::withTrashed()->find($activityLog->model_id);
            } else {
                // Nếu không có SoftDeletes, check record bình thường
                $existingRecord = $modelClass::find($activityLog->model_id);
            }

            if ($existingRecord && $usesSoftDeletes && method_exists($existingRecord, 'trashed') && $existingRecord->trashed()) {
                // Restore nếu đang bị soft delete
                $existingRecord->restore();
                $actionDesc = "Khôi phục (soft delete): " . $this->getModelDescription($modelClass, $existingRecord);
            } elseif (!$existingRecord) {
                // Tạo mới từ old_values nếu không tồn tại
                $filteredValues = $this->filterRestorableValues($modelClass, $oldValues);
                $newRecord = $modelClass::create($filteredValues);
                $actionDesc = "Khôi phục (tạo lại): " . $this->getModelDescription($modelClass, $newRecord);
            } else {
                return back()->with('error', 'Mục này vẫn còn tồn tại, không cần khôi phục.');
            }

            // Ghi log khôi phục
            AdminActivityLog::log(
                'restore',
                $actionDesc,
                $modelClass,
                $activityLog->model_id
            );

            return back()->with('success', 'Đã khôi phục thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi khi khôi phục: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị danh sách Thùng rác (các mục đã xóa mềm)
     */
    public function trash()
    {
        $trashedRecipes    = Recipe::onlyTrashed()->latest('deleted_at')->get();
        $trashedCategories = Category::onlyTrashed()->latest('deleted_at')->get();
        $trashedUsers      = User::onlyTrashed()->latest('deleted_at')->get();

        return view('admin.activity-logs.trash', compact(
            'trashedRecipes',
            'trashedCategories',
            'trashedUsers'
        ));
    }

    /**
     * Khôi phục từ soft delete
     */
    public function restoreTrashed(Request $request)
    {
        $type = $request->input('type');
        $id = $request->input('id');

        $modelMap = [
            'recipe'   => Recipe::class,
            'category' => Category::class,
            'user'     => User::class,
        ];

        if (!isset($modelMap[$type])) {
            return back()->with('error', 'Loại dữ liệu không hợp lệ.');
        }

        $modelClass = $modelMap[$type];
        $record = $modelClass::withTrashed()->find($id);

        if (!$record || !$record->trashed()) {
            return back()->with('error', 'Không tìm thấy mục cần khôi phục.');
        }

        $record->restore();

        // Ghi log
        AdminActivityLog::log(
            'restore',
            "Khôi phục từ thùng rác: " . $this->getModelDescription($modelClass, $record),
            $modelClass,
            $id
        );

        return back()->with('success', 'Đã khôi phục thành công!');
    }

    /**
     * Xóa vĩnh viễn (hard delete)
     */
    public function forceDelete(Request $request)
    {
        $type = $request->input('type');
        $id = $request->input('id');

        $modelMap = [
            'recipe'   => Recipe::class,
            'category' => Category::class,
            'user'     => User::class,
        ];

        if (!isset($modelMap[$type])) {
            return back()->with('error', 'Loại dữ liệu không hợp lệ.');
        }

        $modelClass = $modelMap[$type];
        $record = $modelClass::withTrashed()->find($id);

        if (!$record) {
            return back()->with('error', 'Không tìm thấy mục cần xóa.');
        }

        $description = $this->getModelDescription($modelClass, $record);
        $record->forceDelete();

        // Ghi log
        AdminActivityLog::log(
            'force_delete',
            "Xóa vĩnh viễn: " . $description,
            $modelClass,
            $id
        );

        return back()->with('success', 'Đã xóa vĩnh viễn!');
    }

    /**
     * Lọc các giá trị có thể restore (loại bỏ id, timestamps)
     */
    private function filterRestorableValues(string $modelClass, array $values): array
    {
        $exclude = ['id', 'created_at', 'updated_at', 'deleted_at'];

        // Loại bỏ password hash cho User
        if ($modelClass === User::class) {
            // Không restore password từ log
            $exclude[] = 'password';
        }

        return array_diff_key($values, array_flip($exclude));
    }

    /**
     * Lấy mô tả ngắn gọn của model
     */
    private function getModelDescription(string $modelClass, $record): string
    {
        return match ($modelClass) {
            Recipe::class   => "C\u00f4ng th\u1ee9c: {$record->title}",
            Category::class => "Danh m\u1ee5c: {$record->name}",
            User::class     => "Th\u00e0nh vi\u00ean: {$record->name} ({$record->email})",
            default         => class_basename($modelClass) . " #{$record->id}",
        };
    }
}
