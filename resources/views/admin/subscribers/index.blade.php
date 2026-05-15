@extends('layouts.admin')
@section('title', 'Quản Lý Đăng Ký Nhận Tin')
@section('header', 'Quản Lý Đăng Ký Nhận Tin')

@section('content')
    <div
        class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
        {{-- Header --}}
        <div
            class="p-4 border-b border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-700 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-4">
                <span class="font-bold text-gray-700 dark:text-slate-200 flex items-center gap-2">
                    <i class="fas fa-envelope text-blue-500"></i>Danh sách Người đăng ký Newsletter
                </span>
                <div class="flex gap-2 text-xs">
                    <span
                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300 rounded-full font-bold whitespace-nowrap">
                        <i class="fas fa-users"></i>Tổng: {{ $totalCount }}
                    </span>
                    <span
                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-300 rounded-full font-bold whitespace-nowrap">
                        <i class="fas fa-check"></i>Active: {{ $activeCount }}
                    </span>
                    <span
                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-gray-100 dark:bg-gray-900/40 text-gray-600 dark:text-gray-300 rounded-full font-bold whitespace-nowrap">
                        <i class="fas fa-ban"></i>Inactive: {{ $inactiveCount }}
                    </span>
                </div>
            </div>
            {{-- Actions --}}
            <div class="flex items-center gap-2">
                {{-- Search --}}
                <div class="relative">
                    <input type="text" id="searchInput" value="{{ request('search') }}" placeholder="Tìm email..."
                        class="w-48 pl-9 pr-3 py-2 text-sm border border-gray-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none placeholder:italic placeholder:text-gray-400 dark:placeholder:text-slate-400">
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
                {{-- Export --}}
                <a href="{{ route('admin.subscribers.export') }}"
                    class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                    <i class="fas fa-file-csv"></i> Xuất CSV
                </a>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto" id="subscribersTableContainer">
            @include('admin.subscribers._table', ['subscribers' => $subscribers])
        </div>

        <div id="paginationContainer">
            @if($subscribers->hasPages())
                <div class="p-4 border-t border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-700">
                    {{ $subscribers->links('vendor.pagination.admin') }}
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            const searchBtn = document.getElementById('searchBtn');
            const clearBtn = document.getElementById('clearSearch');
            const tableContainer = document.getElementById('subscribersTableContainer');
            const paginationContainer = document.getElementById('paginationContainer');
            const searchBtnIcon = document.getElementById('searchBtnIcon');
            const loadingIcon = document.getElementById('loadingIcon');

            let debounceTimer;
            let currentSearch = searchInput.value;

            function performSearch(query) {
                searchBtnIcon.classList.add('hidden');
                loadingIcon.classList.remove('hidden');
                searchBtn.disabled = true;
                tableContainer.style.opacity = '0.5';

                fetch(`{{ route('admin.subscribers.index') }}?search=${encodeURIComponent(query)}&ajax=1`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(response => response.json())
                    .then(data => {
                        tableContainer.innerHTML = data.table;
                        paginationContainer.innerHTML = data.pagination;

                        const url = new URL(window.location.href);
                        if (query) {
                            url.searchParams.set('search', query);
                        } else {
                            url.searchParams.delete('search');
                        }
                        window.history.replaceState({}, '', url);
                        clearBtn.classList.toggle('hidden', !query);
                    })
                    .finally(() => {
                        searchBtnIcon.classList.remove('hidden');
                        loadingIcon.classList.add('hidden');
                        searchBtn.disabled = false;
                        tableContainer.style.opacity = '1';
                    });
            }

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

            searchBtn.addEventListener('click', function () {
                const query = searchInput.value.trim();
                currentSearch = query;
                performSearch(query);
            });

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

            function handlePaginationClick(e) {
                const link = e.target.closest('a[href]');
                if (!link) return;

                e.preventDefault();
                const url = new URL(link.href);
                url.searchParams.set('ajax', '1');

                tableContainer.style.opacity = '0.5';

                fetch(url.toString(), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(response => response.json())
                    .then(data => {
                        tableContainer.innerHTML = data.table;
                        paginationContainer.innerHTML = data.pagination;

                        const newUrl = new URL(link.href);
                        window.history.replaceState({}, '', newUrl);
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
