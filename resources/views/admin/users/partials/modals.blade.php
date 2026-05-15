<!-- Modal Khóa / Mở khóa Tài khoản -->
<div id="banUserModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-xl bg-white dark:bg-slate-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
            <div class="bg-white dark:bg-slate-800 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-orange-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-user-slash text-orange-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                        <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white" id="ban-modal-title">Khóa Tài khoản</h3>
                        <p class="text-sm text-gray-500 mt-1" id="ban-modal-desc">Bạn đang thao tác với người dùng này.</p>
                        <div class="mt-4">
                            <form id="banUserForm" method="POST" action="">
                                @csrf
                                <div class="mb-4" id="ban-reason-container">
                                    <label for="ban_reason" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Lý do khóa tài khoản (Gửi thông báo cho user) <span class="text-red-500">*</span></label>
                                    <textarea name="ban_reason" id="ban_reason" rows="3" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white" placeholder="Vd: Vi phạm nghiêm trọng tiêu chuẩn cộng đồng nhiều lần..."></textarea>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-slate-700/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                <button type="button" onclick="submitBanForm()" id="btn-confirm-ban" class="inline-flex w-full justify-center rounded-md bg-orange-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-orange-500 sm:ml-3 sm:w-auto transition-colors">Xác nhận</button>
                <button type="button" onclick="closeBanModal()" class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-slate-600 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-slate-500 hover:bg-gray-50 dark:hover:bg-slate-500 sm:mt-0 sm:w-auto transition-colors">Hủy bỏ</button>
            </div>
        </div>
    </div>
</div>

<script>
    function openBanModal(url, isActive, userName) {
        document.getElementById('banUserForm').action = url;
        const modal = document.getElementById('banUserModal');
        const title = document.getElementById('ban-modal-title');
        const desc = document.getElementById('ban-modal-desc');
        const reasonContainer = document.getElementById('ban-reason-container');
        const reasonInput = document.getElementById('ban_reason');
        const btn = document.getElementById('btn-confirm-ban');

        if (isActive) {
            // Đang active -> cần khóa
            title.innerText = 'Khóa Tài khoản';
            desc.innerHTML = `Bạn có chắc chắn muốn <b>Khóa</b> tài khoản của <b>${userName}</b>? Người dùng này sẽ không thể đăng nhập.`;
            reasonContainer.classList.remove('hidden');
            reasonInput.setAttribute('required', 'required');
            btn.className = 'inline-flex w-full justify-center rounded-md bg-orange-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-orange-500 sm:ml-3 sm:w-auto transition-colors';
            btn.innerText = 'Khóa tài khoản';
        } else {
            // Đang khóa -> cần mở khóa
            title.innerText = 'Mở khóa Tài khoản';
            desc.innerHTML = `Bạn có chắc chắn muốn <b>Kích hoạt lại</b> tài khoản của <b>${userName}</b>?`;
            reasonContainer.classList.add('hidden');
            reasonInput.removeAttribute('required');
            btn.className = 'inline-flex w-full justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 sm:ml-3 sm:w-auto transition-colors';
            btn.innerText = 'Mở khóa tài khoản';
        }

        modal.classList.remove('hidden');
        if(isActive) reasonInput.focus();
    }

    function closeBanModal() {
        document.getElementById('banUserModal').classList.add('hidden');
        document.getElementById('banUserForm').reset();
    }

    function submitBanForm() {
        const reasonInput = document.getElementById('ban_reason');
        if (!reasonInput.closest('#ban-reason-container').classList.contains('hidden') && !reasonInput.value.trim()) {
            alert('Vui lòng nhập lý do khóa tài khoản!');
            reasonInput.focus();
            return;
        }
        document.getElementById('banUserForm').submit();
    }
</script>
