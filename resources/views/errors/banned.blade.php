<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tài Khoản Bị Khóa - Góc Bếp</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3 { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl overflow-hidden text-center relative">
        <div class="h-32 bg-red-600 relative overflow-hidden">
            <!-- Decorative patterns -->
            <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        </div>
        
        <div class="px-8 pb-8">
            <div class="mx-auto w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-lg -mt-10 mb-6 relative z-10 border-4 border-white">
                <i class="fas fa-user-lock text-4xl text-red-600"></i>
            </div>
            
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Tài Khoản Bị Khóa</h2>
            
            <div class="bg-red-50 text-red-700 px-4 py-3 rounded-xl mb-6 text-sm font-medium border border-red-100 shadow-inner">
                {{ session('ban_reason') ?? 'Tài khoản của bạn đã vi phạm nghiêm trọng Tiêu chuẩn cộng đồng.' }}
            </div>
            
            <p class="text-gray-500 text-sm mb-8 leading-relaxed">
                Rất tiếc, tài khoản của bạn hiện không thể truy cập hệ thống. Nếu bạn cho rằng đây là một sự nhầm lẫn, vui lòng liên hệ với ban quản trị để được hỗ trợ.
            </p>
            
            <div class="space-y-3">
                <a href="{{ route('page.contact') }}" class="block w-full py-3 px-4 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl transition shadow-lg shadow-red-200">
                    <i class="fas fa-headset mr-2"></i> Liên hệ Ban Quản Trị
                </a>
                <a href="{{ route('home') }}" class="block w-full py-3 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition">
                    <i class="fas fa-home mr-2"></i> Trở về Trang chủ
                </a>
            </div>
        </div>
    </div>

</body>
</html>
