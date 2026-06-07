@extends('layouts.app')

@section('title', 'Liên Hệ')

@section('content')
<div class="bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-5xl mx-auto bg-white rounded-2xl shadow-sm overflow-hidden">
            
            <div class="grid grid-cols-1 md:grid-cols-2">
                {{-- Thông tin liên hệ --}}
                <div class="bg-brand-green text-white p-10">
                    <h2 class="text-2xl font-bold mb-6 text-center">Thông tin liên hệ</h2>
                    <p class="text-white/80 mb-8 text-sm text-center">Nếu bạn có bất kỳ câu hỏi hoặc góp ý nào, đừng ngần ngại liên hệ với chúng tôi.</p>
                
                    <div class="space-y-6 max-w-md mx-auto">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm">Địa chỉ</h4>
                                <p class="text-xs text-white/80 mt-1">123 Phố Ẩm Thực, Quận Hải Châu, TP. Đà Nẵng</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm">Điện thoại</h4>
                                <p class="text-xs text-white/80 mt-1">1900 1234</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm">Email</h4>
                                <p class="text-xs text-white/80 mt-1">contact.gocbep@gmail.com</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 text-center">
                        <p class="text-sm font-bold mb-4">Theo dõi chúng tôi</p>
                        <div class="flex gap-4 justify-center">
                            <a href="https://www.facebook.com/profile.php?id=61585413759981" target="_blank" class="hover:text-brand-accent transition"><i class="fab fa-facebook text-xl"></i></a>
                            <a href="https://youtu.be/mKptA96QMZ0" target="_blank" class="hover:text-brand-accent transition"><i class="fab fa-youtube text-xl"></i></a>
                        </div>
                    </div>
                </div>

                {{-- Form Góp ý --}}
                <div class="p-10 bg-white">
                    <h2 class="text-2xl font-bold mb-6 text-gray-800">Gửi góp ý cho chúng tôi</h2>
                    
                    @if(session('success'))
                        <div class="bg-green-50 text-green-600 p-4 rounded-lg mb-6 flex items-start gap-3">
                            <i class="fas fa-check-circle mt-1"></i>
                            <p class="text-sm">{{ session('success') }}</p>
                        </div>
                    @endif

                    <form action="{{ route('feedback.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Họ và tên *</label>
                            <input type="text" name="name" value="{{ old('name', Auth::check() ? Auth::user()->name : '') }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-brand-green/50 outline-none transition">
                            @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input type="email" name="email" value="{{ old('email', Auth::check() ? Auth::user()->email : '') }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-brand-green/50 outline-none transition">
                            @error('email') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề *</label>
                            <input type="text" name="subject" value="{{ old('subject') }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-brand-green/50 outline-none transition">
                            @error('subject') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nội dung *</label>
                            <textarea name="message" rows="4" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-brand-green/50 outline-none transition resize-none">{{ old('message') }}</textarea>
                            @error('message') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" class="w-full py-3 bg-brand-green hover:bg-green-700 text-white rounded-lg font-bold transition flex items-center justify-center gap-2">
                            <i class="fas fa-paper-plane"></i> Gửi Góp Ý
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
