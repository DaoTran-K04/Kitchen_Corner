@extends('layouts.admin')
@section('title', 'Trang Trí Lễ Hội')
@section('header', 'Quản Lý Trang Trí Lễ Hội')

@section('content')
    @php
        $currentTheme = session('admin_theme_override', 'auto');
        $themeSettings = session('theme_settings', []);
        
        $themes = [
            'auto' => ['name' => 'Tự động', 'icon' => '🔄', 'desc' => 'Theo thời gian thực', 'color' => 'gray'],
            'default' => ['name' => 'Mặc định', 'icon' => '📚', 'desc' => 'Không trang trí', 'color' => 'slate'],
            'christmas' => ['name' => 'Giáng Sinh', 'icon' => '🎄', 'desc' => 'Bông tuyết, cây thông (20-26/12)', 'color' => 'emerald'],
            'tet' => ['name' => 'Tết Nguyên Đán', 'icon' => '🧧', 'desc' => 'Hoa đào, phong bao (15/01-15/02)', 'color' => 'red'],
            'valentine' => ['name' => 'Valentine', 'icon' => '💕', 'desc' => 'Trái tim bay (12-15/02)', 'color' => 'pink'],
            'halloween' => ['name' => 'Halloween', 'icon' => '🎃', 'desc' => 'Bí ngô, dơi bay (25/10-01/11)', 'color' => 'orange'],
        ];
        
        // Available icons for each theme
        $themeIcons = [
            'christmas' => [
                'falling' => ['❄️' => 'Bông tuyết', '⭐' => 'Ngôi sao', '🌟' => 'Sao sáng', '✨' => 'Lấp lánh', '🔔' => 'Chuông'],
                'corner' => ['🎄' => 'Cây thông', '🎅' => 'Ông già Noel', '🎁' => 'Hộp quà', '⛄' => 'Người tuyết', '🦌' => 'Tuần lộc'],
            ],
            'tet' => [
                'falling' => ['🌸' => 'Hoa đào', '🌼' => 'Hoa mai', '🎊' => 'Confetti', '✨' => 'Lấp lánh', '🧨' => 'Pháo'],
                'corner' => ['🧧' => 'Bao lì xì', '🏮' => 'Đèn lồng', '🐉' => 'Rồng', '🎋' => 'Cây mai', '🍊' => 'Quýt'],
            ],
            'valentine' => [
                'falling' => ['💕' => 'Trái tim đôi', '❤️' => 'Trái tim', '💖' => 'Tim sáng', '💗' => 'Tim đang lớn', '🦋' => 'Bướm'],
                'corner' => ['🌹' => 'Hoa hồng', '💐' => 'Bó hoa', '🎀' => 'Nơ', '💝' => 'Hộp quà tim', '🕊️' => 'Chim bồ câu'],
            ],
            'halloween' => [
                'falling' => ['🦇' => 'Dơi', '🕷️' => 'Nhện', '👻' => 'Ma', '💀' => 'Đầu lâu', '🕸️' => 'Mạng nhện'],
                'corner' => ['🎃' => 'Bí ngô', '👻' => 'Ma', '🏚️' => 'Nhà ma', '🌙' => 'Trăng', '🦉' => 'Cú'],
            ],
        ];
        
        // Get current settings or defaults
        $getSettings = function($theme) use ($themeSettings) {
            $defaults = [
                'christmas' => ['falling' => '❄️', 'corner_left' => '🎄', 'corner_right' => '🎅', 'falling_count' => 12],
                'tet' => ['falling' => '🌸', 'corner_left' => '🏮', 'corner_right' => '🧧', 'falling_count' => 15],
                'valentine' => ['falling' => '💕', 'corner_left' => '🌹', 'corner_right' => '', 'falling_count' => 10],
                'halloween' => ['falling' => '🦇', 'corner_left' => '🎃', 'corner_right' => '👻', 'falling_count' => 8],
            ];
            return $themeSettings[$theme] ?? ($defaults[$theme] ?? []);
        };
    @endphp

    <div class="max-w-5xl mx-auto">
        {{-- Header Info --}}
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-6 mb-8 text-white shadow-lg">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center text-3xl">🎨</div>
                <div>
                    <h2 class="text-xl font-bold">Trang Trí Lễ Hội</h2>
                    <p class="text-white/80 text-sm mt-1">Chọn theme và tùy chỉnh icon hiển thị trên trang chủ</p>
                </div>
            </div>
        </div>

        {{-- Current Theme Status --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 mb-8 border border-gray-100 dark:border-slate-700 shadow-sm">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                <i class="fas fa-check-circle text-green-500"></i> Theme Đang Áp Dụng
            </h3>
            <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-slate-700 rounded-lg">
                <span class="text-4xl">{{ $themes[$currentTheme]['icon'] ?? '❓' }}</span>
                <div>
                    <h4 class="font-bold text-gray-800 dark:text-white">{{ $themes[$currentTheme]['name'] ?? 'Không xác định' }}</h4>
                    <p class="text-sm text-gray-500 dark:text-slate-400">{{ $themes[$currentTheme]['desc'] ?? '' }}</p>
                </div>
            </div>
        </div>

        {{-- Theme Selection Grid --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 mb-8 border border-gray-100 dark:border-slate-700 shadow-sm">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-6 flex items-center gap-2">
                <i class="fas fa-swatchbook text-indigo-500"></i> Chọn Theme
            </h3>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach($themes as $key => $theme)
                    <button onclick="setTheme('{{ $key }}')" 
                        class="theme-card group relative p-4 rounded-xl border-2 transition-all duration-300 text-center
                            {{ $currentTheme === $key 
                                ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/30 shadow-lg' 
                                : 'border-gray-200 dark:border-slate-600 hover:border-indigo-300 hover:shadow-md' }}">
                        @if($currentTheme === $key)
                            <div class="absolute top-2 right-2 w-5 h-5 bg-indigo-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-white text-[10px]"></i>
                            </div>
                        @endif
                        <div class="text-3xl mb-2">{{ $theme['icon'] }}</div>
                        <h4 class="font-bold text-gray-800 dark:text-white text-sm">{{ $theme['name'] }}</h4>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Theme Customization --}}
        @foreach(['christmas', 'tet', 'valentine', 'halloween'] as $themeKey)
            @php $settings = $getSettings($themeKey); @endphp
            <div id="customize-{{ $themeKey }}" class="bg-white dark:bg-slate-800 rounded-xl p-6 mb-6 border border-gray-100 dark:border-slate-700 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <span class="text-2xl">{{ $themes[$themeKey]['icon'] }}</span> 
                        Tùy Chỉnh {{ $themes[$themeKey]['name'] }}
                    </h3>
                    @if($currentTheme === $themeKey)
                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">ĐANG DÙNG</span>
                    @endif
                </div>

                <form id="form-{{ $themeKey }}" class="space-y-6">
                    {{-- Falling Icon (Multiple Selection) --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-3">
                            <i class="fas fa-snowflake mr-2 text-blue-500"></i> Icon Rơi/Bay <span class="text-xs font-normal text-gray-400">(có thể chọn nhiều)</span>
                        </label>
                        <div class="flex flex-wrap gap-2">
                            @foreach($themeIcons[$themeKey]['falling'] as $icon => $name)
                                @php
                                    $fallingIcons = is_array($settings['falling'] ?? null) ? $settings['falling'] : [($settings['falling'] ?? '')];
                                    $isChecked = in_array($icon, $fallingIcons);
                                @endphp
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="falling_{{ $themeKey }}[]" value="{{ $icon }}" 
                                        class="hidden peer" {{ $isChecked ? 'checked' : '' }}>
                                    <div class="w-14 h-14 rounded-xl border-2 border-gray-200 dark:border-slate-600 flex flex-col items-center justify-center gap-1 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/30 hover:border-indigo-300 transition-all">
                                        <span class="text-2xl">{{ $icon }}</span>
                                        <span class="text-[9px] text-gray-500 dark:text-slate-400">{{ $name }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Corner Icons --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Corner Left --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-3">
                                <i class="fas fa-arrow-left mr-2 text-green-500"></i> Icon Góc Trái
                            </label>
                            <div class="flex flex-wrap gap-2">
                                <label class="cursor-pointer">
                                    <input type="radio" name="corner_left_{{ $themeKey }}" value="" class="hidden peer" {{ empty($settings['corner_left']) ? 'checked' : '' }}>
                                    <div class="w-12 h-12 rounded-lg border-2 border-gray-200 dark:border-slate-600 flex items-center justify-center peer-checked:border-indigo-500 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/30 hover:border-indigo-300 transition-all">
                                        <i class="fas fa-ban text-gray-400"></i>
                                    </div>
                                </label>
                                @foreach($themeIcons[$themeKey]['corner'] as $icon => $name)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="corner_left_{{ $themeKey }}" value="{{ $icon }}" 
                                            class="hidden peer" {{ ($settings['corner_left'] ?? '') === $icon ? 'checked' : '' }}>
                                        <div class="w-12 h-12 rounded-lg border-2 border-gray-200 dark:border-slate-600 flex items-center justify-center peer-checked:border-indigo-500 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/30 hover:border-indigo-300 transition-all" title="{{ $name }}">
                                            <span class="text-xl">{{ $icon }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Corner Right --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-3">
                                <i class="fas fa-arrow-right mr-2 text-orange-500"></i> Icon Góc Phải
                            </label>
                            <div class="flex flex-wrap gap-2">
                                <label class="cursor-pointer">
                                    <input type="radio" name="corner_right_{{ $themeKey }}" value="" class="hidden peer" {{ empty($settings['corner_right']) ? 'checked' : '' }}>
                                    <div class="w-12 h-12 rounded-lg border-2 border-gray-200 dark:border-slate-600 flex items-center justify-center peer-checked:border-indigo-500 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/30 hover:border-indigo-300 transition-all">
                                        <i class="fas fa-ban text-gray-400"></i>
                                    </div>
                                </label>
                                @foreach($themeIcons[$themeKey]['corner'] as $icon => $name)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="corner_right_{{ $themeKey }}" value="{{ $icon }}" 
                                            class="hidden peer" {{ ($settings['corner_right'] ?? '') === $icon ? 'checked' : '' }}>
                                        <div class="w-12 h-12 rounded-lg border-2 border-gray-200 dark:border-slate-600 flex items-center justify-center peer-checked:border-indigo-500 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/30 hover:border-indigo-300 transition-all" title="{{ $name }}">
                                            <span class="text-xl">{{ $icon }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Falling Count --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-3">
                            <i class="fas fa-sort-numeric-up mr-2 text-purple-500"></i> Số lượng icon rơi: <span id="count-display-{{ $themeKey }}">{{ $settings['falling_count'] ?? 10 }}</span>
                        </label>
                        <input type="range" name="falling_count_{{ $themeKey }}" min="5" max="20" value="{{ $settings['falling_count'] ?? 10 }}"
                            class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-slate-700"
                            oninput="document.getElementById('count-display-{{ $themeKey }}').textContent = this.value">
                        <div class="flex justify-between text-xs text-gray-400 mt-1">
                            <span>Ít (5)</span>
                            <span>Nhiều (20)</span>
                        </div>
                    </div>

                    {{-- Save Button --}}
                    <button type="button" onclick="saveSettings('{{ $themeKey }}')"
                        class="w-full py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-bold rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i> Lưu Cài Đặt {{ $themes[$themeKey]['name'] }}
                    </button>
                </form>
            </div>
        @endforeach

        {{-- Help Info --}}
        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-100 dark:border-blue-800">
            <div class="flex gap-3">
                <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                <div class="text-sm text-blue-700 dark:text-blue-300">
                    <p class="font-medium mb-1">Lưu ý:</p>
                    <ul class="list-disc list-inside space-y-1 text-blue-600 dark:text-blue-400">
                        <li>Thay đổi sẽ được áp dụng ngay trên trang chủ</li>
                        <li>Người dùng vẫn có thể tự tắt/bật hiệu ứng trên thiết bị của họ</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Loading Overlay --}}
    <div id="theme-loading" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-xl flex items-center gap-4">
            <i class="fas fa-spinner fa-spin text-2xl text-indigo-500"></i>
            <span class="text-gray-700 dark:text-white font-medium">Đang cập nhật...</span>
        </div>
    </div>

    <script>
        function setTheme(theme) {
            const loading = document.getElementById('theme-loading');
            loading.classList.remove('hidden');

            fetch('{{ route("admin.set-theme") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ theme: theme })
            })
            .then(r => r.json())
            .then(data => { if (data.success) location.reload(); else { alert('Có lỗi!'); loading.classList.add('hidden'); } })
            .catch(() => { alert('Có lỗi!'); loading.classList.add('hidden'); });
        }

        function saveSettings(themeKey) {
            const loading = document.getElementById('theme-loading');
            loading.classList.remove('hidden');

            const form = document.getElementById('form-' + themeKey);
            const formData = new FormData(form);
            
            // Get all checked falling icons
            const fallingIcons = formData.getAll('falling_' + themeKey + '[]');
            
            const settings = {
                falling: fallingIcons.length > 0 ? fallingIcons : ['❄️'], // Default nếu không chọn
                corner_left: formData.get('corner_left_' + themeKey) || '',
                corner_right: formData.get('corner_right_' + themeKey) || '',
                falling_count: formData.get('falling_count_' + themeKey) || 10
            };

            fetch('{{ route("admin.theme.save-settings") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ theme: themeKey, settings: settings })
            })
            .then(r => r.json())
            .then(data => {
                loading.classList.add('hidden');
                if (data.success) {
                    // Show success toast
                    const toast = document.createElement('div');
                    toast.className = 'fixed bottom-6 right-6 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg z-50 flex items-center gap-2';
                    toast.innerHTML = '<i class="fas fa-check-circle"></i> Đã lưu cài đặt!';
                    document.body.appendChild(toast);
                    setTimeout(() => toast.remove(), 3000);
                } else {
                    alert('Có lỗi xảy ra!');
                }
            })
            .catch(() => { alert('Có lỗi!'); loading.classList.add('hidden'); });
        }
    </script>
@endsection
