{{--
    Seasonal Decorations Component
    - Admin: Dropdown chọn theme đầy đủ
    - User/Guest: Nút toggle tắt/mở theme hiện tại
    - Supports custom icons from admin settings
--}}

@php
    $now = now();
    $month = $now->month;
    $day = $now->day;
    
    // Kiểm tra nếu admin đã chọn theme cụ thể (lưu trong session)
    $overrideTheme = session('admin_theme_override');
    $themeSettings = session('theme_settings', []);
    
    if ($overrideTheme && $overrideTheme !== 'auto') {
        $initialTheme = $overrideTheme === 'default' ? null : $overrideTheme;
    } else {
        // Xác định chủ đề tự động theo ngày
        $initialTheme = null;
        
        // 🎄 Giáng Sinh: 20/12 - 26/12
        if ($month == 12 && $day >= 20 && $day <= 26) {
            $initialTheme = 'christmas';
        }
        // 🧧 Tết Nguyên Đán: ~15/01 - 15/02
        elseif (($month == 1 && $day >= 15) || ($month == 2 && $day <= 15)) {
            $initialTheme = 'tet';
        }
        // 💕 Valentine: 12/02 - 15/02
        elseif ($month == 2 && $day >= 12 && $day <= 15) {
            $initialTheme = 'valentine';
        }
        // 🎃 Halloween: 25/10 - 01/11
        elseif (($month == 10 && $day >= 25) || ($month == 11 && $day == 1)) {
            $initialTheme = 'halloween';
        }
    }
    
    // Default settings for each theme
    $defaultSettings = [
        'christmas' => ['falling' => '❄️', 'corner_left' => '🎄', 'corner_right' => '🎅', 'falling_count' => 12],
        'tet' => ['falling' => '🌸', 'corner_left' => '🏮', 'corner_right' => '🧧', 'falling_count' => 15],
        'valentine' => ['falling' => '💕', 'corner_left' => '🌹', 'corner_right' => '', 'falling_count' => 10],
        'halloween' => ['falling' => '🦇', 'corner_left' => '🎃', 'corner_right' => '👻', 'falling_count' => 8],
    ];
    
    // Get settings for each theme (merge with defaults)
    $getSettings = function($theme) use ($themeSettings, $defaultSettings) {
        $defaults = $defaultSettings[$theme] ?? [];
        $custom = $themeSettings[$theme] ?? [];
        return array_merge($defaults, $custom);
    };
    
    // Theme icons mapping for toggle button
    $themeIcons = [
        'christmas' => '🎄',
        'tet' => '🧧',
        'valentine' => '💕',
        'halloween' => '🎃',
    ];
    $currentIcon = $themeIcons[$initialTheme] ?? '✨';
@endphp

