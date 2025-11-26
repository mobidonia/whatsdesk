@extends('knowledge::layouts.frontend')

@section('title', 'Search Results' . ($query ? ' for "' . $query . '"' : '') . ' - ' . $company->name)
@section('description', 'Search results for articles and documentation.')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 transition-colors duration-200">
    <!-- Header Section -->
    <div class="relative bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Top Navigation -->
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('static-page', $company->subdomain) }}" class="flex items-center">
                        @if($company->logo)
                            <img class="h-8 w-8 object-cover rounded-full" src="{{ $company->logom }}" alt="{{ $company->name }}">
                        @else
                            <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-sm">{{ substr($company->name, 0, 1) }}</span>
                            </div>
                        @endif
                        <span class="ml-3 text-xl font-semibold text-gray-900 dark:text-white">{{ $company->name }}</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <button onclick="toggleDarkMode()" class="p-2 text-gray-400 hover:text-gray-600 dark:text-gray-300 dark:hover:text-gray-100 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Breadcrumb -->
    <div class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <nav class="flex" aria-label="Breadcrumb">
                <ol role="list" class="flex items-center space-x-4">
                    <li>
                        <div>
                            <a href="{{ route('static-page', $company->subdomain) }}" class="text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400">
                                <svg class="flex-shrink-0 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                                </svg>
                                <span class="sr-only">Home</span>
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" />
                            </svg>
                            <span class="ml-4 text-sm font-medium text-gray-500 dark:text-gray-400">Search Results</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar -->
            <div class="lg:w-1/4">
                <!-- Search -->
                <div class="mb-8">
                    <form action="{{ route('knowledge.frontend.search', $company->subdomain) }}" method="GET">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                name="q" 
                                value="{{ $query }}"
                                placeholder="Search articles..."
                                class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                                autofocus
                            >
                        </div>
                    </form>
                </div>

                <!-- Categories Navigation -->
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 transition-colors duration-200">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Categories</h3>
                    <nav class="space-y-1">
                        @foreach($categories as $cat)
                        <a href="{{ route('knowledge.frontend.category', [$company->subdomain, $cat->slug]) }}" 
                           class="group flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition-colors text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-gray-100">
                            <span>{{ $cat->name }}</span>
                            <span class="text-xs text-gray-400 dark:text-gray-500">
                                {{ $cat->published_articles_count ?? 0 }}
                            </span>
                        </a>
                        @endforeach
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:w-3/4">
                <!-- Search Header -->
                <div class="mb-8">
                    @if($query)
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                            Search Results
                        </h1>
                        <p class="mt-2 text-lg text-gray-600 dark:text-gray-300">
                            @if($articles instanceof \Illuminate\Pagination\LengthAwarePaginator && $articles->total() > 0)
                                Showing {{ $articles->total() }} {{ $articles->total() == 1 ? 'result' : 'results' }} for "<span class="font-medium">{{ $query }}</span>"
                            @elseif($articles->count() > 0)
                                Found {{ $articles->count() }} {{ $articles->count() == 1 ? 'result' : 'results' }} for "<span class="font-medium">{{ $query }}</span>"
                            @else
                                No results found for "<span class="font-medium">{{ $query }}</span>"
                            @endif
                        </p>
                    @else
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Search Knowledge Base</h1>
                        <p class="mt-2 text-lg text-gray-600 dark:text-gray-300">Enter a search term to find relevant articles and information.</p>
                    @endif
                </div>

                <!-- Search Results -->
                @if($query && $articles instanceof \Illuminate\Pagination\LengthAwarePaginator && $articles->count() > 0)
                    <div class="space-y-6">
                        @foreach($articles as $article)
                        <a href="{{ route('knowledge.frontend.article', [$company->subdomain, $article->slug]) }}" 
                           class="group block bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-600 hover:shadow-lg transition-all duration-200 p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        @if($article->category)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                {{ $article->category->name }}
                                            </span>
                                        @endif
                                        @if($article->is_featured)
                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                Featured
                                            </span>
                                        @endif
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors mb-2">
                                        {!! Str::of($article->title)->replace($query, '<mark class="bg-yellow-200 px-1 rounded">' . $query . '</mark>') !!}
                                    </h3>
                                    @if($article->excerpt)
                                        <p class="text-gray-600 dark:text-gray-300 line-clamp-2 mb-4">
                                            {!! Str::of($article->excerpt)->replace($query, '<mark class="bg-yellow-200 px-1 rounded">' . $query . '</mark>') !!}
                                        </p>
                                    @endif
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                {{ $article->formatted_read_time }}
                                            </span>
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                {{ number_format($article->views_count) }} views
                                            </span>
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4h3a1 1 0 011 1v8a1 1 0 01-1 1H4a1 1 0 01-1-1V8a1 1 0 011-1h3z"></path>
                                                </svg>
                                                {{ $article->created_at->format('M d, Y') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="ml-6 flex-shrink-0">
                                    <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 group-hover:text-indigo-500 dark:group-hover:text-indigo-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($articles->hasPages())
                        <div class="mt-12 flex justify-center">
                            {{ $articles->appends(request()->query())->links() }}
                        </div>
                    @endif

                @elseif($query && $articles->count() == 0)
                    <!-- No Results -->
                    <div class="text-center py-12">
                        <svg class="mx-auto h-24 w-24 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No articles found</h3>
                        <p class="mt-2 text-gray-500 dark:text-gray-400">We couldn't find any articles matching "<span class="font-medium">{{ $query }}</span>". Try:</p>
                        <ul class="mt-4 text-sm text-gray-500 dark:text-gray-400 space-y-1">
                            <li>• Check your spelling</li>
                            <li>• Try different or more general keywords</li>
                            <li>• Browse our categories instead</li>
                        </ul>
                        <div class="mt-6">
                            <a href="{{ route('static-page', $company->subdomain) }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600">
                                <svg class="mr-2 -ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                Browse Categories
                            </a>
                        </div>
                    </div>

                @else
                    <!-- Search Suggestions -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-8 transition-colors duration-200">
                        <div class="text-center">
                            <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Search our knowledge base</h3>
                            <p class="text-gray-500 dark:text-gray-400 mb-6">Enter keywords to find helpful articles and documentation</p>
                            
                            <!-- Popular Search Terms -->
                            <div class="flex flex-wrap justify-center gap-2">
                                <span class="text-sm text-gray-500 dark:text-gray-400 mr-2">Popular searches:</span>
                                @php
                                    $popularTerms = ['getting started', 'account setup', 'billing', 'integrations', 'troubleshooting'];
                                @endphp
                                @foreach($popularTerms as $term)
                                    <a href="{{ route('knowledge.frontend.search', $company->subdomain) }}?q={{ urlencode($term) }}" 
                                       class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                        {{ $term }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    // Keyboard shortcut for search
    document.addEventListener('keydown', function(e) {
        if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
            e.preventDefault();
            document.querySelector('input[name="q"]').focus();
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

    // Auto-focus search input if no query
    document.addEventListener('DOMContentLoaded', function() {
        const queryInput = document.querySelector('input[name="q"]');
        if (queryInput && !queryInput.value) {
            queryInput.focus();
        }
    });
</script>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    mark {
        background-color: #fef3c7;
        padding: 0.125rem 0.25rem;
        border-radius: 0.25rem;
    }
</style>
@endsection
