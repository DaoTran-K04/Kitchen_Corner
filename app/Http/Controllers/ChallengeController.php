<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Challenge;
use Illuminate\Support\Facades\Auth;

class ChallengeController extends Controller
{
    // 1. Trang danh sách thử thách (Frontend)
    public function index()
    {
        // Lấy các thử thách đang mở (is_active = 1)
        // Sắp xếp mới nhất lên đầu
        $challenges = Challenge::with(['badge', 'avatarFrame', 'users'])
            ->where('is_active', 1)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('challenges', compact('challenges'));
    }

    // 2. Xử lý hành động "Tham Gia"
    public function join($id)
    {
        // Kiểm tra đăng nhập
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để tham gia!');
        }

        $user = Auth::user();
        $challenge = Challenge::findOrFail($id);

        // Kiểm tra xem thử thách đã bắt đầu chưa
        $today = now()->startOfDay();
        if ($challenge->start_date->startOfDay()->gt($today)) {
            return redirect()->back()->with('error', 'Thử thách chưa bắt đầu! Vui lòng quay lại sau.');
        }

        // Kiểm tra xem thử thách đã kết thúc chưa
        if ($challenge->end_date->startOfDay()->lt($today)) {
            return redirect()->back()->with('error', 'Thử thách đã kết thúc!');
        }

        // Kiểm tra xem đã tham gia chưa
        // Lưu ý: Dùng hàm challenges() trong Model User mà chúng ta đã sửa
        if ($user->challenges()->where('challenge_id', $id)->exists()) {
            return redirect()->back()->with('info', 'Bạn đã tham gia thử thách này rồi!');
        }

        // Thêm vào bảng user_challenges
        // Các cột current_count, is_completed sẽ nhận giá trị mặc định từ database (0)
        $user->challenges()->attach($id, [
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Chào mừng bạn tham gia thử thách: ' . $challenge->name);
    }
}