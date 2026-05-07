@extends('layouts.admin')
@section('title', 'Nhật Ký Hoạt Động')
@section('header', 'Nhật Ký Hoạt Động')

@section('content')
    @php
        $todayCount = \App\Models\AdminActivityLog::whereDate('created_at', today())->count();
        $weekCount = \App\Models\AdminActivityLog::where('created_at', '>=', now()->startOfWeek())->count();
        $totalCount = \App\Models\AdminActivityLog::count();
    @endphp

    <style>
        /* Custom Dropdown Styles */
        .custom-select-wrapper {
            position: relative;
            user-select: none;
        }

        .custom-select-trigger {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding: 0 12px;
            height: 38px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            color: #374151;
            cursor: pointer;
            transition: all 0.2s;
        }

        .dark .custom-select-trigger {
            background: #334155;
            border-color: #475569;
            color: #f1f5f9;
        }

        .custom-select-trigger:hover {
            border-color: #3b82f6;
        }

        .custom-select-wrapper.open .custom-select-trigger {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .custom-options {
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            z-index: 100;
            max-height: 280px;
            overflow-y: auto;
            padding: 4px;
        }

        .dark .custom-options {
            background: #1e293b;
            border-color: #334155;
        }

        .custom-select-wrapper.open .custom-options {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .custom-option {
            padding: 8px 12px;
            font-size: 14px;
            color: #374151;
            cursor: pointer;
            border-radius: 6px;
            transition: all 0.15s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dark .custom-option {
            color: #e2e8f0;
        }

        .custom-option:hover {
            background-color: #f3f4f6;
            color: #2563eb;
        }

        .dark .custom-option:hover {
            background-color: #334155;
            color: #60a5fa;
        }

        .custom-option.selected {
            background-color: #eff6ff;
            color: #2563eb;
            font-weight: 500;
        }

        .dark .custom-option.selected {
            background-color: #1e3a8a;
            color: #93c5fd;
        }

        .arrow {
            transition: transform 0.2s;
            color: #9ca3af;
            font-size: 12px;
        }

        .custom-select-wrapper.open .arrow {
            transform: rotate(180deg);
            color: #3b82f6;
        }

        /* Dropdown opens upward */
        .custom-select-wrapper.dropup .custom-options {
            top: auto;
            bottom: calc(100% + 6px);
        }
    </style>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700">
        {{-- Header --}}
        <div class="p-4 border-b border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-700 rounded-t-xl">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="font-bold text-gray-700 dark:text-slate-200 flex items-center gap-2">
                        <i class="fas fa-clipboard-list text-blue-500"></i>Theo dõi trạng thái hoạt động
                    </span>
                    <div class="flex gap-2 text-xs">
                        <span
                            class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300 rounded-full font-bold">
                            <i class="fas fa-calendar-day"></i>Hôm nay: {{ $todayCount }}
                        </span>
                        <span
                            class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-300 rounded-full font-bold">
                            <i class="fas fa-calendar-week"></i>Tuần này: {{ $weekCount }}
                        </span>
                        <span
                            class="inline-flex items-center gap-1 px-2.5 py-1 bg-gray-100 dark:bg-slate-600 text-gray-600 dark:text-slate-300 rounded-full font-bold">
                            <i class="fas fa-database"></i>Tổng: {{ $totalCount }}
                        </span>
                    </div>
                </div>

                {{-- Search & Actions --}}
                <div class="flex items-center gap-2">
                    <div class="relative">
                        <input type="text" id="searchInput" value="{{ request('search') }}" placeholder="Tìm mô tả..."
                            class="w-56 pl-9 pr-3 py-2 text-sm border border-gray-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none placeholder:italic">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    </div>
                    <button type="button" id="searchBtn"
                        class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                        <i class="fas fa-search" id="searchBtnIcon"></i>
                        <i class="fas fa-spinner fa-spin hidden" id="loadingIcon"></i>
                    </button>
                    <button type="button" id="clearSearch"
                        class="px-3 py-2 bg-gray-200 dark:bg-slate-600 text-gray-700 dark:text-slate-200 text-sm rounded-lg hover:bg-gray-300 dark:hover:bg-slate-500 transition {{ request('search') ? '' : 'hidden' }}">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="p-4 bg-white dark:bg-slate-800 border-b border-gray-100 dark:border-slate-700">
            <div class="flex flex-wrap gap-3 items-end">
                {{-- Admin filter --}}
                <div class="custom-select-wrapper" id="adminSelectWrapper" style="min-width: 160px;">
                    <label class="block text-xs text-gray-500 dark:text-slate-400 mb-1 font-medium">Admin</label>
                    <div class="custom-select-trigger">
                        <span
                            class="trigger-text">{{ request('admin_id') ? ($admins->firstWhere('id', request('admin_id'))->name ?? 'Tất cả') : 'Tất cả' }}</span>
                        <i class="fas fa-chevron-down arrow"></i>
                    </div>
                    <div class="custom-options">
                        <div class="custom-option {{ !request('admin_id') ? 'selected' : '' }}" data-value="">
                            <i class="fas fa-users text-xs w-4"></i> Tất cả
                        </div>
                        @foreach($admins as $admin)
                            <div class="custom-option {{ request('admin_id') == $admin->id ? 'selected' : '' }}"
                                data-value="{{ $admin->id }}">
                                <img src="{{ $admin->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($admin->name) }}"
                                    class="w-4 h-4 rounded-full object-cover flex-shrink-0">
                                {{ $admin->name }}
                            </div>
                        @endforeach
                    </div>
                    <input type="hidden" id="adminFilter" value="{{ request('admin_id') }}">
                </div>

                {{-- Action filter --}}
                <div class="custom-select-wrapper" id="actionSelectWrapper" style="min-width: 140px;">
                    <label class="block text-xs text-gray-500 dark:text-slate-400 mb-1 font-medium">Hành động</label>
                    <div class="custom-select-trigger">
                        <span class="trigger-text">{{ request('action') ? ucfirst(request('action')) : 'Tất cả' }}</span>
                        <i class="fas fa-chevron-down arrow"></i>
                    </div>
                    <div class="custom-options">
                        <div class="custom-option {{ !request('action') ? 'selected' : '' }}" data-value="">
                            <i class="fas fa-list text-xs w-4"></i> Tất cả
                        </div>
                        @foreach($actions as $action)
                            <div class="custom-option {{ request('action') == $action ? 'selected' : '' }}"
                                data-value="{{ $action }}">
                                <i
                                    class="fas fa-{{ $action == 'create' ? 'plus' : ($action == 'update' ? 'edit' : ($action == 'delete' ? 'trash' : ($action == 'approve' ? 'check' : ($action == 'reject' ? 'times' : 'circle')))) }} text-xs w-4"></i>
                                {{ ucfirst($action) }}
                            </div>
                        @endforeach
                    </div>
                    <input type="hidden" id="actionFilter" value="{{ request('action') }}">
                </div>

                {{-- Date From --}}
                <div>
                    <label class="block text-xs text-gray-500 dark:text-slate-400 mb-1 font-medium">Từ ngày</label>
                    <input type="date" id="dateFrom" value="{{ request('date_from') }}" max="{{ date('Y-m-d') }}"
                        class="border border-gray-200 dark:border-slate-600 rounded-lg px-3 py-2 text-sm text-gray-700 dark:text-white bg-white dark:bg-slate-700 focus:ring-2 focus:ring-blue-500 outline-none cursor-pointer h-[38px]">
                </div>

                {{-- Date To --}}
                <div>
                    <label class="block text-xs text-gray-500 dark:text-slate-400 mb-1 font-medium">Đến ngày</label>
                    <input type="date" id="dateTo" value="{{ request('date_to') }}" max="{{ date('Y-m-d') }}"
                        class="border border-gray-200 dark:border-slate-600 rounded-lg px-3 py-2 text-sm text-gray-700 dark:text-white bg-white dark:bg-slate-700 focus:ring-2 focus:ring-blue-500 outline-none cursor-pointer h-[38px]">
                </div>

                {{-- Buttons --}}
                <button type="button" id="applyFilters"
                    class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition flex items-center gap-1.5 h-[38px]">
                    <i class="fas fa-filter"></i>Áp dụng
                </button>
                <button type="button" id="clearFilters"
                    class="px-4 py-2 bg-gray-200 dark:bg-slate-600 text-gray-700 dark:text-slate-200 text-sm font-medium rounded-lg hover:bg-red-100 dark:hover:bg-red-900/50 hover:text-red-600 dark:hover:text-red-400 transition flex items-center gap-1.5 h-[38px]"
                    title="Xóa bộ lọc">
                    <i class="fas fa-rotate-left"></i>
                </button>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto" id="logsTableContainer">
            @include('admin.activity-logs._table', ['logs' => $logs])
        </div>

        {{-- Pagination --}}
        <div id="paginationContainer">
            @if($logs->hasPages())
                <div class="p-4 border-t border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-700 rounded-b-xl">
                    {{ $logs->links('vendor.pagination.admin') }}
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Custom Dropdowns
            function setupDropdowns() {
                document.querySelectorAll('.custom-select-wrapper').forEach(wrapper => {
                    const trigger = wrapper.querySelector('.custom-select-trigger');
                    const options = wrapper.querySelectorAll('.custom-option');
                    const input = wrapper.querySelector('input[type="hidden"]');
                    const triggerText = wrapper.querySelector('.trigger-text');

                    trigger.addEventListener('click', e => {
                        e.stopPropagation();
                        document.querySelectorAll('.custom-select-wrapper').forEach(w => {
                            if (w !== wrapper) w.classList.remove('open');
                        });
                        wrapper.classList.toggle('open');
                    });

                    options.forEach(option => {
                        option.addEventListener('click', e => {
                            e.stopPropagation();
                            triggerText.textContent = option.textContent.trim();
                            input.value = option.dataset.value;
                            options.forEach(o => o.classList.remove('selected'));
                            option.classList.add('selected');
                            wrapper.classList.remove('open');
                        });
                    });
                });

                document.addEventListener('click', () => {
                    document.querySelectorAll('.custom-select-wrapper').forEach(w => w.classList.remove('open'));
                });
            }
            setupDropdowns();

            // Elements
            const searchInput = document.getElementById('searchInput');
            const searchBtn = document.getElementById('searchBtn');
            const clearSearchBtn = document.getElementById('clearSearch');
            const clearFiltersBtn = document.getElementById('clearFilters');
            const applyFiltersBtn = document.getElementById('applyFilters');
            const tableContainer = document.getElementById('logsTableContainer');
            const paginationContainer = document.getElementById('paginationContainer');
            const searchBtnIcon = document.getElementById('searchBtnIcon');
            const loadingIcon = document.getElementById('loadingIcon');
            const adminFilter = document.getElementById('adminFilter');
            const actionFilter = document.getElementById('actionFilter');
            const dateFrom = document.getElementById('dateFrom');
            const dateTo = document.getElementById('dateTo');

            let debounceTimer;

            function buildQuery(page = 1) {
                const params = new URLSearchParams();
                if (page > 1) params.set('page', page);
                if (searchInput.value.trim()) params.set('search', searchInput.value.trim());
                if (adminFilter.value) params.set('admin_id', adminFilter.value);
                if (actionFilter.value) params.set('action', actionFilter.value);
                if (dateFrom.value) params.set('date_from', dateFrom.value);
                if (dateTo.value) params.set('date_to', dateTo.value);
                params.set('ajax', '1');
                return params.toString();
            }

            function performSearch(page = 1) {
                searchBtnIcon.classList.add('hidden');
                loadingIcon.classList.remove('hidden');
                searchBtn.disabled = true;
                tableContainer.style.opacity = '0.5';

                fetch(`{{ route('admin.activity-logs.index') }}?${buildQuery(page)}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(r => r.json())
                    .then(data => {
                        tableContainer.innerHTML = data.table;
                        paginationContainer.innerHTML = data.pagination;

                        const url = new URL(window.location.href);
                        url.search = buildQuery(page).replace('&ajax=1', '').replace('ajax=1', '');
                        history.replaceState({}, '', url);

                        clearSearchBtn.classList.toggle('hidden', !searchInput.value.trim());
                        attachPaginationListeners();
                    })
                    .finally(() => {
                        searchBtnIcon.classList.remove('hidden');
                        loadingIcon.classList.add('hidden');
                        searchBtn.disabled = false;
                        tableContainer.style.opacity = '1';
                    });
            }

            function attachPaginationListeners() {
                paginationContainer.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', function (e) {
                        e.preventDefault();
                        const href = this.getAttribute('href');
                        if (href) {
                            const page = new URLSearchParams(href.split('?')[1]).get('page') || 1;
                            performSearch(page);
                        }
                    });
                });
            }
            attachPaginationListeners();

            // Event Listeners
            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => performSearch(), 300);
            });

            searchBtn.addEventListener('click', () => performSearch());

            searchInput.addEventListener('keydown', e => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    performSearch();
                }
            });

            clearSearchBtn.addEventListener('click', () => {
                searchInput.value = '';
                performSearch();
                searchInput.focus();
            });

            applyFiltersBtn.addEventListener('click', () => performSearch());

            clearFiltersBtn.addEventListener('click', () => {
                // Reset dropdowns
                adminFilter.value = '';
                actionFilter.value = '';
                document.querySelector('#adminSelectWrapper .trigger-text').textContent = 'Tất cả';
                document.querySelectorAll('#adminSelectWrapper .custom-option').forEach(o => o.classList.remove('selected'));
                document.querySelector('#adminSelectWrapper .custom-option[data-value=""]').classList.add('selected');
                document.querySelector('#actionSelectWrapper .trigger-text').textContent = 'Tất cả';
                document.querySelectorAll('#actionSelectWrapper .custom-option').forEach(o => o.classList.remove('selected'));
                document.querySelector('#actionSelectWrapper .custom-option[data-value=""]').classList.add('selected');

                // Reset dates & search
                dateFrom.value = '';
                dateTo.value = '';
                searchInput.value = '';

                performSearch();
            });
        });
    </script>
@endsection
