<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\Challenge;
use App\Models\AvatarFrame;

class GameController extends Controller
{
    /**
     * Trang tích hợp quản lý Badges, Challenges và Avatar Frames
     */
    public function index()
    {
        $badges = Badge::withCount(['userBadges', 'challenges'])
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'badges_page');

        $challenges = Challenge::with(['badge', 'avatarFrame'])
            ->withCount([
                'userChallenges',
                'userChallenges as completed_count' => function ($query) {
                    $query->where('is_completed', true);
                }
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'challenges_page');

        $frames = AvatarFrame::withCount('userAvatarFrames')
            ->orderBy('order', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'frames_page');

        return view('admin.game.index', compact('badges', 'challenges', 'frames'));
    }
}
