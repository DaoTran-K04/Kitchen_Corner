@extends('layouts.app')

@section('title', 'Về Chúng Tôi')

@section('content')
<div class="bg-brand-beige/30 py-8">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto bg-white p-8 md:p-12 rounded-2xl shadow-soft border border-gray-100">
            <h1 class="text-3xl md:text-4xl font-bold text-brand-green font-serif mb-6 text-center">Về Góc Sách Review</h1>
            
            <div class="prose prose-lg text-gray-600 mx-auto leading-relaxed">
                <p class="mb-4">
                    Chào mừng bạn đến với <strong>Góc Sách Review</strong> - nơi kết nối những tâm hồn yêu văn học. Được thành lập vào năm 2024, chúng tôi mong muốn tạo ra một không gian trực tuyến lành mạnh, nơi mọi người có thể chia sẻ những cảm nhận chân thực nhất về những cuốn sách họ đã đọc.
                </p>
                <p class="mb-4">
                    Tại đây, bạn không chỉ tìm thấy những bài review chất lượng mà còn có thể tham gia thảo luận, đề xuất sách hay và kết bạn với những người cùng sở thích.
                </p>

                <h3 class="text-xl font-bold text-gray-800 mt-8 mb-3">Sứ mệnh của chúng tôi</h3>
                <ul class="list-disc pl-5 space-y-2 mb-6">
                    <li>Lan tỏa văn hóa đọc đến cộng đồng trẻ.</li>
                    <li>Xây dựng thư viện review sách phong phú, đa chiều và trung thực.</li>
                    <li>Kết nối tác giả, nhà xuất bản và độc giả.</li>
                </ul>

                <h3 class="text-xl font-bold text-gray-800 mt-8 mb-3">Đội ngũ phát triển</h3>
                <p>
                    Dự án được xây dựng và phát triển bởi team <strong>Mọt Sách</strong> với niềm đam mê cháy bỏng dành cho công nghệ và văn học.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection