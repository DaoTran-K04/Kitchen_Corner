<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feedback;

class FeedbackController extends Controller
{
    public function index()
    {
        $feedbacks = Feedback::latest()->paginate(15);
        return view('admin.feedbacks.index', compact('feedbacks'));
    }

    public function show(Feedback $feedback)
    {
        if ($feedback->status === 'pending') {
            $feedback->update(['status' => 'read']);
        }
        return view('admin.feedbacks.show', compact('feedback'));
    }

    public function updateStatus(Request $request, Feedback $feedback)
    {
        $request->validate([
            'status' => 'required|in:pending,read,resolved'
        ]);

        $feedback->update(['status' => $request->status]);
        return redirect()->back()->with('success', 'Đã cập nhật trạng thái góp ý.');
    }

    public function destroy(Feedback $feedback)
    {
        $feedback->delete();
        return redirect()->route('admin.feedbacks.index')->with('success', 'Đã xóa góp ý thành công.');
    }
}
