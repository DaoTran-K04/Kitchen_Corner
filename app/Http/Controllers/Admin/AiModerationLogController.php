<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiModerationLog;
use Illuminate\Http\Request;

class AiModerationLogController extends Controller
{
    public function index()
    {
        $logs = AiModerationLog::with('user')->latest()->paginate(20);
        return view('admin.ai-moderation.index', compact('logs'));
    }
}
