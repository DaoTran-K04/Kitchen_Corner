{{-- Modal Xem Chi Tiết Bình Luận --}}
<div id="commentModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4"
    onclick="closeCommentModal(event)">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden"
        onclick="event.stopPropagation()">
        {{-- Header --}}
        <div class="flex items-center justify-between p-6 border-b dark:border-slate-700 bg-gray-50 dark:bg-slate-700">
            <div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-comment-alt mr-2 text-blue-500"></i>Chi Tiết Bình Luận
                </h3>
            </div>
            <button onclick="closeCommentModal()"
                class="w-10 h-10 rounded-full bg-gray-100 dark:bg-slate-600 hover:bg-gray-200 dark:hover:bg-slate-500 flex items-center justify-center transition">
                <i class="fas fa-times text-gray-500 dark:text-slate-300"></i>
            </button>
        </div>

        {{-- Content --}}
        <div class="p-6 space-y-4">
            {{-- Thông tin bình luận --}}
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800 rounded-xl p-4">
                <p class="text-xs text-red-600 dark:text-red-400 font-bold mb-2">
                    <i class="fas fa-exclamation-triangle mr-1"></i> Bình luận bị báo cáo
                </p>
                <div class="flex items-start gap-3">
                    <i class="fas fa-quote-left text-red-300 dark:text-red-700 mt-1"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-800 dark:text-white mb-1" id="modal-comment-author"></p>
                        <p class="text-gray-700 dark:text-slate-300 leading-relaxed" id="modal-comment-content"></p>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mt-2" id="modal-comment-date"></p>
                    </div>
                </div>
            </div>

            {{-- Thông tin báo cáo --}}
            <div class="bg-gray-50 dark:bg-slate-700 rounded-xl p-4">
                <p class="text-xs text-gray-600 dark:text-slate-400 font-bold mb-2">
                    <i class="fas fa-flag mr-1"></i> Thông tin báo cáo
                </p>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500 dark:text-slate-400">Người báo cáo:</span>
                        <p class="font-medium text-gray-800 dark:text-white" id="modal-reporter-name"></p>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-slate-400">Lý do:</span>
                        <p class="font-medium text-orange-600 dark:text-orange-400" id="modal-report-reason"></p>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-gray-500 dark:text-slate-400 text-sm">Mô tả:</span>
                    <p class="text-gray-700 dark:text-slate-300 text-sm mt-1" id="modal-report-description"></p>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="p-4 border-t dark:border-slate-700 bg-gray-50 dark:bg-slate-700 flex justify-end">
            <button onclick="closeCommentModal()"
                class="px-4 py-2 rounded-lg bg-gray-200 dark:bg-slate-600 text-gray-700 dark:text-slate-200 font-medium hover:bg-gray-300 dark:hover:bg-slate-500 transition">
                Đóng
            </button>
        </div>
    </div>
</div>

{{-- Modal Chấp Thuận --}}
<div id="approveModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeApproveModal()"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-md transform transition-all">
            {{-- Header --}}
            <div class="bg-green-500 text-white px-6 py-4 rounded-t-xl flex justify-between items-center">
                <h3 class="font-bold text-lg">
                    <i class="fas fa-check-circle mr-2"></i>Chấp Thuận Báo Cáo
                </h3>
                <button onclick="closeApproveModal()" class="text-white/70 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            {{-- Body --}}
            <form id="approveForm" method="POST">
                @csrf
                <div class="p-6">
                    <div
                        class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-4">
                        <p class="text-sm text-yellow-800 dark:text-yellow-300">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Bạn đang chấp thuận báo cáo về bình luận của <strong id="approveUserName"></strong>.
                            <br><br>
                            <strong>Hành động:</strong> Bình luận vi phạm sẽ bị <span
                                class="text-red-600 dark:text-red-400 font-bold">xóa vĩnh viễn</span>.
                        </p>
                    </div>

                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-200 mb-2">
                        Ghi chú (tùy chọn):
                    </label>
                    <textarea name="admin_note" rows="5"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-slate-700 dark:text-white text-sm resize-y min-h-[120px]"
                        placeholder="Nhập ghi chú..."></textarea>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 bg-gray-50 dark:bg-slate-700 rounded-b-xl flex justify-end gap-3">
                    <button type="button" onclick="closeApproveModal()"
                        class="px-4 py-2 bg-gray-200 dark:bg-slate-600 text-gray-700 dark:text-slate-200 rounded-lg hover:bg-gray-300 dark:hover:bg-slate-500 transition text-sm font-bold">
                        Hủy
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition text-sm font-bold">
                        <i class="fas fa-check mr-1"></i> Xác nhận chấp thuận
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Từ Chối --}}
<div id="rejectModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeRejectModal()"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-md transform transition-all">
            {{-- Header --}}
            <div class="bg-red-500 text-white px-6 py-4 rounded-t-xl flex justify-between items-center">
                <h3 class="font-bold text-lg">
                    <i class="fas fa-ban mr-2"></i>Từ Chối Báo Cáo
                </h3>
                <button onclick="closeRejectModal()" class="text-white/70 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            {{-- Body --}}
            <form id="rejectForm" method="POST">
                @csrf
                <div class="p-6">
                    <p class="text-sm text-gray-600 dark:text-slate-300 mb-4">
                        Bình luận sẽ được giữ nguyên và báo cáo sẽ được đánh dấu là đã từ chối.
                    </p>

                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-200 mb-2">
                        Lý do từ chối (tùy chọn):
                    </label>
                    <textarea name="admin_note" rows="5"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white text-sm resize-y min-h-[120px]"
                        placeholder="Nhập lý do từ chối..."></textarea>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 bg-gray-50 dark:bg-slate-700 rounded-b-xl flex justify-end gap-3">
                    <button type="button" onclick="closeRejectModal()"
                        class="px-4 py-2 bg-gray-200 dark:bg-slate-600 text-gray-700 dark:text-slate-200 rounded-lg hover:bg-gray-300 dark:hover:bg-slate-500 transition text-sm font-bold">
                        Hủy
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-sm font-bold">
                        <i class="fas fa-ban mr-1"></i> Xác nhận từ chối
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
