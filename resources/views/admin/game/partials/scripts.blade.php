@push('scripts')
    <script>
        // ========== TAB FUNCTIONS ==========
        const tabColors = {
            challenges: { bg: 'bg-yellow-500', hover: 'hover:bg-yellow-100', dark_hover: 'dark:hover:bg-yellow-900/30' },
            badges: { bg: 'bg-emerald-500', hover: 'hover:bg-emerald-100', dark_hover: 'dark:hover:bg-emerald-900/30' },
            frames: { bg: 'bg-purple-500', hover: 'hover:bg-purple-100', dark_hover: 'dark:hover:bg-purple-900/30' }
        };

        function showTab(tab) {
            // Hide all sections
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            // Remove active from all tabs
            document.querySelectorAll('.tab-btn').forEach(el => {
                el.classList.remove('bg-yellow-500', 'bg-emerald-500', 'bg-purple-500', 'text-white');
                el.classList.add('text-gray-600', 'dark:text-slate-300', 'hover:bg-gray-100', 'dark:hover:bg-slate-700');
            });

            // Show selected section
            document.getElementById('section-' + tab).classList.remove('hidden');
            // Activate selected tab with its color
            const activeTab = document.getElementById('tab-' + tab);
            const color = tabColors[tab];
            activeTab.classList.add(color.bg, 'text-white');
            activeTab.classList.remove('text-gray-600', 'dark:text-slate-300', 'hover:bg-gray-100', 'dark:hover:bg-slate-700');

            // Update URL without reload
            history.pushState(null, '', '?tab=' + tab);
        }

        // ========== BADGE MODAL FUNCTIONS ==========
        let currentBadgeId = null;

        function openBadgeModal(badgeId = null) {
            currentBadgeId = badgeId;
            const modal = document.getElementById('badge-modal');
            const content = document.getElementById('badge-modal-content');
            const title = document.querySelector('#badge-modal-title span');
            const submitText = document.getElementById('badge-submit-text');
            const form = document.getElementById('badge-form');
            const methodInput = document.getElementById('badge-form-method');

            form.reset();
            document.getElementById('badge-error-name').classList.add('hidden');
            setBadgeIconType('emoji');

            if (badgeId) {
                title.textContent = 'Chỉnh sửa Biểu Tượng';
                submitText.textContent = 'Cập nhật';
                methodInput.value = 'PUT';
                document.getElementById('badge-id').value = badgeId;

                submitText.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                fetch(`{{ url('admin/badges') }}/${badgeId}/edit`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            const b = data.badge;
                            document.getElementById('badge-name').value = b.name || '';
                            document.getElementById('badge-desc').value = b.description || '';
                            document.getElementById('badge-icon').value = b.icon || '';
                            document.getElementById('badge-active').checked = b.is_active;

                            if (b.icon && b.icon.startsWith('http')) {
                                setBadgeIconType('url');
                                document.getElementById('badge-icon-url').value = b.icon;
                            }
                        }
                        submitText.textContent = 'Cập nhật';
                    })
                    .catch(() => {
                        submitText.textContent = 'Cập nhật';
                        alert('Không thể tải dữ liệu!');
                    });
            } else {
                title.textContent = 'Thêm Biểu Tượng Mới';
                submitText.textContent = 'Tạo biểu tượng';
                methodInput.value = 'POST';
                document.getElementById('badge-active').checked = true;
            }

            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
                document.getElementById('badge-name').focus();
            }, 10);
        }

        function closeBadgeModal() {
            const modal = document.getElementById('badge-modal');
            const content = document.getElementById('badge-modal-content');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => modal.classList.add('hidden'), 200);
        }

        function setBadgeIconType(type) {
            const emojiBtn = document.getElementById('badge-btn-emoji');
            const urlBtn = document.getElementById('badge-btn-url');
            const fileBtn = document.getElementById('badge-btn-file');
            const emojiSection = document.getElementById('badge-emoji-section');
            const urlSection = document.getElementById('badge-url-section');
            const fileSection = document.getElementById('badge-file-section');

            const activeClasses = ['border-2', 'border-emerald-500', 'bg-emerald-50', 'dark:bg-emerald-900/30', 'text-emerald-600', 'dark:text-emerald-400'];
            const inactiveClasses = ['border', 'border-gray-300', 'dark:border-slate-600', 'text-gray-600', 'dark:text-slate-400'];

            // Reset all buttons
            [emojiBtn, urlBtn, fileBtn].forEach(btn => {
                btn.classList.remove(...activeClasses);
                btn.classList.add(...inactiveClasses);
            });

            // Hide all sections
            emojiSection.classList.add('hidden');
            urlSection.classList.add('hidden');
            fileSection.classList.add('hidden');

            // Activate selected
            if (type === 'emoji') {
                emojiBtn.classList.remove(...inactiveClasses);
                emojiBtn.classList.add(...activeClasses);
                emojiSection.classList.remove('hidden');
            } else if (type === 'url') {
                urlBtn.classList.remove(...inactiveClasses);
                urlBtn.classList.add(...activeClasses);
                urlSection.classList.remove('hidden');
            } else if (type === 'file') {
                fileBtn.classList.remove(...inactiveClasses);
                fileBtn.classList.add(...activeClasses);
                fileSection.classList.remove('hidden');
            }
        }

        function selectBadgeEmoji(emoji) {
            document.getElementById('badge-icon').value = emoji;
            document.getElementById('badge-emoji-input').value = emoji;
        }

        function updateBadgeUrlPreview() {
            const url = document.getElementById('badge-icon-url').value.trim();
            const preview = document.getElementById('badge-url-preview');
            const previewImg = document.getElementById('badge-url-preview-img');

            if (url && url.startsWith('http')) {
                previewImg.src = url;
                preview.classList.remove('hidden');
                document.getElementById('badge-icon').value = url;
            } else {
                preview.classList.add('hidden');
            }
        }

        function updateBadgeFilePreview(input) {
            const file = input.files[0];
            const preview = document.getElementById('badge-file-preview');
            const previewImg = document.getElementById('badge-file-preview-img');
            const fileName = document.getElementById('badge-file-name');

            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                    fileName.textContent = file.name;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
                // Clear icon value since we're using file upload
                document.getElementById('badge-icon').value = '';
            } else {
                preview.classList.add('hidden');
            }
        }

        function deleteBadge(id, name) {
            if (!confirm(`Xóa biểu tượng "${name}"?`)) return;

            fetch(`{{ url('admin/badges') }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.message || 'Có lỗi xảy ra!');
                    }
                })
                .catch(() => alert('Có lỗi xảy ra!'));
        }

        function filterBadges() {
            const search = document.getElementById('badge-search').value.toLowerCase();
            const filter = document.getElementById('badge-filter').value;
            let visible = 0;

            document.querySelectorAll('.badge-item').forEach(item => {
                const name = item.dataset.name;
                const status = item.dataset.status;
                const matchSearch = !search || name.includes(search);
                const matchFilter = filter === 'all' || status === filter;

                if (matchSearch && matchFilter) {
                    item.classList.remove('hidden');
                    visible++;
                } else {
                    item.classList.add('hidden');
                }
            });

            let noResults = document.getElementById('badges-no-results');
            if (visible === 0 && document.querySelectorAll('.badge-item').length > 0) {
                if (!noResults) {
                    noResults = document.createElement('div');
                    noResults.id = 'badges-no-results';
                    noResults.className = 'px-6 py-8 text-center text-gray-500 dark:text-slate-400';
                    noResults.innerHTML = '<i class="fas fa-search text-3xl text-gray-300 dark:text-slate-600 mb-2"></i><p>Không tìm thấy biểu tượng phù hợp</p>';
                    document.getElementById('badges-list').appendChild(noResults);
                }
            } else if (noResults) {
                noResults.remove();
            }
        }

        // ========== FRAME MODAL FUNCTIONS ==========
        let currentFrameId = null;

        function openFrameModal(frameId = null) {
            currentFrameId = frameId;
            const modal = document.getElementById('frame-modal');
            const content = document.getElementById('frame-modal-content');
            const title = document.querySelector('#frame-modal-title span');
            const submitText = document.getElementById('frame-submit-text');
            const form = document.getElementById('frame-form');
            const methodInput = document.getElementById('frame-form-method');

            form.reset();
            document.getElementById('frame-error-name').classList.add('hidden');
            document.getElementById('frame-error-image').classList.add('hidden');
            document.getElementById('frame-url-preview').classList.add('hidden');
            document.getElementById('frame-file-preview').classList.add('hidden');
            setFrameImageType('url');

            if (frameId) {
                title.textContent = 'Chỉnh sửa Khung Hoạt Ảnh';
                submitText.textContent = 'Cập nhật';
                methodInput.value = 'PUT';
                document.getElementById('frame-id').value = frameId;

                submitText.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                fetch(`{{ url('admin/avatar-frames') }}/${frameId}/edit`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            const f = data.frame;
                            document.getElementById('frame-name').value = f.name || '';
                            document.getElementById('frame-desc').value = f.description || '';
                            document.getElementById('frame-image-url').value = f.frame_image || '';
                            document.getElementById('frame-order').value = f.order || 0;
                            document.getElementById('frame-active').checked = f.is_active;
                            updateFrameUrlPreview();
                        }
                        submitText.textContent = 'Cập nhật';
                    })
                    .catch(() => {
                        submitText.textContent = 'Cập nhật';
                        alert('Không thể tải dữ liệu!');
                    });
            } else {
                title.textContent = 'Thêm Khung Hoạt Ảnh';
                submitText.textContent = 'Tạo khung';
                methodInput.value = 'POST';
                document.getElementById('frame-active').checked = true;
            }

            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
                document.getElementById('frame-name').focus();
            }, 10);
        }

        function closeFrameModal() {
            const modal = document.getElementById('frame-modal');
            const content = document.getElementById('frame-modal-content');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => modal.classList.add('hidden'), 200);
        }

        function setFrameImageType(type) {
            const urlBtn = document.getElementById('frame-btn-url');
            const fileBtn = document.getElementById('frame-btn-file');
            const urlSection = document.getElementById('frame-url-section');
            const fileSection = document.getElementById('frame-file-section');

            const activeClasses = ['border-2', 'border-purple-500', 'bg-purple-50', 'dark:bg-purple-900/30', 'text-purple-600', 'dark:text-purple-400'];
            const inactiveClasses = ['border', 'border-gray-300', 'dark:border-slate-600', 'text-gray-600', 'dark:text-slate-400'];

            // Reset buttons
            [urlBtn, fileBtn].forEach(btn => {
                btn.classList.remove(...activeClasses);
                btn.classList.add(...inactiveClasses);
            });

            // Hide all sections
            urlSection.classList.add('hidden');
            fileSection.classList.add('hidden');

            // Activate selected
            if (type === 'url') {
                urlBtn.classList.remove(...inactiveClasses);
                urlBtn.classList.add(...activeClasses);
                urlSection.classList.remove('hidden');
            } else if (type === 'file') {
                fileBtn.classList.remove(...inactiveClasses);
                fileBtn.classList.add(...activeClasses);
                fileSection.classList.remove('hidden');
            }
        }

        function updateFrameUrlPreview() {
            const url = document.getElementById('frame-image-url').value.trim();
            const preview = document.getElementById('frame-url-preview');
            const previewImg = document.getElementById('frame-url-preview-img');

            if (url && url.startsWith('http')) {
                previewImg.src = url;
                preview.classList.remove('hidden');
            } else {
                preview.classList.add('hidden');
            }
        }

        function updateFrameFilePreview(input) {
            const file = input.files[0];
            const preview = document.getElementById('frame-file-preview');
            const previewImg = document.getElementById('frame-file-preview-img');
            const fileName = document.getElementById('frame-file-name');

            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                    fileName.textContent = file.name;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                preview.classList.add('hidden');
            }
        }

        function deleteFrame(id, name) {
            if (!confirm(`Xóa khung "${name}"?`)) return;

            fetch(`{{ url('admin/avatar-frames') }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.message || 'Có lỗi xảy ra!');
                    }
                })
                .catch(() => alert('Có lỗi xảy ra!'));
        }

        function filterFrames() {
            const search = document.getElementById('frame-search').value.toLowerCase();
            const filter = document.getElementById('frame-filter').value;
            let visible = 0;

            document.querySelectorAll('.frame-item').forEach(item => {
                const name = item.dataset.name;
                const status = item.dataset.status;
                const matchSearch = !search || name.includes(search);
                const matchFilter = filter === 'all' || status === filter;

                if (matchSearch && matchFilter) {
                    item.classList.remove('hidden');
                    visible++;
                } else {
                    item.classList.add('hidden');
                }
            });

            let noResults = document.getElementById('frames-no-results');
            if (visible === 0 && document.querySelectorAll('.frame-item').length > 0) {
                if (!noResults) {
                    noResults = document.createElement('div');
                    noResults.id = 'frames-no-results';
                    noResults.className = 'col-span-full px-6 py-8 text-center text-gray-500 dark:text-slate-400';
                    noResults.innerHTML = '<i class="fas fa-search text-3xl text-gray-300 dark:text-slate-600 mb-2"></i><p>Không tìm thấy khung phù hợp</p>';
                    document.getElementById('frames-list').appendChild(noResults);
                }
            } else if (noResults) {
                noResults.remove();
            }
        }

        // ========== CHALLENGE MODAL FUNCTIONS ==========
        let currentChallengeId = null;

        function openChallengeModal(challengeId = null) {
            currentChallengeId = challengeId;
            const modal = document.getElementById('challenge-modal');
            const content = document.getElementById('challenge-modal-content');
            const title = document.querySelector('#challenge-modal-title span');
            const submitText = document.getElementById('challenge-submit-text');
            const form = document.getElementById('challenge-form');
            const methodInput = document.getElementById('challenge-form-method');

            form.reset();
            clearChallengeErrors();

            if (challengeId) {
                title.textContent = 'Chỉnh sửa Thử Thách';
                submitText.textContent = 'Cập nhật';
                methodInput.value = 'PUT';
                document.getElementById('challenge-id').value = challengeId;

                submitText.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                fetch(`{{ url('admin/challenges') }}/${challengeId}/edit`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            const c = data.challenge;
                            document.getElementById('challenge-name').value = c.name || '';
                            document.getElementById('challenge-name-count').textContent = (c.name || '').length;
                            document.getElementById('challenge-badge').value = c.badge_id || '';
                            document.getElementById('challenge-frame').value = c.avatar_frame_id || '';
                            document.getElementById('challenge-target').value = c.target_count || 1;
                            document.getElementById('challenge-start').value = c.start_date ? c.start_date.split('T')[0] : '';
                            document.getElementById('challenge-end').value = c.end_date ? c.end_date.split('T')[0] : '';
                            document.getElementById('challenge-description').value = c.description || '';
                            document.getElementById('challenge-desc-count').textContent = (c.description || '').length;
                            document.getElementById('challenge-active').checked = c.is_active;
                        }
                        submitText.textContent = 'Cập nhật';
                    })
                    .catch(() => {
                        submitText.textContent = 'Cập nhật';
                        alert('Không thể tải dữ liệu!');
                    });
            } else {
                title.textContent = 'Thêm Thử Thách Mới';
                submitText.textContent = 'Tạo thử thách';
                methodInput.value = 'POST';
                document.getElementById('challenge-active').checked = true;
            }

            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
                document.getElementById('challenge-name').focus();
            }, 10);
        }

        function closeChallengeModal() {
            const modal = document.getElementById('challenge-modal');
            const content = document.getElementById('challenge-modal-content');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => modal.classList.add('hidden'), 200);
        }

        function clearChallengeErrors() {
            ['name', 'badge_id', 'target_count', 'start_date', 'end_date', 'description'].forEach(field => {
                document.getElementById('error-' + field)?.classList.add('hidden');
            });
        }

        function deleteChallenge(id, name) {
            if (!confirm(`Xóa thử thách "${name}"?`)) return;

            fetch(`{{ url('admin/challenges') }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.message || 'Có lỗi xảy ra!');
                    }
                })
                .catch(() => alert('Có lỗi xảy ra!'));
        }

        function filterChallenges() {
            const search = document.getElementById('challenge-search').value.toLowerCase();
            const filter = document.getElementById('challenge-filter').value;
            let visible = 0;

            document.querySelectorAll('.challenge-item').forEach(item => {
                const name = item.dataset.name;
                const status = item.dataset.status;
                const matchSearch = !search || name.includes(search);
                const matchFilter = filter === 'all' || status === filter;

                if (matchSearch && matchFilter) {
                    item.classList.remove('hidden');
                    visible++;
                } else {
                    item.classList.add('hidden');
                }
            });
        }

        // ========== PAGE INIT ==========
        document.addEventListener('DOMContentLoaded', function () {
            // Read tab from URL
            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab') || 'challenges';
            showTab(tab);

            // ESC to close modals
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeChallengeModal();
                    closeBadgeModal();
                    closeFrameModal();
                }
            });

            // Challenge form submit handler
            const challengeForm = document.getElementById('challenge-form');
            if (challengeForm) {
                challengeForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const submitBtn = document.getElementById('challenge-submit-btn');
                    const submitText = document.getElementById('challenge-submit-text');
                    const originalText = submitText.textContent;

                    submitBtn.disabled = true;
                    submitText.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
                    clearChallengeErrors();

                    const formData = new FormData(challengeForm);
                    const method = document.getElementById('challenge-form-method').value;
                    const challengeId = document.getElementById('challenge-id').value;

                    let url = '{{ route("admin.challenges.store") }}';
                    if (method === 'PUT' && challengeId) {
                        url = `{{ url('admin/challenges') }}/${challengeId}`;
                        formData.append('_method', 'PUT');
                    }

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                closeChallengeModal();
                                window.location.href = '{{ route("admin.game.index") }}?tab=challenges';
                            } else if (data.errors) {
                                Object.keys(data.errors).forEach(field => {
                                    const errorEl = document.getElementById('error-' + field);
                                    if (errorEl) {
                                        errorEl.textContent = data.errors[field][0];
                                        errorEl.classList.remove('hidden');
                                    }
                                });
                            } else if (data.message) {
                                alert(data.message);
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            alert('Có lỗi xảy ra!');
                        })
                        .finally(() => {
                            submitBtn.disabled = false;
                            submitText.textContent = originalText;
                        });
                });
            }

            // Badge form submit handler
            const badgeForm = document.getElementById('badge-form');
            if (badgeForm) {
                badgeForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const submitBtn = document.getElementById('badge-submit-btn');
                    const submitText = document.getElementById('badge-submit-text');
                    const originalText = submitText.textContent;

                    submitBtn.disabled = true;
                    submitText.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
                    document.getElementById('badge-error-name').classList.add('hidden');

                    const formData = new FormData(badgeForm);
                    const method = document.getElementById('badge-form-method').value;
                    const badgeId = document.getElementById('badge-id').value;

                    let url = '{{ route("admin.badges.store") }}';
                    if (method === 'PUT' && badgeId) {
                        url = `{{ url('admin/badges') }}/${badgeId}`;
                        formData.append('_method', 'PUT');
                    }

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                closeBadgeModal();
                                window.location.href = '{{ route("admin.game.index") }}?tab=badges';
                            } else if (data.errors) {
                                if (data.errors.name) {
                                    document.getElementById('badge-error-name').textContent = data.errors.name[0];
                                    document.getElementById('badge-error-name').classList.remove('hidden');
                                }
                            } else if (data.message) {
                                alert(data.message);
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            alert('Có lỗi xảy ra!');
                        })
                        .finally(() => {
                            submitBtn.disabled = false;
                            submitText.textContent = originalText;
                        });
                });
            }

            // Frame form submit handler
            const frameForm = document.getElementById('frame-form');
            if (frameForm) {
                frameForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const submitBtn = document.getElementById('frame-submit-btn');
                    const submitText = document.getElementById('frame-submit-text');
                    const originalText = submitText.textContent;

                    submitBtn.disabled = true;
                    submitText.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
                    document.getElementById('frame-error-name').classList.add('hidden');
                    document.getElementById('frame-error-image').classList.add('hidden');

                    const formData = new FormData(frameForm);
                    const method = document.getElementById('frame-form-method').value;
                    const frameId = document.getElementById('frame-id').value;

                    let url = '{{ route("admin.avatar-frames.store") }}';
                    if (method === 'PUT' && frameId) {
                        url = `{{ url('admin/avatar-frames') }}/${frameId}`;
                        formData.append('_method', 'PUT');
                    }

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                closeFrameModal();
                                window.location.href = '{{ route("admin.game.index") }}?tab=frames';
                            } else if (data.errors) {
                                if (data.errors.name) {
                                    document.getElementById('frame-error-name').textContent = data.errors.name[0];
                                    document.getElementById('frame-error-name').classList.remove('hidden');
                                }
                                if (data.errors.frame_image_url) {
                                    document.getElementById('frame-error-image').textContent = data.errors.frame_image_url[0];
                                    document.getElementById('frame-error-image').classList.remove('hidden');
                                }
                            } else if (data.message) {
                                alert(data.message);
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            alert('Có lỗi xảy ra!');
                        })
                        .finally(() => {
                            submitBtn.disabled = false;
                            submitText.textContent = originalText;
                        });
                });
            }
        });
    </script>
@endpush