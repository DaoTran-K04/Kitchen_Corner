{{--
Report Modal Component
Include this partial once per page (at the end of body)
Usage: @include('partials.report-modal')

Functions available:
- openReportModal(id, type) - type: 'post' or 'comment'
- closeReportModal()
--}}

{{-- Report Modal --}}
<div id="reportModal" class="fixed inset-0 bg-black/50 z-[100] hidden items-center justify-center p-4 backdrop-blur-sm"
    onclick="closeReportModal()">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden animate-fade-in"
        onclick="event.stopPropagation()">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-red-500 to-orange-500 text-white p-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold flex items-center gap-2">
                    <i class="fas fa-flag"></i> Báo cáo vi phạm
                </h3>
                <button onclick="closeReportModal()"
                    class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        {{-- Body --}}
        <form id="reportForm" onsubmit="submitReport(event)">
            @csrf
            <input type="hidden" id="reportItemId" name="item_id">
            <input type="hidden" id="reportItemType" name="item_type">

            <div class="p-5 space-y-4">
                {{-- Reason Selection --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fas fa-exclamation-circle text-orange-500 mr-1"></i> Lý do báo cáo
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        <label
                            class="report-reason-option flex items-center gap-2 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="reason" value="spam" class="hidden">
                            <span
                                class="w-4 h-4 rounded-full border-2 border-gray-300 flex-shrink-0 radio-circle"></span>
                            <span class="text-sm text-gray-700">Spam / Quảng cáo</span>
                        </label>
                        <label
                            class="report-reason-option flex items-center gap-2 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="reason" value="offensive" class="hidden">
                            <span
                                class="w-4 h-4 rounded-full border-2 border-gray-300 flex-shrink-0 radio-circle"></span>
                            <span class="text-sm text-gray-700">Xúc phạm</span>
                        </label>
                        <label
                            class="report-reason-option flex items-center gap-2 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="reason" value="harassment" class="hidden">
                            <span
                                class="w-4 h-4 rounded-full border-2 border-gray-300 flex-shrink-0 radio-circle"></span>
                            <span class="text-sm text-gray-700">Quấy rối</span>
                        </label>
                        <label
                            class="report-reason-option flex items-center gap-2 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="reason" value="inappropriate" class="hidden">
                            <span
                                class="w-4 h-4 rounded-full border-2 border-gray-300 flex-shrink-0 radio-circle"></span>
                            <span class="text-sm text-gray-700">Không phù hợp</span>
                        </label>
                        <label
                            class="report-reason-option flex items-center gap-2 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition col-span-2">
                            <input type="radio" name="reason" value="other" class="hidden">
                            <span
                                class="w-4 h-4 rounded-full border-2 border-gray-300 flex-shrink-0 radio-circle"></span>
                            <span class="text-sm text-gray-700">Lý do khác</span>
                        </label>
                    </div>
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fas fa-edit text-gray-400 mr-1"></i> Mô tả chi tiết (tùy chọn)
                    </label>
                    <textarea name="description" rows="3"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 resize-none text-sm"
                        placeholder="Nhập mô tả thêm về vi phạm..."></textarea>
                </div>

                {{-- Note --}}
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-xs text-yellow-700">
                    <i class="fas fa-info-circle mr-1"></i>
                    Báo cáo sẽ được admin xem xét và xử lý trong thời gian sớm nhất. Cảm ơn bạn đã giúp cộng đồng trở
                    nên tốt đẹp hơn!
                </div>
            </div>

            {{-- Footer --}}
            <div class="p-4 bg-gray-50 flex justify-end gap-3 border-t">
                <button type="button" onclick="closeReportModal()"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium text-sm">
                    Hủy
                </button>
                <button type="submit" id="reportSubmitBtn"
                    class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition font-bold text-sm flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i> Gửi báo cáo
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .report-reason-option:has(input:checked) {
        background: linear-gradient(135deg, #fef3c7 0%, #fef9c3 100%);
        border-color: #f59e0b;
    }

    .report-reason-option:has(input:checked) .radio-circle {
        background: #f59e0b;
        border-color: #f59e0b;
        box-shadow: inset 0 0 0 3px white;
    }
</style>

<script>
    function openReportModal(itemId, itemType) {
        @guest
            alert('Bạn cần đăng nhập để báo cáo!');
            window.location.href = '{{ route("login") }}';
            return;
        @endguest

        document.getElementById('reportItemId').value = itemId;
        document.getElementById('reportItemType').value = itemType;
        document.getElementById('reportModal').classList.remove('hidden');
        document.getElementById('reportModal').classList.add('flex');
        document.body.style.overflow = 'hidden';

        // Reset form
        document.getElementById('reportForm').reset();
    }

    function closeReportModal() {
        document.getElementById('reportModal').classList.add('hidden');
        document.getElementById('reportModal').classList.remove('flex');
        document.body.style.overflow = '';
    }

    async function submitReport(event) {
        event.preventDefault();

        const form = document.getElementById('reportForm');
        const itemId = document.getElementById('reportItemId').value;
        const itemType = document.getElementById('reportItemType').value;
        const reason = form.querySelector('input[name="reason"]:checked');
        const description = form.querySelector('textarea[name="description"]').value;
        const submitBtn = document.getElementById('reportSubmitBtn');

        if (!reason) {
            alert('Vui lòng chọn lý do báo cáo!');
            return;
        }

        // Disable button
        const originalBtnHtml = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';
        submitBtn.disabled = true;

        try {
            const url = itemType === 'post' ? `{{ url("/report/post") }}/${itemId}` : `{{ url("/report/comment") }}/${itemId}`;
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    reason: reason.value,
                    description: description
                })
            });

            const data = await response.json();

            if (data.success) {
                closeReportModal();
                // Show success toast
                showReportToast(data.message || 'Báo cáo đã được gửi thành công!', 'success');
            } else {
                showReportToast(data.message || 'Có lỗi xảy ra, vui lòng thử lại!', 'error');
            }
        } catch (error) {
            console.error('Report error:', error);
            showReportToast('Có lỗi xảy ra, vui lòng thử lại!', 'error');
        } finally {
            submitBtn.innerHTML = originalBtnHtml;
            submitBtn.disabled = false;
        }
    }

    function showReportToast(message, type = 'success') {
        // Remove existing toast if any
        const existingToast = document.getElementById('reportToast');
        if (existingToast) existingToast.remove();

        const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

        const toast = document.createElement('div');
        toast.id = 'reportToast';
        toast.className = `fixed bottom-4 right-4 ${bgColor} text-white px-4 py-3 rounded-lg shadow-lg z-[200] flex items-center gap-2 animate-fade-in`;
        toast.innerHTML = `<i class="fas ${icon}"></i> ${message}`;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(10px)';
            toast.style.transition = 'all 0.3s';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // ESC to close report modal
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !document.getElementById('reportModal').classList.contains('hidden')) {
            closeReportModal();
        }
    });
</script>
