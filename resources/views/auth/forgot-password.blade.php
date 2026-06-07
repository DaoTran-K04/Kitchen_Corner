<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên Mật Khẩu - Góc Bếp</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600;1,700&family=Nunito+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'brand-green': '#9B2226',
                        'brand-cream': '#FAFAF8',
                        'brand-accent': '#E85D04'
                    },
                    fontFamily: {
                        sans: ['Nunito Sans', 'sans-serif'],
                        serif: ['Playfair Display', 'serif']
                    }
                }
            }
        }
    </script>
</head>

<body class="font-sans min-h-screen flex items-center justify-center p-4 relative bg-gray-50">
    <!-- Beautiful Full Background Image -->
    <div class="absolute inset-0 z-0">
        <img loading="lazy" src="{{ asset('images/auth/kitchen_1.png') }}" alt="Kitchen Background" class="w-full h-full object-cover" />
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    </div>

    <div class="max-w-md w-full bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl overflow-hidden border border-white/20 relative z-10">
        <div class="p-8 text-center text-white relative overflow-hidden">
            <!-- Header Image -->
            <div class="absolute inset-0 z-0">
                <img loading="lazy" src="{{ asset('images/auth/kitchen_2.png') }}" alt="Cooking" class="w-full h-full object-cover" />
                <div class="absolute inset-0 bg-brand-green/80"></div>
            </div>
            
            <div class="relative z-10">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center mx-auto mb-4 border border-white/30 shadow-inner">
                    <i class="fas fa-key text-2xl drop-shadow-sm"></i>
                </div>
                <h2 class="text-2xl font-serif font-bold tracking-wide drop-shadow-md">Khôi Phục Mật Khẩu</h2>
                <p class="text-white/90 text-sm mt-2 font-light">Đừng lo lắng, chúng tôi sẽ giúp bạn lấy lại quyền truy cập Góc Bếp.</p>
            </div>
        </div>

        <div class="p-8 md:p-10 bg-white">
            @if (session('status'))
                <div class="bg-green-50 text-green-700 border-l-4 border-green-500 text-sm p-4 rounded-r-lg mb-6 flex items-start gap-3 shadow-sm">
                    <i class="fas fa-check-circle mt-0.5 text-green-500"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 text-red-600 border-l-4 border-red-500 text-sm p-4 rounded-r-lg mb-6 flex items-start gap-3 shadow-sm">
                    <i class="fas fa-exclamation-circle mt-0.5"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-8">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Địa chỉ Email liên kết</label>
                    @auth
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" name="email" 
                                class="w-full pl-11 pr-4 py-3 bg-gray-100 border border-gray-200 rounded-xl text-gray-500 cursor-not-allowed shadow-inner" 
                                value="{{ Auth::user()->email }}" readonly>
                        </div>
                        <p class="text-xs text-gray-500 mt-2 flex items-center gap-1.5">
                            <i class="fas fa-info-circle text-brand-accent"></i>
                            Bạn đang đặt lại mật khẩu cho tài khoản này
                        </p>
                    @else
                        @php
                            $prefillEmail = request()->query('email');
                        @endphp
                        
                        @if($prefillEmail)
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input type="email" name="email" 
                                    class="w-full pl-11 pr-4 py-3 bg-gray-100 border border-gray-200 rounded-xl text-gray-500 cursor-not-allowed shadow-inner" 
                                    value="{{ $prefillEmail }}" readonly>
                            </div>
                            <p class="text-xs text-gray-500 mt-2 flex items-center gap-1.5">
                                <i class="fas fa-info-circle text-brand-accent"></i>
                                Bạn đang đặt lại mật khẩu cho tài khoản này
                            </p>
                        @else
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                                    <i class="fas fa-envelope transition-colors group-focus-within:text-brand-green"></i>
                                </span>
                                <input type="email" name="email" 
                                    class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-800 transition duration-300 focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green focus:bg-white" 
                                    placeholder="yourname@gmail.com" required value="{{ old('email') }}">
                            </div>
                        @endif
                    @endauth
                </div>

                <button type="submit" 
                    class="w-full bg-brand-green text-white font-bold text-lg py-3.5 rounded-xl hover:bg-[#7a1a1f] transition duration-300 shadow-[0_4px_14px_0_rgba(155,34,38,0.39)] hover:shadow-[0_6px_20px_rgba(155,34,38,0.23)] transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <i class="fas fa-paper-plane"></i>
                    Gửi Liên Kết Xác Thực
                </button>
            </form>

            <div class="text-center mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('login') }}" class="text-gray-500 hover:text-brand-green font-medium transition duration-300 flex items-center justify-center gap-2 group">
                    <i class="fas fa-arrow-left transform group-hover:-translate-x-1 transition duration-300"></i>
                    Quay lại Đăng nhập
                </a>
            </div>
        </div>
    </div>
</body>

</html>
