@extends('layouts.app')

@section('title', 'Smart Search – Tìm theo nguyên liệu')

@section('content')
<div class="max-w-5xl mx-auto px-4 pt-24 lg:pt-32 pb-8">

    {{-- Header / Banner --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-brand-green via-[#2D4539] to-[#1e3a2f] rounded-[2.5rem] p-6 md:p-12 mb-8 text-white text-center shadow-deep border border-white/10 group">
        {{-- Decorative background elements --}}
        <div class="absolute -top-12 -right-12 w-64 h-64 bg-brand-accent/10 rounded-full blur-3xl pointer-events-none group-hover:scale-110 transition-transform duration-700"></div>
        <div class="absolute -bottom-10 -left-10 w-48 h-48 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-5 pointer-events-none"></div>

        <div class="relative z-10">
            <div class="inline-flex items-center justify-center w-16 h-16 md:w-20 md:h-20 bg-white/10 backdrop-blur-md border border-white/20 rounded-3xl mb-4 md:mb-6 shadow-xl transform group-hover:rotate-6 transition-transform duration-500">
                <span class="text-3xl md:text-4xl animate-float">🧊</span>
            </div>
            
            <h1 class="text-3xl md:text-5xl font-black font-serif mb-3 md:mb-4 leading-tight">
                Tủ Lạnh <span class="text-brand-accent">Thông Minh AI</span>
            </h1>
            <p class="text-white/80 text-sm md:text-lg mb-6 max-w-2xl mx-auto font-light leading-relaxed px-2">
                @auth
                    Chào {{ Auth::user()->name }}, Tủ Lạnh AI đã phân tích khẩu vị của bạn và đề xuất những công thức phù hợp nhất.
                @else
                    Hệ thống AI sẽ tự động phân tích sở thích của bạn và đề xuất những công thức phù hợp nhất. Đăng nhập để trải nghiệm!
                @endauth
                Nhập thêm nguyên liệu đang có để tìm chính xác hơn!
            </p>
    
            <form method="GET" action="{{ route('recipes.smart-search') }}" class="max-w-2xl mx-auto sticky-mobile-search mb-12">
                <div class="relative w-full mb-3 md:mb-4">
                    <textarea name="ingredients" id="smartSearchTextarea" rows="2" autocomplete="off"
                        placeholder="VD: thịt bò, cà chua, hành tây..."
                        class="w-full px-5 py-3 md:px-6 md:py-4 rounded-2xl text-gray-800 focus:outline-none focus:ring-4 focus:ring-brand-accent/30 shadow-card text-base resize-none pr-12 border border-gray-200 transition-all">{{ $ingredientInput }}</textarea>
                    <button type="button" id="clearBtn" onclick="clearIngredients()" 
                        class="absolute top-3 right-4 md:top-4 md:right-5 text-gray-400 hover:text-red-500 transition-colors {{ trim($ingredientInput) ? '' : 'hidden' }}"
                        title="Xóa tất cả">
                        <i class="fas fa-times-circle text-xl md:text-2xl"></i>
                    </button>

                    {{-- Live Autocomplete Dropdown --}}
                    <div id="autocompleteDropdown" class="absolute z-50 left-0 right-0 top-full mt-2 bg-white rounded-2xl shadow-2xl border border-gray-100 hidden overflow-hidden flex-col max-h-[300px] overflow-y-auto text-left">
                        <ul id="autocompleteList" class="py-2 text-gray-800 text-base m-0">
                            {{-- JS will populate items here --}}
                        </ul>
                    </div>
                </div>
                <button type="submit" class="group/btn relative w-full py-3 md:py-4 bg-brand-accent hover:bg-amber-300 text-brand-green font-black rounded-full transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:-translate-y-1 overflow-hidden">
                    <div class="absolute inset-0 bg-white/20 translate-y-full group-hover/btn:translate-y-0 transition-transform duration-300"></div>
                    <span class="relative z-10 text-base md:text-lg flex items-center justify-center gap-2">
                        <i class="fas fa-magic"></i> Tìm món ăn ngay!
                    </span>
                </button>
            </form>

            {{-- Nguyên liệu phổ biến --}}
            @if(count($popularIngredients))
            <div class="bg-white/10 backdrop-blur-md rounded-[2rem] p-5 md:p-6 mb-12 border border-white/20 reveal overflow-hidden text-left">
                <h3 class="font-serif font-black text-white mb-4 flex items-center gap-3">
                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-star text-brand-accent text-sm"></i>
                    </div>
                    Nguyên liệu thường được tìm kiếm:
        </h3>
        
        {{-- Cuộn ngang trên Mobile --}}
        <div class="flex overflow-x-auto pb-4 -mx-5 px-5 md:mx-0 md:px-0 md:pb-0 md:flex-wrap gap-2 gap-y-3 hide-scroll-bar" id="tagCloud">
            @foreach(array_slice($popularIngredients, 0, 20) as $ing)
            <button type="button" onclick="addIngTag('{{ $ing }}', this)"
                class="ingredient-tag group whitespace-nowrap bg-brand-beige/50 hover:bg-brand-green border border-brand-green/10 hover:border-brand-green text-gray-700 hover:text-white text-xs px-4 py-2 rounded-xl transition-all duration-300 font-bold flex items-center gap-2 flex-shrink-0">
                <span class="tag-text">{{ $ing }}</span>
                <i class="fas fa-plus group-hover:rotate-90 transition-transform text-[10px] opacity-50 remove-icon"></i>
            </button>
            @endforeach
            <button type="button" id="clearTagsRowBtn" onclick="clearIngredients()" class="hidden whitespace-nowrap bg-red-50 hover:bg-red-500 border border-red-100 hover:border-red-500 text-red-600 hover:text-white text-xs px-4 py-2 rounded-xl transition-all font-black flex items-center gap-2 flex-shrink-0">
                <i class="fas fa-trash-alt"></i> Xóa hết
            </button>
        </div>
    </div>
    @endif

            {{-- Dành Riêng Cho Bạn (AI Suggestions) - Luôn hiển thị ở trên --}}
            @if(isset($aiResults) && $aiResults->isNotEmpty())
            <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-white/20 pb-4 mb-6 gap-2 text-left">
                <h2 class="text-xl md:text-2xl font-bold text-white font-serif flex items-center">
                    <div class="w-10 h-10 rounded-full bg-brand-accent flex items-center justify-center text-brand-green mr-3 shadow-md">
                        <i class="fas fa-sparkles"></i>
                    </div>
                    Dành Riêng Cho Bạn
                </h2>
                <div class="text-xs md:text-sm text-white/90 bg-white/10 px-3 py-1.5 rounded-full inline-block self-start md:self-auto border border-white/20">
                    Dựa trên phân tích thói quen và sở thích của bạn
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-left">
        @foreach($aiResults as $result)
        @php $recipe = $result['recipe']; @endphp
        <a href="{{ route('recipes.show', $recipe->slug) }}"
            class="group bg-white/95 backdrop-blur-md rounded-3xl shadow-soft hover:shadow-card p-5 transition-all duration-500 border border-transparent hover:border-brand-green/30 relative overflow-hidden flex flex-col sm:flex-row gap-5">
            
            <div class="absolute top-4 right-4 z-10">
                <div class="relative w-14 h-14 bg-white shadow-md rounded-full flex items-center justify-center border border-gray-100 group-hover:scale-110 transition-transform">
                    <svg class="absolute inset-0 w-full h-full -rotate-90" viewBox="0 0 36 36">
                        <circle cx="18" cy="18" r="16" fill="none" stroke="#f3f4f6" stroke-width="3"/>
                        <circle cx="18" cy="18" r="16" fill="none"
                            stroke="{{ $result['similarity'] >= 70 ? '#10B981' : ($result['similarity'] >= 40 ? '#F59E0B' : '#6B7280') }}"
                            stroke-width="3" stroke-dasharray="{{ $result['similarity'] }}, 100" stroke-linecap="round"/>
                    </svg>
                    <div class="text-[11px] font-black {{ $result['similarity'] >= 70 ? 'text-green-500' : 'text-gray-700' }} flex flex-col items-center leading-none">
                        <span>{{ $result['similarity'] }}</span>
                        <span class="text-[8px] uppercase font-bold text-gray-400">Match</span>
                    </div>
                </div>
            </div>

            <div class="w-full sm:w-32 h-32 rounded-2xl overflow-hidden flex-shrink-0 shadow-inner">
                <img loading="lazy" src="{{ $recipe->thumbnail }}" alt="{{ $recipe->title }}"
                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                    onerror="this.src='https://images.unsplash.com/photo-1490645935967-10de6ba17061?w=300'">
            </div>

            <div class="flex-1 min-w-0 flex flex-col justify-between">
                <div>
                    <h3 class="font-serif font-black text-gray-800 group-hover:text-brand-green transition-colors text-lg line-clamp-2 mb-2 leading-tight">
                        {{ $recipe->title }}
                    </h3>
                    <div class="flex flex-wrap gap-2 mb-3">
                        @if($recipe->category)
                        <span class="text-[10px] bg-brand-green/10 text-brand-green px-2 py-0.5 rounded-full font-bold uppercase tracking-wider">{{ $recipe->category->name }}</span>
                        @endif
                        <span class="text-[10px] text-gray-500 font-bold uppercase tracking-widest flex items-center gap-1">
                            <i class="far fa-clock"></i> {{ $recipe->cooking_time }}'
                        </span>
                    </div>
                </div>

                <div class="flex flex-wrap gap-1.5 mt-3 pt-3 border-t border-gray-100">
                    <span class="text-[10px] px-2.5 py-1 rounded-lg bg-blue-50 text-blue-600 font-bold border border-blue-100 flex items-center gap-1">
                        <i class="fas fa-thumbs-up"></i> AI Đề Xuất
                    </span>
                    <span class="text-[10px] px-2.5 py-1 rounded-lg bg-gray-50 text-gray-500 border border-gray-100 flex items-center gap-1">
                        <i class="fas fa-eye"></i> {{ number_format($recipe->view_count) }} lượt xem
                    </span>
                </div>
            </div>
        </a>
        @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Kết quả Tìm Kiếm (Chỉ hiện khi có input) --}}
    @if(isset($ingredientInput) && trim($ingredientInput) !== '')
    <div id="search-results-section" class="mb-8 flex flex-col md:flex-row md:items-center justify-between border-b border-gray-200 pb-4 gap-2 scroll-mt-24">
        <h2 class="text-xl md:text-2xl font-bold text-gray-800 font-serif flex items-center">
            <i class="fas fa-utensils text-brand-green mr-3"></i>
            Kết quả gợi ý <span class="text-brand-green ml-2">({{ $searchResults->count() }})</span>
        </h2>
        <div class="text-sm text-gray-500 italic">Dựa trên: <span class="font-bold text-gray-700 bg-brand-green/10 text-brand-green px-2 py-1 rounded-md">{{ $ingredientInput }}</span></div>
    </div>



    @if($searchResults->isEmpty())
    <div class="text-center py-20 bg-white/50 backdrop-blur-sm rounded-[2.5rem] border border-dashed border-gray-300">
        <div class="text-6xl mb-6 animate-bounce">🥘</div>
        <p class="text-gray-600 text-xl font-serif font-bold">Ối! Không tìm thấy món nào phù hợp rồi.</p>
        <p class="text-gray-400 text-sm mt-3 max-w-sm mx-auto">Thử thêm nhiều nguyên liệu khác nhau hoặc sử dụng các gợi ý phổ biến ở trên nhé!</p>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($searchResults as $result)
        @php $recipe = $result['recipe']; @endphp
        <a href="{{ route('recipes.show', $recipe->slug) }}"
            class="group bg-white rounded-3xl shadow-soft hover:shadow-card p-5 transition-all duration-500 border border-transparent hover:border-brand-green/20 relative overflow-hidden flex flex-col sm:flex-row gap-5">
            
            <div class="absolute top-4 right-4 z-10">
                <div class="relative w-14 h-14 bg-white/90 backdrop-blur-sm rounded-full shadow-lg flex items-center justify-center border border-gray-100 group-hover:scale-110 transition-transform">
                    <svg class="absolute inset-0 w-full h-full -rotate-90" viewBox="0 0 36 36">
                        <circle cx="18" cy="18" r="16" fill="none" stroke="#f3f4f6" stroke-width="3"/>
                        <circle cx="18" cy="18" r="16" fill="none"
                            stroke="{{ $result['similarity'] >= 70 ? '#10B981' : ($result['similarity'] >= 40 ? '#F59E0B' : '#6B7280') }}"
                            stroke-width="3" stroke-dasharray="{{ $result['similarity'] }}, 100" stroke-linecap="round"/>
                    </svg>
                    <div class="text-[11px] font-black {{ $result['similarity'] >= 70 ? 'text-green-500' : 'text-gray-700' }} flex flex-col items-center leading-none">
                        <span>{{ $result['similarity'] }}</span>
                        <span class="text-[8px] uppercase font-bold text-gray-400">Match</span>
                    </div>
                </div>
            </div>

            <div class="w-full sm:w-32 h-32 rounded-2xl overflow-hidden flex-shrink-0 shadow-inner">
                <img loading="lazy" src="{{ $recipe->thumbnail }}" alt="{{ $recipe->title }}"
                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                    onerror="this.src='https://images.unsplash.com/photo-1490645935967-10de6ba17061?w=300'">
            </div>

            <div class="flex-1 min-w-0 flex flex-col justify-between">
                <div>
                    <h3 class="font-serif font-black text-gray-800 group-hover:text-brand-green transition-colors text-lg line-clamp-2 mb-2 leading-tight">
                        {{ $recipe->title }}
                    </h3>
                    <div class="flex flex-wrap gap-2 mb-3">
                        @if($recipe->category)
                        <span class="text-[10px] bg-brand-green/10 text-brand-green px-2 py-0.5 rounded-full font-bold uppercase tracking-wider">{{ $recipe->category->name }}</span>
                        @endif
                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest flex items-center gap-1">
                            <i class="far fa-clock"></i> {{ $recipe->cooking_time }}'
                        </span>
                        <span class="text-[10px] text-brand-green font-bold uppercase tracking-widest flex items-center gap-1">
                            <i class="fas fa-check-double"></i> Khớp {{ $result['matches'] }} nguyên liệu
                        </span>
                    </div>
                </div>

                <div class="flex flex-wrap gap-1.5 mt-3 pt-3 border-t border-gray-100">
                    @foreach($recipe->ingredients->take(5) as $ing)
                    @php
                        $userList = collect(explode(',', request('ingredients')))->map(fn($i)=>trim(mb_strtolower($i)));
                        $isMatch = $userList->contains(fn($u) => str_contains(mb_strtolower($ing->name), $u) || str_contains($u, mb_strtolower($ing->name)));
                    @endphp
                    <span class="text-[10px] px-2.5 py-1 rounded-lg transition-all
                        {{ $isMatch ? 'bg-brand-green text-white font-bold shadow-sm ring-1 ring-brand-green/30' : 'bg-gray-100 text-gray-500' }}">
                        {{ $ing->name }}
                    </span>
                    @endforeach
                    @if($recipe->ingredients->count() > 5)
                    <span class="text-[10px] text-gray-400 px-2 py-1 bg-gray-50 rounded-lg border border-gray-100">+{{ $recipe->ingredients->count()-5 }}</span>
                    @endif
                </div>
            </div>
        </a>
        @endforeach
    </div>
    </div>
    @endif

    {{-- Khám Phá Thêm Từ Internet (External Results) --}}
    @if(isset($externalResults) && count($externalResults) > 0)
    <div class="mt-12 pt-8 border-t border-gray-200">
        <h2 class="text-xl md:text-2xl font-bold text-gray-800 font-serif flex items-center mb-6">
            <i class="fas fa-globe-asia text-blue-500 mr-3"></i>
            Khám Phá Thêm Từ Internet
            <span class="ml-3 text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full font-bold uppercase">Nguồn mở rộng</span>
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @php
                $ultimateFallback = \App\Models\Recipe::whereNotNull('image')->inRandomOrder()->value('image') ?? 'https://foodish-api.com/images/burger/burger87.jpg';
            @endphp
            @foreach($externalResults as $ext)
            <a href="{{ $ext['url'] }}" target="_blank" class="block group bg-white rounded-2xl shadow-sm hover:shadow-md border border-gray-100 hover:border-blue-200 transition-all overflow-hidden flex flex-col h-full">
                <div class="h-32 w-full overflow-hidden relative bg-gray-100 flex items-center justify-center">
                    <img src="{{ $ext['thumbnail'] }}" alt="{{ $ext['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" onerror="this.onerror=null; this.src='{{ $ultimateFallback }}';">
                    <div class="absolute top-2 left-2 bg-white/90 backdrop-blur-sm px-2 py-1 rounded flex items-center gap-1.5 shadow-sm">
                        <img src="{{ $ext['favicon'] }}" class="w-3 h-3 rounded-sm" alt="favicon">
                        <span class="text-[10px] font-bold text-gray-700">{{ $ext['source'] }}</span>
                    </div>
                </div>
                <div class="p-4 flex flex-col flex-grow">
                    <h4 class="font-serif font-bold text-gray-800 group-hover:text-blue-600 line-clamp-2 mb-2 text-sm">{{ $ext['title'] }}</h4>
                    <p class="text-xs text-gray-500 line-clamp-3 leading-relaxed">{{ $ext['snippet'] }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    @endif

</div>

<style>
    /* Ẩn scrollbar trên Chrome, Safari và Opera */
    .hide-scroll-bar::-webkit-scrollbar {
        display: none;
    }
    /* Ẩn scrollbar trên IE, Edge và Firefox */
    .hide-scroll-bar {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }
</style>

@push('scripts')
<script>
let selectedTags = [];

document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('smartSearchTextarea');
    const clearBtn = document.getElementById('clearBtn');
    
    // Tự động cuộn xuống phần kết quả tìm kiếm nếu có
    const searchResultsSection = document.getElementById('search-results-section');
    if (searchResultsSection) {
        setTimeout(() => {
            searchResultsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 150);
    }

    if (textarea && clearBtn) {
        let debounceTimer;
        const dropdown = document.getElementById('autocompleteDropdown');
        const autocompleteList = document.getElementById('autocompleteList');

        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!textarea.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });

        textarea.addEventListener('input', function() {
            const rawVal = this.value;
            // Get the last part after comma, or the whole text
            const parts = rawVal.split(',');
            const currentSearch = parts[parts.length - 1].trim();

            if (rawVal.trim() !== '') {
                clearBtn.classList.remove('hidden');
            } else {
                clearBtn.classList.add('hidden');
                dropdown.classList.add('hidden');
                return;
            }

            clearTimeout(debounceTimer);
            
            if (currentSearch.length < 2) {
                dropdown.classList.add('hidden');
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch(`/ajax-search?keyword=${encodeURIComponent(currentSearch)}`)
                    .then(res => res.json())
                    .then(data => {
                        autocompleteList.innerHTML = '';
                        
                        // Default search option
                        const searchOption = document.createElement('li');
                        searchOption.className = 'px-5 py-3 hover:bg-gray-50 cursor-pointer flex items-center gap-3 border-b border-gray-100 text-brand-green font-bold';
                        searchOption.innerHTML = `<i class="fas fa-search text-gray-400"></i> Tìm kiếm tất cả món có: "${currentSearch}"`;
                        searchOption.onclick = function() {
                            textarea.closest('form').submit();
                        };
                        autocompleteList.appendChild(searchOption);

                        // Recipe results
                        if (data && data.length > 0) {
                            data.forEach(recipe => {
                                const li = document.createElement('li');
                                li.className = 'px-5 py-2 hover:bg-gray-50 cursor-pointer flex items-center gap-3 transition-colors';
                                li.innerHTML = `
                                    <img src="${recipe.image || 'https://images.unsplash.com/photo-1490645935967-10de6ba17061?w=100'}" class="w-10 h-10 rounded-lg object-cover shadow-sm flex-shrink-0" onerror="this.src='https://images.unsplash.com/photo-1490645935967-10de6ba17061?w=100'">
                                    <div class="flex-1 min-w-0">
                                        <div class="text-gray-800 font-bold truncate">${recipe.title}</div>
                                        <div class="text-xs text-gray-500 truncate"><i class="far fa-clock"></i> ${recipe.cooking_time || 0} phút</div>
                                    </div>
                                `;
                                li.onclick = function() {
                                    window.location.href = `/cong-thuc/${recipe.slug}`;
                                };
                                autocompleteList.appendChild(li);
                            });
                        }

                        dropdown.classList.remove('hidden');
                    })
                    .catch(err => console.error(err));
            }, 300);
        });
    }
});

