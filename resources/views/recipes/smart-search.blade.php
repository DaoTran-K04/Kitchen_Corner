@extends('layouts.app')

@section('title', 'Smart Search – Tìm theo nguyên liệu')

@section('content')
<div class="max-w-5xl mx-auto px-4 pt-24 lg:pt-32 pb-8">

    {{-- Header / Banner --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-brand-green via-[#2D4539] to-[#1e3a2f] rounded-[2.5rem] p-8 md:p-12 mb-10 text-white text-center shadow-deep border border-white/10 group">
        {{-- Decorative background elements --}}
        <div class="absolute -top-12 -right-12 w-64 h-64 bg-brand-accent/10 rounded-full blur-3xl pointer-events-none group-hover:scale-110 transition-transform duration-700"></div>
        <div class="absolute -bottom-10 -left-10 w-48 h-48 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-5 pointer-events-none"></div>

        <div class="relative z-10">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white/10 backdrop-blur-md border border-white/20 rounded-3xl mb-6 shadow-xl transform group-hover:rotate-6 transition-transform duration-500">
                <span class="text-4xl animate-float">🧊</span>
            </div>
            
            <h1 class="text-3xl md:text-5xl font-black font-serif mb-4 leading-tight">
                Tủ Lạnh <span class="text-brand-accent">Thông Minh</span>
            </h1>
            <p class="text-white/80 text-base md:text-lg mb-8 max-w-2xl mx-auto font-light leading-relaxed">
                Nhập những nguyên liệu bạn đang có, hệ thống sẽ gợi ý những món ăn 
                phù hợp nhất bằng thuật toán <span class="font-bold text-brand-accent italic">Jaccard Similarity</span>
            </p>
    
            <form method="GET" action="{{ route('recipes.smart-search') }}" class="max-w-2xl mx-auto">
                <div class="relative w-full mb-4">
                    <textarea name="ingredients" id="smartSearchTextarea" rows="2"
                        placeholder="VD: thịt bò, cà chua, hành tây, trứng, bắp cải..."
                        class="w-full px-6 py-4 rounded-2xl text-gray-800 focus:outline-none focus:ring-4 focus:ring-brand-accent/30 shadow-card text-base resize-none pr-14 border-0">{{ $ingredientInput }}</textarea>
                    <button type="button" id="clearBtn" onclick="clearIngredients()" 
                        class="absolute top-4 right-5 text-gray-400 hover:text-red-500 transition-colors {{ trim($ingredientInput) ? '' : 'hidden' }}"
                        title="Xóa tất cả">
                        <i class="fas fa-times-circle text-2xl"></i>
                    </button>
                </div>
                <button type="submit" class="group/btn relative w-full py-4 bg-brand-accent hover:bg-amber-300 text-brand-green font-black rounded-full transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:-translate-y-1 overflow-hidden">
                    <div class="absolute inset-0 bg-white/20 translate-y-full group-hover/btn:translate-y-0 transition-transform duration-300"></div>
                    <span class="relative z-10 text-lg flex items-center justify-center gap-2">
                        <i class="fas fa-magic"></i> Tìm món nấu được ngay!
                    </span>
                </button>
            </form>
        </div>
    </div>

    {{-- Nguyên liệu phổ biến --}}
    @if(!$ingredientInput && count($popularIngredients))
    <div class="bg-white/80 backdrop-blur-md rounded-[2rem] shadow-soft p-6 mb-10 border border-white/50 reveal">
        <h3 class="font-serif font-black text-gray-800 mb-4 flex items-center gap-3">
            <div class="w-8 h-8 bg-brand-accent/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-magic text-brand-brown text-sm"></i>
            </div>
            Gợi ý nguyên liệu phổ biến:
        </h3>
        <div class="flex flex-wrap gap-2.5" id="tagCloud">
            @foreach(array_slice($popularIngredients, 0, 20) as $ing)
            <button type="button" onclick="addIngTag('{{ $ing }}', this)"
                class="ingredient-tag group bg-brand-beige/50 hover:bg-brand-green border border-brand-green/10 hover:border-brand-green text-gray-700 hover:text-white text-xs px-4 py-2 rounded-xl transition-all duration-300 font-bold flex items-center gap-2">
                <span class="tag-text">{{ $ing }}</span>
                <i class="fas fa-plus group-hover:rotate-90 transition-transform text-[10px] opacity-50 remove-icon"></i>
            </button>
            @endforeach
            <button type="button" id="clearTagsRowBtn" onclick="clearIngredients()" class="hidden bg-red-50 hover:bg-red-500 border border-red-100 hover:border-red-500 text-red-600 hover:text-white text-xs px-4 py-2 rounded-xl transition-all font-black flex items-center gap-2">
                <i class="fas fa-trash-alt"></i> Xóa hết
            </button>
        </div>
    </div>
    @endif

    {{-- Kết quả --}}
    @if($ingredientInput)
    <div class="mb-8 flex items-center justify-between border-b border-gray-200 pb-4">
        <h2 class="text-2xl font-bold text-gray-800 font-serif">
            <i class="fas fa-utensils text-brand-green mr-2"></i>
            Kết quả gợi ý <span class="text-brand-green">({{ $results->count() }})</span>
        </h2>
        <div class="text-sm text-gray-500 italic">Dựa trên: <span class="font-bold text-gray-700">{{ $ingredientInput }}</span></div>
    </div>

    @if($results->isEmpty())
    <div class="text-center py-20 bg-white/50 backdrop-blur-sm rounded-[2.5rem] border border-dashed border-gray-300">
        <div class="text-6xl mb-6 animate-bounce">🥘</div>
        <p class="text-gray-600 text-xl font-serif font-bold">Ối! Không tìm thấy món nào phù hợp rồi.</p>
        <p class="text-gray-400 text-sm mt-3 max-w-sm mx-auto">Thử thêm nhiều nguyên liệu khác nhau hoặc sử dụng các gợi ý phổ biến ở trên nhé!</p>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($results as $result)
        @php $recipe = $result['recipe']; @endphp
        <a href="{{ route('recipes.show', $recipe->slug) }}"
            class="group bg-white rounded-3xl shadow-soft hover:shadow-card p-5 transition-all duration-500 border border-transparent hover:border-brand-green/20 relative overflow-hidden flex flex-col sm:flex-row gap-5">
            
            {{-- Circular Similarity Badge (Floating) --}}
            <div class="absolute top-4 right-4 z-10">
                <div class="relative w-12 h-12 bg-white rounded-full shadow-md flex items-center justify-center border border-gray-100">
                    <svg class="absolute inset-0 w-full h-full -rotate-90" viewBox="0 0 36 36">
                        <circle cx="18" cy="18" r="16" fill="none" stroke="#f3f4f6" stroke-width="2.5"/>
                        <circle cx="18" cy="18" r="16" fill="none"
                            stroke="{{ $result['similarity'] >= 50 ? '#9B2226' : ($result['similarity'] >= 30 ? '#CA6702' : '#6B7280') }}"
                            stroke-width="2.5"
                            stroke-dasharray="{{ $result['similarity'] }}, 100"
                            stroke-linecap="round"/>
                    </svg>
                    <div class="text-[10px] font-black {{ $result['similarity'] >= 50 ? 'text-brand-green' : 'text-gray-600' }}">
                        {{ $result['similarity'] }}%
                    </div>
                </div>
            </div>

            <div class="w-full sm:w-32 h-32 rounded-2xl overflow-hidden flex-shrink-0 shadow-inner">
                <img src="{{ $recipe->thumbnail }}"
                    alt="{{ $recipe->title }}"
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

                {{-- Ingredients check --}}
                <div class="flex flex-wrap gap-1.5">
                    @foreach($recipe->ingredients->take(6) as $ing)
                    @php
                        $userList = collect(explode(',', request('ingredients')))->map(fn($i)=>trim(mb_strtolower($i)));
                        $isMatch = $userList->contains(fn($u) => str_contains(mb_strtolower($ing->name), $u) || str_contains($u, mb_strtolower($ing->name)));
                    @endphp
                    <span class="text-[10px] px-2.5 py-1 rounded-lg transition-all
                        {{ $isMatch ? 'bg-brand-green text-white font-bold shadow-sm' : 'bg-gray-100 text-gray-400 font-medium' }}">
                        {{ $ing->name }}
                    </span>
                    @endforeach
                    @if($recipe->ingredients->count() > 6)
                    <span class="text-[10px] text-gray-400 px-2 py-1">+{{ $recipe->ingredients->count()-6 }}</span>
                    @endif
                </div>
            </div>
        </a>
        @endforeach
    </div>
    @endif
    @endif

</div>

@push('scripts')
<script>
let selectedTags = [];

document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('smartSearchTextarea');
    const clearBtn = document.getElementById('clearBtn');
    
    if (textarea && clearBtn) {
        textarea.addEventListener('input', function() {
            if (this.value.trim() !== '') {
                clearBtn.classList.remove('hidden');
            } else {
                clearBtn.classList.add('hidden');
            }
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
