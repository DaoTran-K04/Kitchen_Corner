<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    /**
     * Display a listing of subscribers
     */
    public function index(Request $request)
    {
        $query = Subscriber::query();

        // Search by email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('email', 'like', "%{$search}%");
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $subscribers = $query->latest()->paginate(20)->appends($request->query());

        // Stats
        $totalCount = Subscriber::count();
        $activeCount = Subscriber::where('is_active', true)->count();
        $inactiveCount = Subscriber::where('is_active', false)->count();

        // AJAX request - return JSON with rendered HTML
        if ($request->ajax() || $request->has('ajax')) {
            $paginationHtml = '';
            if ($subscribers->hasPages()) {
                $paginationHtml = '<div class="p-4 border-t border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-700">'
                    . $subscribers->links('vendor.pagination.admin')->toHtml()
                    . '</div>';
            }
            return response()->json([
                'table' => view('admin.subscribers._table', compact('subscribers'))->render(),
                'pagination' => $paginationHtml
            ]);
        }

        // Mark as viewed - store current time in session
        session(['admin_last_viewed_subscribers' => now()]);

        return view('admin.subscribers.index', compact('subscribers', 'totalCount', 'activeCount', 'inactiveCount'));
    }

    /**
     * Toggle active status
     */
    public function toggleActive(Subscriber $subscriber)
    {
        $subscriber->is_active = !$subscriber->is_active;
        $subscriber->save();

        $action = $subscriber->is_active ? 'Kích hoạt' : 'Vô hiệu hóa';

        return back()->with('success', "Đã {$action} subscriber!");
    }

    /**
     * Delete a subscriber
     */
    public function destroy(Subscriber $subscriber)
    {
        $email = $subscriber->email;
        $subscriber->delete();

        return back()->with('success', "Đã xóa subscriber: {$email}");
    }

    /**
     * Export subscribers to CSV
     */
    public function export()
    {
        $subscribers = Subscriber::where('is_active', true)->orderBy('email')->get();

        $filename = 'subscribers_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($subscribers) {
            $file = fopen('php://output', 'w');
            // UTF-8 BOM for Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            
            // Header row
            fputcsv($file, ['Email', 'Ngày đăng ký']);

            foreach ($subscribers as $subscriber) {
                fputcsv($file, [
                    $subscriber->email,
                    $subscriber->subscribed_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
