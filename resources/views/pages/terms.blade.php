@extends('layouts.app')

@section('title', 'Điều Khoản Sử Dụng')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8 pb-4 border-b border-gray-200">Điều Khoản Sử Dụng</h1>
        
        <div class="space-y-6 text-gray-600">
            <section>
                <h3 class="text-lg font-bold text-gray-800 mb-2">1. Giới thiệu</h3>
                <p>Chào mừng bạn đến với Góc Bếp. Khi truy cập website của chúng tôi, bạn đồng ý với các điều khoản này. Trang web có quyền thay đổi, chỉnh sửa, thêm hoặc lược bỏ bất kỳ phần nào trong Điều khoản sử dụng dịch vụ này, vào bất cứ lúc nào.</p>
            </section>

            <section>
                <h3 class="text-lg font-bold text-gray-800 mb-2">2. Quy định về nội dung User</h3>
                <p>Người dùng chịu trách nhiệm về nội dung bài công thức và bình luận mình đăng tải. Nghiêm cấm các hành vi:</p>
                <ul class="list-disc pl-5 mt-2">
                    <li>Đăng tải nội dung đồi trụy, phản động, vi phạm pháp luật Việt Nam.</li>
                    <li>Sử dụng ngôn từ thù địch, xúc phạm danh dự người khác.</li>
                    <li>Spam quảng cáo, link độc hại.</li>
                </ul>
            </section>

            <section>
                <h3 class="text-lg font-bold text-gray-800 mb-2">3. Bản quyền</h3>
                <p>Mọi nội dung trên website thuộc quyền sở hữu của Góc Bếp hoặc được cấp phép sử dụng. Nghiêm cấm sao chép dưới mọi hình thức nếu không có sự đồng ý bằng văn bản.</p>
            </section>
        </div>
    </div>
</div>
@endsection