{{-- ADMIN THEME SELECTOR (Full dropdown) --}}
@if(Auth::check() && Auth::user()->isAdmin())
<div id="admin-theme-selector" class="fixed bottom-6 left-10 z-[9999]">
    <button id="theme-toggle-btn" 
        class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 text-white rounded-full shadow-lg flex items-center justify-center hover:scale-110 transition-all duration-300"
        title="Chọn Theme Trang Trí">
        <i class="fas fa-palette text-lg"></i>
    </button>
    
    <div id="theme-dropdown" class="hidden absolute bottom-16 left-0 bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden min-w-[200px] transform scale-95 opacity-0 transition-all duration-200">
        <div class="px-4 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white">
            <p class="text-xs font-bold uppercase tracking-wider">🎨 Chọn Theme</p>
        </div>
        <div class="py-2">
            <button onclick="setTheme('auto')" data-theme="auto" class="theme-option w-full px-4 py-2.5 text-left hover:bg-gray-50 flex items-center gap-3 text-gray-700">
                <span class="text-lg">🔄</span>
                <div><span class="font-medium">Tự động</span><span class="text-xs text-gray-400 block">Theo thời gian</span></div>
                <i class="fas fa-check ml-auto text-indigo-600 hidden check-icon"></i>
            </button>
            <button onclick="setTheme('default')" data-theme="default" class="theme-option w-full px-4 py-2.5 text-left hover:bg-gray-50 flex items-center gap-3 text-gray-700">
                <span class="text-lg">📚</span>
                <div><span class="font-medium">Mặc định</span><span class="text-xs text-gray-400 block">Không trang trí</span></div>
                <i class="fas fa-check ml-auto text-indigo-600 hidden check-icon"></i>
            </button>
            <div class="border-t border-gray-100 my-1"></div>
            <button onclick="setTheme('christmas')" data-theme="christmas" class="theme-option w-full px-4 py-2.5 text-left hover:bg-gray-50 flex items-center gap-3 text-gray-700">
                <span class="text-lg">🎄</span>
                <div><span class="font-medium">Giáng Sinh</span></div>
                <i class="fas fa-check ml-auto text-indigo-600 hidden check-icon"></i>
            </button>
            <button onclick="setTheme('tet')" data-theme="tet" class="theme-option w-full px-4 py-2.5 text-left hover:bg-gray-50 flex items-center gap-3 text-gray-700">
                <span class="text-lg">🧧</span>
                <div><span class="font-medium">Tết Nguyên Đán</span></div>
                <i class="fas fa-check ml-auto text-indigo-600 hidden check-icon"></i>
            </button>
            <button onclick="setTheme('valentine')" data-theme="valentine" class="theme-option w-full px-4 py-2.5 text-left hover:bg-gray-50 flex items-center gap-3 text-gray-700">
                <span class="text-lg">💕</span>
                <div><span class="font-medium">Valentine</span></div>
                <i class="fas fa-check ml-auto text-indigo-600 hidden check-icon"></i>
            </button>
            <button onclick="setTheme('halloween')" data-theme="halloween" class="theme-option w-full px-4 py-2.5 text-left hover:bg-gray-50 flex items-center gap-3 text-gray-700">
                <span class="text-lg">🎃</span>
                <div><span class="font-medium">Halloween</span></div>
                <i class="fas fa-check ml-auto text-indigo-600 hidden check-icon"></i>
            </button>
        </div>
    </div>
</div>

<script>
    const autoTheme = '{{ $initialTheme ?? "default" }}';
    let currentTheme = '{{ $overrideTheme ?? "auto" }}';

    document.addEventListener('DOMContentLoaded', function() {
        updateThemeHighlight(currentTheme);
    });

    document.getElementById('theme-toggle-btn').addEventListener('click', function(e) {
        e.stopPropagation();
        const dropdown = document.getElementById('theme-dropdown');
        if (dropdown.classList.contains('hidden')) {
            dropdown.classList.remove('hidden');
            setTimeout(() => {
                dropdown.classList.remove('scale-95', 'opacity-0');
                dropdown.classList.add('scale-100', 'opacity-100');
            }, 10);
        } else {
            closeDropdown();
        }
    });

    function closeDropdown() {
        const dropdown = document.getElementById('theme-dropdown');
        dropdown.classList.add('scale-95', 'opacity-0');
        dropdown.classList.remove('scale-100', 'opacity-100');
        setTimeout(() => dropdown.classList.add('hidden'), 200);
    }

    document.addEventListener('click', function(e) {
        const selector = document.getElementById('admin-theme-selector');
        const dropdown = document.getElementById('theme-dropdown');
        if (selector && !selector.contains(e.target) && !dropdown.classList.contains('hidden')) {
            closeDropdown();
        }
    });

    function updateThemeHighlight(theme) {
        document.querySelectorAll('.theme-option').forEach(btn => {
            const btnTheme = btn.dataset.theme;
            const checkIcon = btn.querySelector('.check-icon');
            if (btnTheme === theme) {
                btn.classList.add('bg-indigo-50');
                checkIcon.classList.remove('hidden');
            } else {
                btn.classList.remove('bg-indigo-50');
                checkIcon.classList.add('hidden');
            }
        });
    }

    function applyTheme(theme) {
        document.querySelectorAll('.theme-decoration').forEach(el => el.classList.add('hidden'));
        let targetTheme = theme === 'auto' ? autoTheme : (theme === 'default' ? null : theme);
        if (targetTheme) {
            const themeEl = document.getElementById('decoration-' + targetTheme);
            if (themeEl) themeEl.classList.remove('hidden');
        }
    }

    function setTheme(theme) {
        currentTheme = theme;
        updateThemeHighlight(theme);
        applyTheme(theme);
        closeDropdown();

        fetch('{{ route("admin.set-theme") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ theme: theme })
        }).then(r => r.json()).then(data => {
            if (data.success) showThemeToast(theme);
        });
    }

    function showThemeToast(theme) {
        const names = { 'auto': 'Tự động', 'default': 'Mặc định', 'christmas': 'Giáng Sinh 🎄', 'tet': 'Tết 🧧', 'valentine': 'Valentine 💕', 'halloween': 'Halloween 🎃' };
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-20 left-6 bg-gray-800 text-white px-4 py-2 rounded-lg shadow-lg z-[10000] transform translate-y-4 opacity-0 transition-all duration-300';
        toast.innerHTML = `<i class="fas fa-check-circle text-green-400 mr-2"></i>Theme: <strong>${names[theme]}</strong>`;
        document.body.appendChild(toast);
        setTimeout(() => toast.classList.remove('translate-y-4', 'opacity-0'), 10);
        setTimeout(() => { toast.classList.add('translate-y-4', 'opacity-0'); setTimeout(() => toast.remove(), 300); }, 2000);
    }

    applyTheme(currentTheme);
