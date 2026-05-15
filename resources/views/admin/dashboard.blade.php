@extends('layouts.admin')
@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div
        class="bg-white dark:bg-slate-800 p-4 rounded-lg shadow-sm border border-gray-100 dark:border-slate-700 mb-6 flex flex-col md:flex-row justify-between items-center gap-4 transition-colors duration-300">
        <div>
            <h3 class="font-bold text-gray-700 dark:text-slate-200"><i class="fas fa-filter mr-2 text-blue-500"></i>Bộ lọc
                thời gian</h3>
            <p class="text-xs text-gray-500 dark:text-slate-400">Xem chi tiết dữ liệu theo từng tháng cụ thể</p>
        </div>
        <div class="flex items-center gap-2" id="filter-controls">
            {{-- Hidden inputs to store actual values --}}
            <input type="hidden" id="filter-month" value="{{ $selectedMonth }}">
            <input type="hidden" id="filter-year" value="{{ $selectedYear }}">

            {{-- Custom Month Dropdown --}}
            <div class="custom-dropdown" id="month-dropdown">
                <div
                    class="custom-dropdown-trigger border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-700 dark:text-slate-200 text-sm font-medium">
                    <span id="month-display">Tháng {{ $selectedMonth }}</span>
                    <svg class="w-4 h-4 text-gray-500 dark:text-slate-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
                <div class="custom-dropdown-menu month-menu">
                    <div class="custom-dropdown-menu-inner">
                        @for($m = 1; $m <= 12; $m++)
                            <div class="custom-dropdown-item text-gray-700 dark:text-slate-200 {{ $selectedMonth == $m ? 'active' : '' }}"
                                data-value="{{ $m }}">
                                {{ $m }}
                            </div>
                        @endfor
                    </div>
                </div>
            </div>

            {{-- Custom Year Dropdown --}}
            <div class="custom-dropdown" id="year-dropdown">
                <div
                    class="custom-dropdown-trigger border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-700 dark:text-slate-200 text-sm font-medium">
                    <span id="year-display">Năm {{ $selectedYear }}</span>
                    <svg class="w-4 h-4 text-gray-500 dark:text-slate-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
                <div class="custom-dropdown-menu year-menu">
                    <div class="custom-dropdown-menu-inner">
                        @for($y = date('Y'); $y >= 2024; $y--)
                            <div class="custom-dropdown-item text-gray-700 dark:text-slate-200 {{ $selectedYear == $y ? 'active' : '' }}"
                                data-value="{{ $y }}">
                                {{ $y }}
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
            <a href="{{ route('admin.dashboard.export', ['month' => $selectedMonth, 'year' => $selectedYear]) }}"
                id="export-excel-btn"
                class="inline-flex items-center gap-2 px-4 py-2 bg-[#107C41] text-white text-sm font-semibold rounded-lg shadow hover:bg-[#056030] transition-all duration-200 hover:shadow-md">
                <i class="fas fa-file-excel"></i>
                <span>Xuất Excel</span>
            </a>
            <div id="filter-loading" class="hidden">
                <i class="fas fa-spinner fa-spin text-blue-500"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div
            class="bg-white dark:bg-slate-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 relative overflow-hidden group transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
            <div class="relative z-10">
                <p class="text-[#9b2226] text-sm font-bold uppercase tracking-wider mb-1">CÔNG THỨC</p>
                <h3 class="text-3xl font-extrabold text-gray-800 dark:text-white">{{ number_format($totalReviews) }}</h3>
                <p class="text-gray-400 dark:text-slate-400 text-xs mt-2"><i
                        class="fas fa-check-circle text-green-500 mr-1"></i> Đã xuất bản
                </p>
            </div>
            <i class="fas fa-utensils absolute right-4 top-6 text-6xl text-[#9b2226] opacity-10"></i>
        </div>
        <div
            class="bg-white dark:bg-slate-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 relative overflow-hidden group transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
            <div class="relative z-10">
                <p class="text-amber-600 text-sm font-bold uppercase tracking-wider mb-1">LƯỢT XEM</p>
                <h3 class="text-3xl font-extrabold text-gray-800 dark:text-white">{{ number_format($totalViews) }}</h3>
                <p class="text-gray-400 dark:text-slate-400 text-xs mt-2">
                    <span class="text-[#9b2226]">Công thức: {{ number_format($postViews) }}</span>
                </p>
            </div>
            <i class="fas fa-eye absolute right-4 top-6 text-6xl text-amber-500 opacity-10"></i>
        </div>
        <div
            class="bg-white dark:bg-slate-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 relative overflow-hidden group transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
            <div class="relative z-10">
                <p class="text-orange-500 text-sm font-bold uppercase tracking-wider mb-1">CHỜ DUYỆT</p>
                <h3 class="text-3xl font-extrabold text-gray-800 dark:text-white">{{ number_format($pendingReviews) }}</h3>
                <p class="text-gray-400 dark:text-slate-400 text-xs mt-2">Cần xử lý ngay</p>
            </div>
            <i class="fas fa-clipboard-list absolute right-4 top-6 text-6xl text-orange-400 opacity-10"></i>
        </div>
        <div
            class="bg-white dark:bg-slate-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 relative overflow-hidden group transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
            <div class="relative z-10">
                <p class="text-teal-600 text-sm font-bold uppercase tracking-wider mb-1">THÀNH VIÊN</p>
                <h3 class="text-3xl font-extrabold text-gray-800 dark:text-white">{{ number_format($totalUsers) }}</h3>
                <p class="text-gray-400 dark:text-slate-400 text-xs mt-2">Đang hoạt động</p>
            </div>
            <i class="fas fa-users absolute right-4 top-6 text-6xl text-teal-500 opacity-10"></i>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div
            class="lg:col-span-2 bg-white dark:bg-slate-800 p-6 rounded-lg shadow-sm border border-gray-100 dark:border-slate-700 transition-colors duration-300">
            <h4 class="text-[#9b2226] dark:text-red-400 font-bold mb-6">Biểu đồ phát triển</h4>
            <div class="relative h-80 w-full"><canvas id="reviewChart"></canvas></div>
        </div>
        <div
            class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden transition-colors duration-300">
            <div class="p-4 border-b border-gray-100 dark:border-slate-700 bg-blue-50 dark:bg-slate-700">
                <h4 class="text-blue-600 dark:text-blue-400 font-bold text-sm">Chi tiết theo tháng</h4>
            </div>
            <div class="overflow-y-auto max-h-[340px] custom-scrollbar">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 dark:bg-slate-700 text-gray-500 dark:text-slate-300 font-medium sticky top-0">
                        <tr>
                            <th class="px-4 py-3">Tháng</th>
                            <th class="px-4 py-3 text-right">Bài viết</th>
                            <th class="px-4 py-3 text-right">Thành viên mới</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                        @foreach($tableData as $row)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700">
                                <td class="px-4 py-3 font-medium text-gray-700 dark:text-slate-200">{{ $row['month'] }}</td>
                                <td class="px-4 py-3 text-right text-blue-600 dark:text-blue-400 font-bold">
                                    {{ $row['reviews'] }}
                                </td>
                                <td class="px-4 py-3 text-right text-green-600 dark:text-green-400">{{ $row['users'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div id="reviews-container"
            class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden transition-colors duration-300 flex flex-col min-h-[300px] relative">
            <div id="reviews-loading"
                class="absolute inset-0 bg-white/80 dark:bg-slate-800/80 flex items-center justify-center z-10 hidden rounded-lg">
                <i class="fas fa-spinner fa-spin text-2xl text-blue-500"></i>
            </div>
            <div id="reviews-content">
                @include('admin.partials.dashboard-reviews', ['monthlyReviewsList' => $monthlyReviewsList, 'selectedMonth' => $selectedMonth, 'selectedYear' => $selectedYear])
            </div>
        </div>

        <div id="users-container"
            class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden transition-colors duration-300 flex flex-col min-h-[300px] relative">
            <div id="users-loading"
                class="absolute inset-0 bg-white/80 dark:bg-slate-800/80 flex items-center justify-center z-10 hidden rounded-lg">
                <i class="fas fa-spinner fa-spin text-2xl text-green-500"></i>
            </div>
            <div id="users-content">
                @include('admin.partials.dashboard-users', ['monthlyUsersList' => $monthlyUsersList, 'selectedMonth' => $selectedMonth, 'selectedYear' => $selectedYear])
            </div>
        </div>
    </div>


    <script>
        // AJAX functionality for filter and pagination
        document.addEventListener('DOMContentLoaded', function () {
            // Chart initialization
            const ctx = document.getElementById('reviewChart').getContext('2d');
            const labels = @json($labels);
            const dataReviews = @json($dataReviews);
            const dataUsers = @json($dataViews);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        { label: 'Công thức', data: dataReviews, borderColor: '#9b2226', backgroundColor: 'rgba(155, 34, 38, 0.1)', borderWidth: 2, tension: 0.4, fill: true },
                        { label: 'Thành viên', data: dataUsers, borderColor: '#E85D04', backgroundColor: 'rgba(232, 93, 4, 0.05)', borderWidth: 2, borderDash: [5, 5], tension: 0.4, fill: false }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true }, x: { grid: { display: false } } } }
            });

            const filterMonth = document.getElementById('filter-month');
            const filterYear = document.getElementById('filter-year');
            const reviewsContent = document.getElementById('reviews-content');
            const usersContent = document.getElementById('users-content');

            // Show/hide loading via opacity
            function showLoading(element) {
                if (element) element.classList.add('opacity-50', 'pointer-events-none');
            }

            function hideLoading(element) {
                if (element) element.classList.remove('opacity-50', 'pointer-events-none');
            }

            function showBothLoading() {
                showLoading(reviewsContent);
                showLoading(usersContent);
            }

            function hideBothLoading() {
                hideLoading(reviewsContent);
                hideLoading(usersContent);
            }

            // Load reviews via AJAX
            function loadReviews(page = 1) {
                const month = filterMonth.value;
                const year = filterYear.value;
                const reviewsContent = document.getElementById('reviews-content');

                showLoading(reviewsContent);

                return fetch(`{{ route('admin.dashboard.reviews') }}?month=${month}&year=${year}&page=${page}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(response => response.text())
                    .then(html => {
                        reviewsContent.innerHTML = html;
                        hideLoading(reviewsContent);
                    })
                    .catch(error => {
                        console.error('Error loading reviews:', error);
                        hideLoading(reviewsContent);
                    });
            }

            // Load users via AJAX
            function loadUsers(page = 1) {
                const month = filterMonth.value;
                const year = filterYear.value;
                const usersContent = document.getElementById('users-content');

                showLoading(usersContent);

                return fetch(`{{ route('admin.dashboard.users') }}?month=${month}&year=${year}&page=${page}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(response => response.text())
                    .then(html => {
                        usersContent.innerHTML = html;
                        hideLoading(usersContent);
                    })
                    .catch(error => {
                        console.error('Error loading users:', error);
                        hideLoading(usersContent);
                    });
            }


            // AJAX Pagination - attach to PARENT containers with CAPTURE phase
            const usersWrapper = document.getElementById('users-container');
            const reviewsWrapper = document.getElementById('reviews-container');

            function handlePaginationClick(container, contentId) {
                return function (e) {
                    const link = e.target.closest('.dashboard-pg-link');
                    if (!link) return;

                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();

                    const url = link.getAttribute('href');
                    const contentEl = document.getElementById(contentId);

                    if (contentEl) contentEl.classList.add('opacity-50', 'pointer-events-none');

                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(r => r.text())
                        .then(html => {
                            const contentEl = document.getElementById(contentId);
                            if (contentEl) {
                                contentEl.innerHTML = html;
                                contentEl.classList.remove('opacity-50', 'pointer-events-none');
                            }
                        })
                        .catch(err => {
                            console.error('[Pagination] Error:', err);
                            const contentEl = document.getElementById(contentId);
                            if (contentEl) contentEl.classList.remove('opacity-50', 'pointer-events-none');
                        });
                };
            }

            if (usersWrapper) {
                usersWrapper.addEventListener('click', handlePaginationClick(usersWrapper, 'users-content'), true);
            }

            if (reviewsWrapper) {
                reviewsWrapper.addEventListener('click', handlePaginationClick(reviewsWrapper, 'reviews-content'), true);
            }

            // Custom Dropdown Functionality
            const monthDropdown = document.getElementById('month-dropdown');
            const yearDropdown = document.getElementById('year-dropdown');
            const monthDisplay = document.getElementById('month-display');
            const yearDisplay = document.getElementById('year-display');
            const allDropdowns = document.querySelectorAll('.custom-dropdown');

            // Toggle dropdown
            function toggleDropdown(dropdown) {
                const isOpen = dropdown.classList.contains('open');
                // Close all dropdowns first
                allDropdowns.forEach(d => d.classList.remove('open'));
                // Toggle current
                if (!isOpen) {
                    dropdown.classList.add('open');
                }
            }

            // Close dropdowns when clicking outside
            document.addEventListener('click', function (e) {
                if (!e.target.closest('.custom-dropdown')) {
                    allDropdowns.forEach(d => d.classList.remove('open'));
                }
            });

            // Month dropdown trigger
            monthDropdown.querySelector('.custom-dropdown-trigger').addEventListener('click', function (e) {
                e.stopPropagation();
                toggleDropdown(monthDropdown);
            });

            // Year dropdown trigger
            yearDropdown.querySelector('.custom-dropdown-trigger').addEventListener('click', function (e) {
                e.stopPropagation();
                toggleDropdown(yearDropdown);
            });

            // Month item selection
            monthDropdown.querySelectorAll('.custom-dropdown-item').forEach(item => {
                item.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const value = this.dataset.value;

                    // Update hidden input
                    filterMonth.value = value;

                    // Update display text
                    monthDisplay.textContent = 'Tháng ' + value;

                    // Update active state
                    monthDropdown.querySelectorAll('.custom-dropdown-item').forEach(i => i.classList.remove('active'));
                    this.classList.add('active');

                    // Close dropdown
                    monthDropdown.classList.remove('open');

                    // Load data
                    showBothLoading();
                    Promise.all([
                        loadReviews(1),
                        loadUsers(1)
                    ]).then(() => {
                        hideBothLoading();
                        updateURL();
                    });
                });
            });

            // Year item selection
            yearDropdown.querySelectorAll('.custom-dropdown-item').forEach(item => {
                item.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const value = this.dataset.value;
                    const month = filterMonth.value;

                    // Close dropdown with animation first
                    yearDropdown.classList.remove('open');

                    // Navigate to new page
                    setTimeout(() => {
                        window.location.href = `{{ route('admin.dashboard') }}?month=${month}&year=${value}`;
                    }, 150);
                });
            });

            // Update browser URL
            function updateURL() {
                const month = filterMonth.value;
                const year = filterYear.value;
                const newUrl = `{{ route('admin.dashboard') }}?month=${month}&year=${year}`;
                window.history.pushState({}, '', newUrl);

                const exportBtn = document.getElementById('export-excel-btn');
                if (exportBtn) {
                    exportBtn.href = `{{ route('admin.dashboard.export') }}?month=${month}&year=${year}`;
                }
            }
        });
    </script>
@endsection
