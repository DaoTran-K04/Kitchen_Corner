<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Đổi Mật Khẩu - Góc Sách</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { 'brand-green': '#3E5F4E', 'brand-cream': '#FDFBF7', 'brand-brown': '#8C6B4B' },
                    fontFamily: { sans: ['Segoe UI', 'Roboto', 'sans-serif'], serif: ['Merriweather', 'serif'] }
                }
            }
        }
    </script>
    <style>
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
        .animate-slide-in { animation: slideIn 0.3s ease-out forwards; }
        .animate-slide-out { animation: slideOut 0.3s ease-in forwards; }
    </style>
</head>

<body class="bg-brand-cream font-sans min-h-screen flex items-center justify-center p-4">
    <div
        class="max-w-4xl w-full bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col md:flex-row border border-gray-100">
        <!-- Left Side - Decorative -->
        <div
            class="md:w-1/2 bg-brand-green flex flex-col justify-center items-center text-white p-8 relative overflow-hidden">
            <div class="text-center">
                <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shield-alt text-4xl"></i>
                </div>
                <h2 class="text-3xl font-serif font-bold mb-2">Bảo Mật Tài Khoản</h2>
                <p class="opacity-90">Thay đổi mật khẩu thường xuyên để bảo vệ tài khoản của bạn an toàn hơn.</p>
                <div class="mt-6 space-y-2 text-sm opacity-80">
                    <div class="flex items-center justify-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        <span>Mật khẩu tối thiểu 6 ký tự</span>
                    </div>
                    <div class="flex items-center justify-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        <span>Kết hợp chữ và số</span>
                    </div>
                    <div class="flex items-center justify-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        <span>Không dùng thông tin cá nhân</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="md:w-1/2 p-8 md:p-12">
            <div class="text-center mb-8">
                <a href="{{ route('home') }}"
                    class="text-brand-green text-2xl font-bold flex items-center justify-center gap-2 mb-2">
                    <h3 class="text-xl font-bold text-gray-800 font-serif">Đổi Mật Khẩu</h3>
                </a>
                <hr>
                <p class="text-gray-500 text-sm mt-1">Xin chào, <strong
                        class="text-brand-green">{{ Auth::user()->name }}</strong></p>
            </div>

            <form id="change-password-form">
                @csrf

                <!-- Message Container (for AJAX responses) -->
                <div id="message-container"></div>

                <!-- Current Password -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Mật khẩu hiện tại</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"><i
                                class="fas fa-lock"></i></span>
                        <input type="password" name="current_password" id="current_password"
                            class="w-full pl-10 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green transition"
                            placeholder="Nhập mật khẩu hiện tại" required>
                        <button type="button" onclick="togglePassword('current_password', 'eye-1')"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-brand-green focus:outline-none cursor-pointer">
                            <i class="fas fa-eye" id="eye-1"></i>
                        </button>
                    </div>
                </div>

                <!-- New Password -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Mật khẩu mới</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"><i
                                class="fas fa-key"></i></span>
                        <input type="password" name="new_password" id="new_password"
                            class="w-full pl-10 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green transition"
                            placeholder="Nhập mật khẩu mới (tối thiểu 6 ký tự)" required>
                        <button type="button" onclick="togglePassword('new_password', 'eye-2')"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-brand-green focus:outline-none cursor-pointer">
                            <i class="fas fa-eye" id="eye-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Confirm New Password -->
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Xác nhận mật khẩu mới</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"><i
                                class="fas fa-check-double"></i></span>
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                            class="w-full pl-10 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green transition"
                            placeholder="Nhập lại mật khẩu mới" required>
                        <button type="button" onclick="togglePassword('new_password_confirmation', 'eye-3')"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-brand-green focus:outline-none cursor-pointer">
                            <i class="fas fa-eye" id="eye-3"></i>
                        </button>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3">
                    <a href="{{ route('profile') }}"
                        class="flex-1 text-center bg-gray-100 text-gray-700 font-bold py-3 rounded-lg hover:bg-gray-200 transition border border-gray-200">
                        <i class="fas fa-arrow-left mr-2"></i>Quay lại
                    </a>
                    <button type="submit" id="submit-btn"
                        class="flex-1 bg-brand-green text-white font-bold py-3 rounded-lg hover:bg-[#2C3E36] transition transform hover:-translate-y-0.5 shadow-md">
                        <i class="fas fa-save mr-2"></i>Lưu Thay Đổi
                    </button>
                </div>
            </form>

            <!-- Footer Link -->
            <div class="text-center mt-6 pt-4 border-t border-gray-100">
                <a href="{{ route('password.request') }}" class="text-sm text-brand-brown hover:underline">
                    <i class="fas fa-question-circle mr-1"></i>Quên mật khẩu?
                </a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // AJAX Form Submission - Sticky Form
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('change-password-form');
            const messageContainer = document.getElementById('message-container');
            const submitBtn = document.getElementById('submit-btn');

            // Function to show toast notification
            function showToast(message, isError = true) {
                // Remove existing toast if any
                const existingToast = document.getElementById('toast-notification');
                if (existingToast) existingToast.remove();

                const bgColor = isError ? 'bg-red-500' : 'bg-green-500';
                const icon = isError ? 'fa-exclamation-circle' : 'fa-check-circle';

                const toast = document.createElement('div');
                toast.id = 'toast-notification';
                toast.className = `fixed top-4 right-4 ${bgColor} text-white px-4 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2 text-sm max-w-xs animate-slide-in`;
                toast.innerHTML = `
                    <i class="fas ${icon}"></i>
                    <span>${message}</span>
                    <button onclick="this.parentElement.remove()" class="ml-2 hover:text-white/80">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                document.body.appendChild(toast);

                // Auto remove after 5 seconds
                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.classList.add('animate-slide-out');
                        setTimeout(() => toast.remove(), 300);
                    }
                }, 5000);
            }

            form.addEventListener('submit', async function (e) {
                e.preventDefault();

                // Disable button and show loading
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang xử lý...';

                // Clear previous messages
                messageContainer.innerHTML = '';

                // Get form data
                const formData = new FormData(form);

                try {
                    const response = await fetch('{{ route("change.password.post") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        // Success - show message with countdown (keep this inline as it's important)
                        messageContainer.innerHTML = `
                            <div id="success-message" class="bg-green-50 text-green-700 p-3 rounded-lg mb-4 border border-green-200 text-sm">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-check-circle text-green-600"></i>
                                    <span class="font-medium">${data.message || 'Đổi mật khẩu thành công!'}</span>
                                    <span class="ml-auto text-xs">
                                        <i class="fas fa-home mr-1"></i>Về trang chủ sau <strong id="countdown">3</strong>s
                                    </span>
                                </div>
                            </div>
                        `;

                        // Clear form fields
                        form.reset();

                        // Start countdown
                        let seconds = 3;
                        const countdownEl = document.getElementById('countdown');
                        const countdownInterval = setInterval(function () {
                            seconds--;
                            countdownEl.textContent = seconds;
                            if (seconds <= 0) {
                                clearInterval(countdownInterval);
                                window.location.href = '{{ route("home") }}';
                            }
                        }, 1000);

                    } else {
                        // Error - show toast notification (form values are preserved!)
                        let errorMessage = '';

                        if (data.errors) {
                            // Get first error message only for toast
                            for (const field in data.errors) {
                                errorMessage = data.errors[field][0];
                                break;
                            }
                        } else if (data.message) {
                            errorMessage = data.message;
                        } else {
                            errorMessage = 'Có lỗi xảy ra, vui lòng thử lại.';
                        }

                        showToast(errorMessage, true);
                    }

                } catch (error) {
                    console.error('Error:', error);
                    showToast('Không thể kết nối đến server. Vui lòng thử lại.', true);
                }

                // Re-enable button
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i>Lưu Thay Đổi';
            });
        });
    </script>
</body>

</html>