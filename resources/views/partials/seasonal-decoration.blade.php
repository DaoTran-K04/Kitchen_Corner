{{--
    Seasonal Decorations & Universal Theme Switcher Component
--}}

@php
    $now = now();
    $month = $now->month;
    $day = $now->day;
    
    // Kiểm tra theme được chọn từ session (cho cả user và admin chung 1 logic)
    $overrideTheme = session('site_theme', 'auto');
    
    if ($overrideTheme !== 'auto') {
        $initialTheme = $overrideTheme === 'default' ? null : $overrideTheme;
    } else {
        // Tự động
        $initialTheme = null;
        if ($month == 12 && $day >= 20 && $day <= 26) $initialTheme = 'christmas';
        elseif (($month == 1 && $day >= 15) || ($month == 2 && $day <= 15)) $initialTheme = 'tet';
        elseif ($month == 2 && $day >= 12 && $day <= 30) $initialTheme = 'valentine';
        elseif (($month == 10 && $day >= 25) || ($month == 11 && $day <= 2)) $initialTheme = 'halloween';
    }
    
    $defaultSettings = [
        'christmas' => ['falling' => '❄️', 'corner_left' => '🎄', 'corner_right' => '🎅', 'falling_count' => 12],
        'tet' => ['falling' => '🌸', 'corner_left' => '🏮', 'corner_right' => '🧧', 'falling_count' => 15],
        'valentine' => ['falling' => '💕', 'corner_left' => '🌹', 'corner_right' => '', 'falling_count' => 10],
        'halloween' => ['falling' => '🦇', 'corner_left' => '🎃', 'corner_right' => '👻', 'falling_count' => 8],
    ];
@endphp

{{-- 🎛️ UNIVERSAL THEME FAB COMPONENT --}}
<div id="theme-module" class="fixed bottom-6 left-6 z-[9999] font-sans">
    
    <!-- OPTIONS PANEL (Opens upward) -->
    <div id="theme-panel" class="absolute bottom-16 left-0 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden w-56 transform scale-95 opacity-0 pointer-events-none transition-all duration-300 origin-bottom-left">
        <!-- HEADER -->
        <div class="bg-gradient-to-r from-purple-500 to-indigo-500 text-white px-4 py-3 font-bold text-[13px] tracking-wider flex items-center justify-between">
            <span class="flex items-center gap-2"><i class="fas fa-palette"></i> CHỌN THEME</span>
            <i onclick="toggleThemePanel()" class="fas fa-times text-purple-200 hover:text-white cursor-pointer"></i>
        </div>
        
        <!-- OPTIONS -->
        <div class="flex flex-col py-2 max-h-[350px] overflow-y-auto custom-scrollbar">
            <button onclick="setGlobalTheme('auto')" class="w-full px-5 py-2.5 text-left hover:bg-purple-50 flex items-center gap-3 text-gray-700 transition-colors {{ $overrideTheme === 'auto' ? 'bg-purple-50/50' : '' }}">
                <div class="w-6 flex justify-center"><i class="fas fa-sync-alt text-blue-500"></i></div>
                <div class="flex-1"><span class="font-medium text-sm">Tự động</span><span class="text-[10px] text-gray-400 block pb-0">Theo thời gian</span></div>
                @if($overrideTheme === 'auto')<i class="fas fa-check text-indigo-500 text-xs"></i>@endif
            </button>
            <button onclick="setGlobalTheme('default')" class="w-full px-5 py-2.5 text-left hover:bg-purple-50 flex items-center gap-3 text-gray-700 transition-colors {{ $overrideTheme === 'default' ? 'bg-purple-50/50' : '' }}">
                <div class="w-6 flex justify-center"><i class="fas fa-book-open text-gray-500"></i></div>
                <div class="flex-1"><span class="font-medium text-sm">Mặc định</span><span class="text-[10px] text-gray-400 block pb-0">Giao diện gốc</span></div>
                @if($overrideTheme === 'default')<i class="fas fa-check text-indigo-500 text-xs"></i>@endif
            </button>
            <div class="border-t border-gray-100 my-1 mx-4"></div>
            <button onclick="setGlobalTheme('christmas')" class="w-full px-5 py-2.5 text-left hover:bg-purple-50 flex items-center gap-3 text-gray-700 transition-colors {{ $overrideTheme === 'christmas' ? 'bg-purple-50/50' : '' }}">
                <div class="w-6 flex justify-center text-lg">🎄</div> 
                <div class="flex-1"><span class="font-medium text-sm">Giáng Sinh</span></div>
                @if($overrideTheme === 'christmas')<i class="fas fa-check text-indigo-500 text-xs"></i>@endif
            </button>
            <button onclick="setGlobalTheme('tet')" class="w-full px-5 py-2.5 text-left hover:bg-purple-50 flex items-center gap-3 text-gray-700 transition-colors {{ $overrideTheme === 'tet' ? 'bg-purple-50/50' : '' }}">
                <div class="w-6 flex justify-center text-lg">🧧</div> 
                <div class="flex-1"><span class="font-medium text-sm">Tết Nguyên Đán</span></div>
                @if($overrideTheme === 'tet')<i class="fas fa-check text-indigo-500 text-xs"></i>@endif
            </button>
            <button onclick="setGlobalTheme('valentine')" class="w-full px-5 py-2.5 text-left hover:bg-purple-50 flex items-center gap-3 text-gray-700 transition-colors {{ $overrideTheme === 'valentine' ? 'bg-purple-50/50' : '' }}">
                <div class="w-6 flex justify-center text-lg">💕</div> 
                <div class="flex-1"><span class="font-medium text-sm">Valentine</span></div>
                @if($overrideTheme === 'valentine')<i class="fas fa-check text-indigo-500 text-xs"></i>@endif
            </button>
            <button onclick="setGlobalTheme('halloween')" class="w-full px-5 py-2.5 text-left hover:bg-purple-50 flex items-center gap-3 text-gray-700 transition-colors {{ $overrideTheme === 'halloween' ? 'bg-purple-50/50' : '' }}">
                <div class="w-6 flex justify-center text-lg">🎃</div> 
                <div class="flex-1"><span class="font-medium text-sm">Halloween</span></div>
                @if($overrideTheme === 'halloween')<i class="fas fa-check text-indigo-500 text-xs"></i>@endif
            </button>
        </div>
    </div>
    
    <!-- FLOATING ACTION BUTTON -->
    <button id="theme-fab-toggle" onclick="toggleThemePanel()" class="bg-gradient-to-br from-purple-500 to-indigo-500 text-white w-14 h-14 rounded-full flex items-center justify-center shadow-[0_8px_20px_rgba(139,92,246,0.3)] hover:scale-110 transition-transform duration-300">
        <i class="fas fa-palette text-xl"></i>
    </button>