</script>

{{-- USER/GUEST SIMPLE TOGGLE --}}
@else
@if($initialTheme)
<div id="user-theme-toggle" class="fixed bottom-6 left-10 z-[9999]">
    <button id="user-toggle-btn" 
        class="w-12 h-12 bg-gradient-to-br from-gray-600 to-gray-700 text-white rounded-full shadow-lg flex items-center justify-center hover:scale-110 transition-all duration-300 group"
        title="Tắt/Mở hiệu ứng trang trí">
        <span id="toggle-icon" class="text-xl">{{ $currentIcon }}</span>
    </button>
</div>

<script>
    const userActiveTheme = '{{ $initialTheme }}';
    let userThemeEnabled = localStorage.getItem('themeEnabled') !== 'false';

    document.addEventListener('DOMContentLoaded', function() {
        applyUserTheme();
        updateToggleButton();
    });

    document.getElementById('user-toggle-btn').addEventListener('click', function() {
        userThemeEnabled = !userThemeEnabled;
        localStorage.setItem('themeEnabled', userThemeEnabled);
        applyUserTheme();
        updateToggleButton();
        showUserToast(userThemeEnabled);
    });

    function applyUserTheme() {
        const themeEl = document.getElementById('decoration-' + userActiveTheme);
        if (themeEl) {
            if (userThemeEnabled) {
                themeEl.classList.remove('hidden');
            } else {
                themeEl.classList.add('hidden');
            }
        }
    }

    function updateToggleButton() {
        const btn = document.getElementById('user-toggle-btn');
        const icon = document.getElementById('toggle-icon');
        if (userThemeEnabled) {
            btn.classList.remove('from-gray-600', 'to-gray-700');
            btn.classList.add('from-emerald-500', 'to-teal-600');
            icon.textContent = '{{ $currentIcon }}';
        } else {
            btn.classList.remove('from-emerald-500', 'to-teal-600');
            btn.classList.add('from-gray-600', 'to-gray-700');
            icon.innerHTML = '<i class="fas fa-eye-slash text-base"></i>';
        }
    }

    function showUserToast(enabled) {
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-20 left-6 bg-gray-800 text-white px-4 py-2 rounded-lg shadow-lg z-[10000] transform translate-y-4 opacity-0 transition-all duration-300';
        toast.innerHTML = enabled 
            ? '<i class="fas fa-check-circle text-green-400 mr-2"></i>Đã bật hiệu ứng' 
            : '<i class="fas fa-eye-slash text-gray-400 mr-2"></i>Đã tắt hiệu ứng';
        document.body.appendChild(toast);
        setTimeout(() => toast.classList.remove('translate-y-4', 'opacity-0'), 10);
        setTimeout(() => { toast.classList.add('translate-y-4', 'opacity-0'); setTimeout(() => toast.remove(), 300); }, 2000);
    }
