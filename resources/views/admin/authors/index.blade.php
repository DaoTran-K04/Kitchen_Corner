@extends('layouts.admin')
@section('title', 'Quản Lý Tác Giả')
@section('header', 'Quản Lý Tác Giả')

@section('content')
    <div
        class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden transition-colors duration-300">
        {{-- Header with Tabs and Actions --}}
        <div class="p-6 border-b border-gray-100 dark:border-slate-700">
            <div class="flex flex-wrap justify-between items-center gap-4">
                <div>
                    <h2 class="font-bold text-gray-800 dark:text-white text-lg">Danh sách Tác Giả</h2>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">
                        Tổng cộng: <span class="font-bold">{{ $authors->total() }}</span> tác giả
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    {{-- Filter quốc tịch - Enhanced Dropdown --}}
                    <div class="relative" id="nationality-dropdown-wrapper">
                        <button type="button" id="nationality-dropdown-btn" onclick="toggleNationalityDropdown()"
                            class="flex items-center gap-2 pl-4 pr-3 py-2.5 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-slate-700 dark:to-slate-600 border border-gray-200 dark:border-slate-500 rounded-xl text-sm text-gray-700 dark:text-slate-200 hover:border-blue-400 dark:hover:border-blue-500 hover:shadow-md transition-all duration-300 min-w-[180px] group">
                            <i
                                class="fas fa-globe text-blue-500 group-hover:rotate-12 transition-transform duration-300"></i>
                            <span id="nationality-selected-text" class="flex-1 text-left font-medium">
                                {{ request('nationality') ?: 'Tất cả quốc tịch' }}
                            </span>
                            <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-300"
                                id="nationality-arrow"></i>
                        </button>

                        {{-- Dropdown Menu --}}
                        <div id="nationality-dropdown-menu"
                            class="absolute top-full left-0 mt-2 w-full bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-gray-100 dark:border-slate-600 z-50 opacity-0 invisible translate-y-[-10px] transition-all duration-300 overflow-hidden">
                            {{-- Options --}}
                            <div class="max-h-[280px] overflow-y-auto custom-scrollbar py-1">
                                <button type="button" onclick="selectNationality('')"
                                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-left hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors {{ !request('nationality') ? 'bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300' : 'text-gray-700 dark:text-slate-300' }}">
                                    <i class="fas fa-globe-asia text-blue-500"></i>
                                    <span class="font-medium">Tất cả</span>
                                    @if(!request('nationality'))
                                        <i class="fas fa-check ml-auto text-blue-500"></i>
                                    @endif
                                </button>
                                @foreach($nationalities ?? [] as $nationality)
                                    <button type="button" onclick="selectNationality('{{ $nationality }}')"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-left hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors {{ request('nationality') == $nationality ? 'bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300' : 'text-gray-700 dark:text-slate-300' }}">
                                        <i class="fas fa-flag text-gray-400"></i>
                                        <span class="font-medium">{{ $nationality }}</span>
                                        @if(request('nationality') == $nationality)
                                            <i class="fas fa-check ml-auto text-blue-500"></i>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Reset Filter --}}
                    <button onclick="resetFilters()" id="reset-btn"
                        class="{{ request('nationality') ? '' : 'hidden' }} w-9 h-9 rounded-lg bg-gray-200 dark:bg-slate-500 text-gray-700 dark:text-white hover:bg-red-100 dark:hover:bg-red-900/50 hover:text-red-600 transition flex items-center justify-center"
                        title="Reset bộ lọc">
                        <i class="fas fa-undo"></i>
                    </button>

                    {{-- Button Thêm mới --}}
                    <a href="{{ route('admin.authors.create') }}"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium text-sm shadow-sm transition flex items-center gap-2">
                        <i class="fas fa-plus"></i> Thêm mới
                    </a>
                </div>
            </div>
        </div>

        {{-- Table Container (for AJAX reload like Books) --}}
        <div id="authors-table-container">
            @include('admin.authors.table')
        </div>
    </div>

    <script>
        // ===== Enhanced Nationality Dropdown =====
        let dropdownOpen = false;

        function toggleNationalityDropdown() {
            const menu = document.getElementById('nationality-dropdown-menu');
            const arrow = document.getElementById('nationality-arrow');
            dropdownOpen = !dropdownOpen;

            if (dropdownOpen) {
                menu.classList.remove('opacity-0', 'invisible', 'translate-y-[-10px]');
                menu.classList.add('opacity-100', 'visible', 'translate-y-0');
                arrow.classList.add('rotate-180');
            } else {
                closeDropdown();
            }
        }

        function closeDropdown() {
            const menu = document.getElementById('nationality-dropdown-menu');
            const arrow = document.getElementById('nationality-arrow');
            dropdownOpen = false;
            menu.classList.remove('opacity-100', 'visible', 'translate-y-0');
            menu.classList.add('opacity-0', 'invisible', 'translate-y-[-10px]');
            arrow.classList.remove('rotate-180');
        }

        function selectNationality(nationality) {
            closeDropdown();
            filterByNationality(nationality);

            // Update button text immediately
            const text = document.getElementById('nationality-selected-text');
            text.textContent = nationality || 'Tất cả quốc tịch';
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function (e) {
            const wrapper = document.getElementById('nationality-dropdown-wrapper');
            if (wrapper && !wrapper.contains(e.target) && dropdownOpen) {
                closeDropdown();
            }
        });

        // ===== AJAX Filter by Nationality =====
        function filterByNationality(nationality) {
            const url = new URL(window.location.href);

            if (nationality) {
                url.searchParams.set('nationality', nationality);
            } else {
                url.searchParams.delete('nationality');
            }
            url.searchParams.delete('page');

            // Show loading
            const container = document.getElementById('authors-table-container');
            container.style.opacity = '0.5';
            container.style.pointerEvents = 'none';

            fetch(url.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            })
                .then(response => response.text())
                .then(html => {
                    container.innerHTML = html;
                    container.style.opacity = '1';
                    container.style.pointerEvents = 'auto';

                    // Show/hide reset button
                    document.getElementById('reset-btn').classList.toggle('hidden', !nationality);

                    // Update URL
                    window.history.pushState({}, '', url.toString());
                })
                .catch(error => {
                    console.error('Error:', error);
                    container.style.opacity = '1';
                    container.style.pointerEvents = 'auto';
                    window.location.href = url.toString();
                });
        }

        function resetFilters() {
            selectNationality('');
        }

        // Handle browser back/forward
        window.addEventListener('popstate', function (e) {
            location.reload();
        });
    </script>
@endsection