</div>

<script>
    function toggleThemePanel() {
        const panel = document.getElementById('theme-panel');
        if (panel.classList.contains('opacity-0')) {
            panel.classList.remove('opacity-0', 'scale-95', 'pointer-events-none');
            panel.classList.add('opacity-100', 'scale-100', 'pointer-events-auto');
        } else {
            panel.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
            panel.classList.remove('opacity-100', 'scale-100', 'pointer-events-auto');
        }
    }
    
    function setGlobalTheme(theme) {
        fetch('{{ route("theme.switch") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '{{ csrf_token() }}' },
            body: JSON.stringify({ theme: theme })
        }).then(r => r.json()).then(data => {
            if (data.success) {
                const loadingHtml = '<div class="fixed inset-0 bg-white/80 backdrop-blur-sm z-[99999] flex items-center justify-center flex-col"><div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-purple-500 mb-4"></div><h2 class="text-xl font-bold text-gray-800 font-serif">Đang kích hoạt Theme mới...</h2></div>';
                document.body.insertAdjacentHTML('beforeend', loadingHtml);
                window.location.reload(); 
            }
        });
    }

    // Auto close if click outside
    document.addEventListener('click', function(e) {
        const module = document.getElementById('theme-module');
        const panel = document.getElementById('theme-panel');
        if (module && !module.contains(e.target) && !panel.classList.contains('opacity-0')) {
            toggleThemePanel();
        }
    });
</script>

{{-- DECORATIONS LAYER --}}
@if($initialTheme)
<div id="seasonal-decorations" class="fixed inset-0 pointer-events-none z-[9990] overflow-hidden">
    @php $settings = $defaultSettings[$initialTheme] ?? []; $falling = is_array($settings['falling']) ? $settings['falling'] : [$settings['falling'] ?? '✨']; @endphp
    
    <div class="theme-decoration absolute inset-0">
        <div class="petals" aria-hidden="true">
            @for($i = 0; $i < intval($settings['falling_count'] ?? 15); $i++)
                <div class="petal">{{ $falling[$i % count($falling)] }}</div>
            @endfor
        </div>
        @if(!empty($settings['corner_right']))
            <div class="absolute top-20 right-4 text-4xl opacity-70 animate-pulse">{{ $settings['corner_right'] }}</div>
        @endif
        @if(!empty($settings['corner_left']))
            <div class="absolute bottom-10 left-4 text-5xl opacity-50">{{ $settings['corner_left'] }}</div>
        @endif
    </div>
</div>
@endif

<style>
.petals{position:absolute;top:0;left:0;width:100%;height:100%}
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
@media(max-width:768px){.petal:nth-child(n+8){display:none}.petal{font-size:1rem!important}}
</style>