</script>
@endif
@endif

{{-- ALL THEME DECORATIONS with Custom Icons --}}
<div id="seasonal-decorations" class="fixed inset-0 pointer-events-none z-[9990] overflow-hidden">
    
    {{-- CHRISTMAS --}}
    @php 
        $christmasSettings = $getSettings('christmas'); 
        $christmasFalling = is_array($christmasSettings['falling'] ?? null) ? $christmasSettings['falling'] : [$christmasSettings['falling'] ?? '❄️'];
    @endphp
    <div id="decoration-christmas" class="theme-decoration absolute inset-0 {{ $initialTheme === 'christmas' ? '' : 'hidden' }}">
        <div class="snowflakes" aria-hidden="true">
            @for($i = 0; $i < intval($christmasSettings['falling_count'] ?? 12); $i++)
                <div class="snowflake"><div class="inner">{{ $christmasFalling[$i % count($christmasFalling)] }}</div></div>
            @endfor
        </div>
        @if(!empty($christmasSettings['corner_left']))
            <div class="absolute bottom-0 left-0 text-6xl opacity-50 transform -translate-x-1/4">{{ $christmasSettings['corner_left'] }}</div>
        @endif
        @if(!empty($christmasSettings['corner_right']))
            <div class="absolute top-20 right-4 text-4xl opacity-60 animate-bounce">{{ $christmasSettings['corner_right'] }}</div>
        @endif
    </div>

    {{-- TET --}}
    @php 
        $tetSettings = $getSettings('tet'); 
        $tetFalling = is_array($tetSettings['falling'] ?? null) ? $tetSettings['falling'] : [$tetSettings['falling'] ?? '🌸'];
    @endphp
    <div id="decoration-tet" class="theme-decoration absolute inset-0 {{ $initialTheme === 'tet' ? '' : 'hidden' }}">
        <div class="petals" aria-hidden="true">
            @for($i = 0; $i < intval($tetSettings['falling_count'] ?? 15); $i++)
                <div class="petal">{{ $tetFalling[$i % count($tetFalling)] }}</div>
            @endfor
        </div>
        @if(!empty($tetSettings['corner_right']))
            <div class="absolute top-20 right-4 text-4xl opacity-70 animate-pulse">{{ $tetSettings['corner_right'] }}</div>
        @endif
        @if(!empty($tetSettings['corner_left']))
            <div class="absolute bottom-10 left-4 text-5xl opacity-50">{{ $tetSettings['corner_left'] }}</div>
        @endif
    </div>

    {{-- VALENTINE --}}
    @php 
        $valentineSettings = $getSettings('valentine'); 
        $valentineFalling = is_array($valentineSettings['falling'] ?? null) ? $valentineSettings['falling'] : [$valentineSettings['falling'] ?? '💕'];
    @endphp
    <div id="decoration-valentine" class="theme-decoration absolute inset-0 {{ $initialTheme === 'valentine' ? '' : 'hidden' }}">
        <div class="hearts" aria-hidden="true">
            @for($i = 0; $i < intval($valentineSettings['falling_count'] ?? 10); $i++)
                <div class="heart">{{ $valentineFalling[$i % count($valentineFalling)] }}</div>
            @endfor
        </div>
        @if(!empty($valentineSettings['corner_left']))
            <div class="absolute bottom-0 right-0 text-5xl opacity-50 transform translate-x-1/4">{{ $valentineSettings['corner_left'] }}</div>
        @endif
    </div>

    {{-- HALLOWEEN --}}
    @php 
        $halloweenSettings = $getSettings('halloween'); 
        $halloweenFalling = is_array($halloweenSettings['falling'] ?? null) ? $halloweenSettings['falling'] : [$halloweenSettings['falling'] ?? '🦇'];
    @endphp
    <div id="decoration-halloween" class="theme-decoration absolute inset-0 {{ $initialTheme === 'halloween' ? '' : 'hidden' }}">
        <div class="bats" aria-hidden="true">
            @for($i = 0; $i < intval($halloweenSettings['falling_count'] ?? 8); $i++)
                <div class="bat">{{ $halloweenFalling[$i % count($halloweenFalling)] }}</div>
            @endfor
        </div>
        @if(!empty($halloweenSettings['corner_left']))
            <div class="absolute bottom-0 left-4 text-6xl opacity-60">{{ $halloweenSettings['corner_left'] }}</div>
        @endif
        @if(!empty($halloweenSettings['corner_right']))
            <div class="absolute top-20 right-4 text-4xl opacity-70">{{ $halloweenSettings['corner_right'] }}</div>
        @endif
    </div>