function clearIngredients() {
    const textarea = document.getElementById('smartSearchTextarea');
    const clearBtn = document.getElementById('clearBtn');
    
    if (textarea) {
        textarea.value = '';
        textarea.focus();
    }
    if (clearBtn) {
        clearBtn.classList.add('hidden');
    }
    
    selectedTags = [];
    document.querySelectorAll('.ingredient-tag').forEach(btn => {
        btn.classList.remove('bg-brand-green', 'text-white', 'border-brand-green');
        btn.classList.add('bg-brand-beige/50', 'text-gray-700');
        const icon = btn.querySelector('.remove-icon');
        if (icon) {
            icon.classList.remove('fa-times', 'rotate-90');
            icon.classList.add('fa-plus');
        }
    });

    const rowBtn = document.getElementById('clearTagsRowBtn');
    if (rowBtn) rowBtn.classList.add('hidden');
}

function addIngTag(name, btnElement = null) {
    const textarea = document.getElementById('smartSearchTextarea');
    
    // Lấy lại danh sách tag đang có trong textarea
    if (textarea.value.trim() !== '') {
        selectedTags = textarea.value.split(',').map(tag => tag.trim()).filter(tag => tag !== '');
    } else {
        selectedTags = [];
    }

    const index = selectedTags.indexOf(name);
    
    if (index > -1) {
        // Đã chọn -> Bỏ chọn
        selectedTags.splice(index, 1);
        
        document.querySelectorAll('.ingredient-tag').forEach(btn => {
            const span = btn.querySelector('.tag-text');
            if (span && span.textContent.trim() === name) {
                btn.classList.remove('bg-brand-green', 'text-white', 'border-brand-green');
                btn.classList.add('bg-brand-beige/50', 'text-gray-700');
                const icon = btn.querySelector('.remove-icon');
                if (icon) {
                    icon.classList.remove('fa-times', 'rotate-90');
                    icon.classList.add('fa-plus');
                }
            }
        });
    } else {
        // Chưa chọn -> Thêm vào
        selectedTags.push(name);
        
        document.querySelectorAll('.ingredient-tag').forEach(btn => {
            const span = btn.querySelector('.tag-text');
            if (span && span.textContent.trim() === name) {
                btn.classList.add('bg-brand-green', 'text-white', 'border-brand-green');
                btn.classList.remove('bg-brand-beige/50', 'text-gray-700');
                const icon = btn.querySelector('.remove-icon');
                if (icon) {
                    icon.classList.remove('fa-plus');
                    icon.classList.add('fa-times', 'rotate-90');
                }
            }
        });
    }

    textarea.value = selectedTags.join(', ');
    
    // Toggle X button
    const clearBtn = document.getElementById('clearBtn');
    const rowBtn = document.getElementById('clearTagsRowBtn');
    
    if (textarea.value.trim() !== '') {
        if (clearBtn) clearBtn.classList.remove('hidden');
        if (rowBtn) rowBtn.classList.remove('hidden');
    } else {
        if (clearBtn) clearBtn.classList.add('hidden');
        if (rowBtn) rowBtn.classList.add('hidden');
    }
}

// Initial highlight text matching tags on page load
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('smartSearchTextarea');
    if (textarea && textarea.value.trim() !== '') {
        const initTags = textarea.value.split(',').map(tag => tag.trim()).filter(tag => tag !== '');
        if (initTags.length > 0) {
            const rowBtn = document.getElementById('clearTagsRowBtn');
            if (rowBtn) rowBtn.classList.remove('hidden');

            document.querySelectorAll('.ingredient-tag').forEach(btn => {
                const span = btn.querySelector('.tag-text');
                if (span && initTags.includes(span.textContent.trim())) {
                    btn.classList.add('bg-brand-green', 'text-white', 'border-brand-green');
                    btn.classList.remove('bg-brand-beige/50', 'text-gray-700');
                    const icon = btn.querySelector('.remove-icon');
                    if (icon) {
                        icon.classList.remove('fa-plus');
                        icon.classList.add('fa-times', 'rotate-90');
                    }
                }
            });
        }
    }
});
</script>
@endpush
@endsection
