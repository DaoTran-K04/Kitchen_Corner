@extends('layouts.app')

@section('title', 'Chính Sách Bảo Mật')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8 pb-4 border-b border-gray-200">Chính Sách Bảo Mật</h1>
        
        <div class="space-y-6 text-gray-600">
            <p>Chính sách bảo mật này mô tả cách chúng tôi thu thập, sử dụng và bảo vệ thông tin cá nhân của bạn.</p>

            <section>
                <h3 class="text-lg font-bold text-gray-800 mb-2">1. Thu thập thông tin</h3>
                <p>Chúng tôi thu thập thông tin khi bạn đăng ký tài khoản, bao gồm: Tên, Email và Ảnh đại diện (nếu có). Chúng tôi cam kết không chia sẻ thông tin này cho bên thứ ba.</p>
            </section>

            <section>
                <h3 class="text-lg font-bold text-gray-800 mb-2">2. Sử dụng thông tin</h3>
                <p>Thông tin của bạn được sử dụng để:</p>
                <ul class="list-disc pl-5 mt-2">
                    <li>Cá nhân hóa trải nghiệm người dùng.</li>
                    <li>Gửi thông báo về bài viết mới (nếu bạn đăng ký).</li>
                    <li>Cải thiện chất lượng dịch vụ website.</li>
                </ul>
            </section>

            <section>
                <h3 class="text-lg font-bold text-gray-800 mb-2">3. Cookies</h3>
                <p>Website sử dụng Cookie để lưu trạng thái đăng nhập và các thiết lập cá nhân của bạn nhằm mang lại trải nghiệm tốt nhất.</p>
            </section>
        </div>
    </div>
</div>
@endsection