</div>

<style>
.snowflakes,.petals,.hearts,.bats{position:absolute;top:0;left:0;width:100%;height:100%}
.snowflake{position:absolute;top:-10%;color:#a8d4ff;font-size:1.5rem;animation:snowfall linear infinite;opacity:.9}
.snowflake:nth-child(1){left:5%;animation-duration:10s;animation-delay:0s;font-size:1.2rem}
.snowflake:nth-child(2){left:15%;animation-duration:12s;animation-delay:1s;font-size:1.8rem}
.snowflake:nth-child(3){left:25%;animation-duration:8s;animation-delay:2s;font-size:1rem}
.snowflake:nth-child(4){left:35%;animation-duration:14s;animation-delay:.5s;font-size:1.5rem}
.snowflake:nth-child(5){left:45%;animation-duration:9s;animation-delay:3s;font-size:1.3rem}
.snowflake:nth-child(6){left:55%;animation-duration:11s;animation-delay:1.5s;font-size:1.6rem}
.snowflake:nth-child(7){left:65%;animation-duration:13s;animation-delay:2.5s;font-size:1.1rem}
.snowflake:nth-child(8){left:75%;animation-duration:10s;animation-delay:.8s;font-size:1.4rem}
.snowflake:nth-child(9){left:85%;animation-duration:15s;animation-delay:3.5s;font-size:1.7rem}
.snowflake:nth-child(10){left:92%;animation-duration:7s;animation-delay:1.2s;font-size:1rem}
.snowflake:nth-child(11){left:10%;animation-duration:16s;animation-delay:4s;font-size:1.9rem}
.snowflake:nth-child(12){left:50%;animation-duration:11s;animation-delay:2s;font-size:1.2rem}
.snowflake:nth-child(n+13){left:calc(5% + (var(--i, 0) * 7%));animation-duration:calc(8s + (var(--i, 0) * 0.5s))}
@keyframes snowfall{0%{transform:translateY(0) rotate(0);opacity:.9}100%{transform:translateY(110vh) rotate(360deg);opacity:.5}}

.petal{position:absolute;top:-10%;font-size:1.2rem;animation:petalfall linear infinite;opacity:.95}
.petal:nth-child(1){left:8%;animation-duration:12s}.petal:nth-child(2){left:18%;animation-duration:10s;animation-delay:1s}
.petal:nth-child(3){left:28%;animation-duration:14s;animation-delay:2s}.petal:nth-child(4){left:38%;animation-duration:11s;animation-delay:.5s}
.petal:nth-child(5){left:48%;animation-duration:13s;animation-delay:3s}.petal:nth-child(6){left:58%;animation-duration:9s;animation-delay:1.5s}
.petal:nth-child(7){left:68%;animation-duration:15s;animation-delay:2.5s}.petal:nth-child(8){left:78%;animation-duration:10s;animation-delay:.8s}
.petal:nth-child(9){left:88%;animation-duration:12s;animation-delay:3.5s}.petal:nth-child(10){left:95%;animation-duration:8s;animation-delay:1.2s}
.petal:nth-child(11){left:3%;animation-duration:16s;animation-delay:4s}.petal:nth-child(12){left:33%;animation-duration:11s;animation-delay:2s}
.petal:nth-child(13){left:63%;animation-duration:13s;animation-delay:1s}.petal:nth-child(14){left:73%;animation-duration:14s;animation-delay:3s}
.petal:nth-child(15){left:83%;animation-duration:10s}.petal:nth-child(n+16){left:calc(5% + (var(--i, 0) * 5%));animation-duration:calc(9s + (var(--i, 0) * 0.4s))}
@keyframes petalfall{0%{transform:translateY(0) rotate(0) translateX(0);opacity:.95}50%{transform:translateY(50vh) rotate(180deg) translateX(30px)}100%{transform:translateY(110vh) rotate(360deg) translateX(-30px);opacity:.6}}

.heart{position:absolute;bottom:-10%;font-size:1.5rem;animation:heartrise linear infinite;opacity:.85}
.heart:nth-child(1){left:10%;animation-duration:12s}.heart:nth-child(2){left:20%;animation-duration:10s;animation-delay:1s}
.heart:nth-child(3){left:30%;animation-duration:14s;animation-delay:2s}.heart:nth-child(4){left:40%;animation-duration:11s;animation-delay:.5s}
.heart:nth-child(5){left:50%;animation-duration:13s;animation-delay:3s}.heart:nth-child(6){left:60%;animation-duration:9s;animation-delay:1.5s}
.heart:nth-child(7){left:70%;animation-duration:15s;animation-delay:2.5s}.heart:nth-child(8){left:80%;animation-duration:10s;animation-delay:.8s}
.heart:nth-child(9){left:90%;animation-duration:12s;animation-delay:3.5s}.heart:nth-child(10){left:5%;animation-duration:8s;animation-delay:1.2s}
@keyframes heartrise{0%{transform:translateY(0) scale(.8);opacity:.85}50%{transform:translateY(-50vh) scale(1.2);opacity:1}100%{transform:translateY(-110vh) scale(.5);opacity:0}}

.bat{position:absolute;font-size:1.8rem;animation:batfly linear infinite;opacity:.8}
.bat:nth-child(1){top:10%;left:-10%;animation-duration:8s}.bat:nth-child(2){top:20%;left:-10%;animation-duration:10s;animation-delay:1s}
.bat:nth-child(3){top:30%;left:-10%;animation-duration:7s;animation-delay:2s}.bat:nth-child(4){top:15%;left:-10%;animation-duration:9s;animation-delay:.5s}
.bat:nth-child(5){top:25%;left:-10%;animation-duration:11s;animation-delay:3s}.bat:nth-child(6){top:35%;left:-10%;animation-duration:8s;animation-delay:1.5s}
.bat:nth-child(7){top:5%;left:-10%;animation-duration:12s;animation-delay:2.5s}.bat:nth-child(8){top:40%;left:-10%;animation-duration:6s;animation-delay:.8s}
@keyframes batfly{0%{transform:translateX(0) translateY(0) rotate(0);opacity:.8}25%{transform:translateX(25vw) translateY(-20px) rotate(-5deg)}50%{transform:translateX(50vw) translateY(10px) rotate(5deg)}75%{transform:translateX(75vw) translateY(-15px) rotate(-3deg)}100%{transform:translateX(110vw) translateY(0) rotate(0);opacity:.5}}

@media(max-width:768px){.snowflake:nth-child(n+7),.petal:nth-child(n+8),.heart:nth-child(n+6),.bat:nth-child(n+5){display:none}.snowflake,.petal,.heart,.bat{font-size:1rem!important}}
</style>
