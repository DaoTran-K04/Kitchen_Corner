@extends('layouts.app')

@section('title', 'Liên Hệ')

@section('content')
<div class="bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-sm overflow-hidden">
            
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
        </div>
    </div>
</div>
@endsection
