@props(['company', 'title' => null, 'showSearch' => true])

@extends('knowledge::layouts.frontend')

@section('title', $title ?? ($company->name . ' - Knowledge Base'))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50">
    <!-- Header Section -->
    <div class="relative bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Top Navigation -->
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('static-page', $company->subdomain) }}" class="flex items-center">
                        @if($company->logo)
                            <img class="h-8 w-auto" src="{{ $company->logom }}" alt="{{ $company->name }}">
                        @else
                            <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-sm">{{ substr($company->name, 0, 1) }}</span>
                            </div>
                        @endif
                        <span class="ml-3 text-xl font-semibold text-gray-900">{{ $company->name }}</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    @if($showSearch)
                        <!-- Search Icon -->
                        <button onclick="document.querySelector('#search-input').focus()" class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    @endif
                    <button onclick="toggleDarkMode()" class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{ $slot }}
</div>

<script>
    // Keyboard shortcut for search
    document.addEventListener('keydown', function(e) {
        if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
            e.preventDefault();
            const searchInput = document.querySelector('#search-input, input[name="q"]');
            if (searchInput) searchInput.focus();
        }
    });

    // Dark mode toggle
    function toggleDarkMode() {
        document.documentElement.classList.toggle('dark');
        localStorage.setItem('darkMode', document.documentElement.classList.contains('dark'));
    }

    // Load dark mode preference
    if (localStorage.getItem('darkMode') === 'true') {
        document.documentElement.classList.add('dark');
    }
</script>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection
