<!-- Modal Từ Chối -->
<div id="rejectModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-xl bg-white dark:bg-slate-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
            <div class="bg-white dark:bg-slate-800 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                        <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white" id="modal-title">Từ chối Công thức</h3>
                        <div class="mt-4">
                            <form id="rejectForm" method="POST" action="">
                                @csrf
                                <div class="mb-4">
                                    <label for="reject_reason" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Lý do từ chối (Gửi thông báo cho tác giả) <span class="text-red-500">*</span></label>
                                    <textarea name="reason" id="reject_reason" rows="3" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white" placeholder="Vd: Công thức thiếu bước thực hiện, hình ảnh không hợp lệ..."></textarea>
                                </div>
                                
                                <div class="mb-4 flex items-start p-3 bg-red-50 dark:bg-red-900/30 rounded-lg border border-red-100 dark:border-red-800">
                                    <div class="flex h-6 items-center">
                                        <input id="is_violation" name="is_violation" type="checkbox" value="1" class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-600">
                                    </div>
                                    <div class="ml-3 text-sm leading-6">
                                        <label for="is_violation" class="font-bold text-red-700 dark:text-red-400">Đánh dấu là vi phạm nghiêm trọng</label>
                                        <p class="text-red-500 dark:text-red-300 text-xs">Sử dụng khi bài viết sai thuần phong mỹ tục, spam, hoặc nội dung bị cấm. Hệ thống sẽ cảnh báo/khóa tài khoản nếu vi phạm nhiều lần.</p>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-slate-700/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                <button type="button" onclick="submitRejectForm()" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto transition-colors">Xác nhận Từ chối</button>
                <button type="button" onclick="closeRejectModal()" class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-slate-600 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-slate-500 hover:bg-gray-50 dark:hover:bg-slate-500 sm:mt-0 sm:w-auto transition-colors">Hủy bỏ</button>
            </div>
        </div>
    </div>
</div>

<script>
    function openRejectModal(url) {
        document.getElementById('rejectForm').action = url;
        document.getElementById('rejectModal').classList.remove('hidden');
        document.getElementById('reject_reason').focus();
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
        document.getElementById('rejectForm').reset();
    }

    function submitRejectForm() {
        if (!document.getElementById('reject_reason').value.trim()) {
            alert('Vui lòng nhập lý do từ chối!');
            document.getElementById('reject_reason').focus();
            return;
        }
        document.getElementById('rejectForm').submit();
    }
</script>
