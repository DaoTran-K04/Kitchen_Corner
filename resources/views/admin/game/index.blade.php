@extends('layouts.admin')
@section('title', 'Quản Lý Thử Thách & Phần Thưởng')
@section('header', 'Quản Lý Thử Thách & Phần Thưởng')

@section('content')
    @php
        // Xác định tab cần hiển thị khi có lỗi validation
        $activeTabFromErrors = 'badges'; // Mặc định
        if ($errors->has('badge_id') || $errors->has('target_count') || $errors->has('start_date') || $errors->has('end_date')) {
            // Kiểm tra xem lỗi là từ form Challenge hay Badge
            if ($errors->has('target_count') || $errors->has('start_date') || $errors->has('end_date')) {
                $activeTabFromErrors = 'challenges';
            }
        }
        if ($errors->has('frame_image') || $errors->has('frame_image_url')) {
            $activeTabFromErrors = 'frames';
        }
    @endphp
    <div class="space-y-6">
        {{-- Tab Navigation --}}
        <div
            class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 p-1 inline-flex transition-colors duration-300">
            <button onclick="showTab('challenges')" id="tab-challenges"
                class="tab-btn px-6 py-2 rounded-lg font-medium transition-all bg-yellow-500 text-white">
                <i class="fas fa-trophy mr-2"></i>Thử Thách
            </button>
            <button onclick="showTab('badges')" id="tab-badges"
                class="tab-btn px-6 py-2 rounded-lg font-medium transition-all text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700">
                <i class="fas fa-medal mr-2"></i>Biểu Tượng
            </button>
            <button onclick="showTab('frames')" id="tab-frames"
                class="tab-btn px-6 py-2 rounded-lg font-medium transition-all text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700">
                <i class="fas fa-image mr-2"></i>Khung Hoạt Ảnh
            </button>
        </div>

        {{-- SECTIONS (imported from partials) --}}
        @include('admin.game.partials.badges-section')
        @include('admin.game.partials.challenges-section')
        @include('admin.game.partials.frames-section')
    </div>

    {{-- MODALS --}}
    @include('admin.game.partials.modals')

    {{-- SCRIPTS --}}
    @include('admin.game.partials.scripts')
@endsection
