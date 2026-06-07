<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Nutrition UI</title>
    <!-- Nhúng Tailwind CSS CDN để test nhanh (Bỏ qua Vite) -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4" style="background-image: url('https://images.unsplash.com/photo-1556910103-1c02745aae4d?q=80&w=2070&auto=format&fit=crop'); background-size: cover; background-position: center;">
    
    <!-- Lớp phủ mờ nền giống ảnh thiết kế -->
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

    <!-- Vị trí đặt Component -->
    <div class="relative z-10 w-full max-w-sm">
        <x-nutrition-card calories="622" protein="40" carbs="63" fat="15" />
    </div>

</body>
</html>
