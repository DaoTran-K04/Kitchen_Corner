<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'subject' => 'required|string|max:200',
            'message' => 'required|string',
        ]);

        Feedback::create([
            'user_id' => Auth::check() ? Auth::id() : null,
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Cảm ơn bạn đã gửi góp ý! Chúng tôi sẽ xem xét sớm nhất có thể.');
    }
}
