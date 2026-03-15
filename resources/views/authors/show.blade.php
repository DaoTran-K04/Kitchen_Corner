@extends('layouts.app')

@section('title', $author->name . ' - Góc Sách')

@section('content')
<section class="container mx-auto px-4 py-12">
    {{-- Header tác giả --}}
    <div class="bg-white rounded-2xl shadow-sm p-6 md:p-8 mb-8">
        <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
            {{-- Avatar --}}
            @php
                $photo = $author->photo ?? null;
                $photoUrl = $photo ? (Str::startsWith($photo, 'http') ? $photo : asset('storage/' . $photo)) : null;
            @endphp
            <div class="w-32 h-32 rounded-full overflow-hidden bg-gray-100 flex-shrink-0">
                @if($photoUrl)
                    <img src="{{ $photoUrl }}" alt="{{ $author->name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-brand-green to-brand-accent text-white text-4xl font-bold">
                        {{ strtoupper(substr($author->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="flex-1 text-center md:text-left">
                <h1 class="text-3xl md:text-4xl font-bold font-serif text-gray-800 mb-2">{{ $author->name }}</h1>
                
                <div class="flex flex-wrap justify-center md:justify-start gap-4 text-sm text-gray-500 mb-4">
                    @if($author->nationality)
                        <span class="flex items-center gap-1">
                            <i class="fas fa-globe-asia"></i> {{ $author->nationality }}
                        </span>
                    @endif
                    @if($author->birth_year)
                        <span class="flex items-center gap-1">
                            <i class="fas fa-birthday-cake"></i> 
                            {{ $author->birth_year }}{{ $author->death_year ? ' - ' . $author->death_year : '' }}
                        </span>
                    @endif
                    <span class="flex items-center gap-1">
                        <i class="fas fa-book"></i> {{ $books->total() }} sách
                    </span>
                </div>

                @if($author->bio)
                    <p class="text-gray-600 leading-relaxed">{{ $author->bio }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Danh sách sách --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold font-serif text-gray-800 mb-1">Sách của {{ $author->name }}</h2>
        <p class="text-sm text-gray-500">Tổng cộng {{ $books->total() }} đầu sách</p>
    </div>

    @if($books->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($books as $book)
                <a href="{{ route('detail', $book->slug) }}" class="group">
                    <div class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 transform group-hover:-translate-y-1">
                        {{-- Cover --}}
                        <div class="aspect-[2/3] bg-gray-100 overflow-hidden">
                            @if($book->cover_image)
                                <img src="{{ Str::startsWith($book->cover_image, 'http') ? $book->cover_image : asset('storage/' . $book->cover_image) }}" 
                                    alt="{{ $book->title }}" 
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-200 to-gray-300">
                                    <i class="fas fa-book text-4xl text-gray-400"></i>
                                </div>
                            @endif
                        </div>
                        
                        {{-- Info --}}
                        <div class="p-3">
                            <h3 class="font-semibold text-gray-800 text-sm line-clamp-2 group-hover:text-brand-green transition-colors">{{ $book->title }}</h3>
                            @if($book->avg_rating)
                                <div class="flex items-center gap-1 mt-1">
                                    <i class="fas fa-star text-yellow-400 text-xs"></i>
                                    <span class="text-xs text-gray-500">{{ number_format($book->avg_rating, 1) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-8 flex justify-center">
            {{ $books->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl p-12 text-center">
            <i class="fas fa-book-open text-5xl text-gray-300 mb-4"></i>
            <p class="text-gray-500">Chưa có sách nào của tác giả này</p>
        </div>
    @endif
</section>
@endsection
