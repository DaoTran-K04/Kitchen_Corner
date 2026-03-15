@extends('layouts.admin')
@section('title', 'Quản Lý Thành Viên')
@section('header', 'Quản Lý Thành Viên')

@section('content')
    @php
        $totalUsers = \App\Models\User::count();
        $adminCount = \App\Models\User::where('role', 'admin')->count();
        $memberCount = $totalUsers - $adminCount;
    @endphp

    <div
        class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
        {{-- Header --}}
        <div
            class="p-4 border-b border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-700 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-4">
                <span class="font-bold text-gray-700 dark:text-slate-200 flex items-center gap-2">
                    <i class="fas fa-users text-blue-500"></i>Tất cả thành viên
                </span>
                <div class="flex gap-2 text-xs">
                    <span
                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300 rounded-full font-bold whitespace-nowrap">
                        <i class="fas fa-users"></i>Tổng: {{ $totalUsers }}
                    </span>
                    <span
                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-300 rounded-full font-bold whitespace-nowrap">
                        <i class="fas fa-shield-alt"></i>Admin: {{ $adminCount }}
                    </span>
                    <span
                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-300 rounded-full font-bold whitespace-nowrap">
                        <i class="fas fa-user"></i>Thành viên: {{ $memberCount }}
                    </span>
                </div>
            </div>
            {{-- Search --}}
            <div class="flex items-center gap-2">
                <div class="relative">
                    <input type="text" id="searchInput" value="{{ request('search') }}" placeholder="Tìm tên hoặc email..."
                        class="w-56 pl-9 pr-3 py-2 text-sm border border-gray-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none placeholder:italic placeholder:text-gray-400 dark:placeholder:text-slate-400">
                    <i
                        class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-slate-400 text-sm"></i>
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

        {{-- Bảng --}}
        <div class="overflow-x-auto" id="usersTableContainer">
            @include('admin.users._table', ['users' => $users])
        </div>

        <div id="paginationContainer">
            @if($users->hasPages())
                <div class="p-4 border-t border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-700">
                    {{ $users->links('vendor.pagination.admin') }}
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            const searchBtn = document.getElementById('searchBtn');
            const clearBtn = document.getElementById('clearSearch');
            const tableContainer = document.getElementById('usersTableContainer');
            const paginationContainer = document.getElementById('paginationContainer');
            const searchBtnIcon = document.getElementById('searchBtnIcon');
            const loadingIcon = document.getElementById('loadingIcon');

            let debounceTimer;
            let currentSearch = searchInput.value;

            function performSearch(query) {
                // Show loading on button
                searchBtnIcon.classList.add('hidden');
                loadingIcon.classList.remove('hidden');
                searchBtn.disabled = true;
                tableContainer.style.opacity = '0.5';

                fetch(`{{ route('admin.users.index') }}?search=${encodeURIComponent(query)}&ajax=1`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(response => response.json())
                    .then(data => {
                        tableContainer.innerHTML = data.table;
                        paginationContainer.innerHTML = data.pagination;

                        // Update URL without reload
                        const url = new URL(window.location.href);
                        if (query) {
                            url.searchParams.set('search', query);
                        } else {
                            url.searchParams.delete('search');
                        }
                        window.history.replaceState({}, '', url);

                        // Toggle clear button
                        clearBtn.classList.toggle('hidden', !query);
                    })
                    .finally(() => {
                        searchBtnIcon.classList.remove('hidden');
                        loadingIcon.classList.add('hidden');
                        searchBtn.disabled = false;
                        tableContainer.style.opacity = '1';
                    });
            }

            // Debounced search on input
            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    const query = this.value.trim();
                    if (query !== currentSearch) {
                        currentSearch = query;
                        performSearch(query);
                    }
                }, 300);
            });

            // Click search button
            searchBtn.addEventListener('click', function () {
                const query = searchInput.value.trim();
                currentSearch = query;
                performSearch(query);
            });

            // Enter key
            searchInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const query = this.value.trim();
                    currentSearch = query;
                    performSearch(query);
                }
            });

            clearBtn.addEventListener('click', function () {
                searchInput.value = '';
                currentSearch = '';
                performSearch('');
                searchInput.focus();
            });

            // Handle pagination clicks via AJAX
            function handlePaginationClick(e) {
                const link = e.target.closest('a[href]');
                if (!link) return;

                e.preventDefault();
                const url = new URL(link.href);
                url.searchParams.set('ajax', '1');

                // Show loading
                tableContainer.style.opacity = '0.5';

                fetch(url.toString(), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(response => response.json())
                    .then(data => {
                        tableContainer.innerHTML = data.table;
                        paginationContainer.innerHTML = data.pagination;

                        // Update URL
                        const newUrl = new URL(link.href);
                        window.history.replaceState({}, '', newUrl);

                        // Scroll to top of table
                        tableContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    })
                    .finally(() => {
                        tableContainer.style.opacity = '1';
                    });
            }

            paginationContainer.addEventListener('click', handlePaginationClick);
        });
    </script>
@endsection