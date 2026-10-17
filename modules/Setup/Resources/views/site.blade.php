@extends('setup::layouts.setup')

@section('content')
<div class="p-8">
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-zinc-900 dark:text-white mb-2">Site Configuration</h2>
        <p class="text-zinc-500 dark:text-zinc-400">
            Set up your application name and URL.
        </p>
    </div>

    @if(session('error'))
        <div class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
            <div class="flex gap-3">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm text-red-700 dark:text-red-300">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800">
            <div class="flex gap-3">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm text-emerald-700 dark:text-emerald-300">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(!$isWritable)
        <div class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
            <div class="flex gap-3">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <h4 class="font-medium text-red-800 dark:text-red-200 text-sm">Permission Error</h4>
                    <p class="text-sm text-red-700 dark:text-red-300 mt-1">
                        The <code class="bg-red-100 dark:bg-red-800 px-1 rounded">.env</code> file is not writable. Please set proper permissions before continuing.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 mb-6">
        <div class="flex gap-3">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <h4 class="font-medium text-blue-800 dark:text-blue-200 text-sm">About these settings</h4>
                <ul class="text-sm text-blue-700 dark:text-blue-300 mt-1 space-y-1">
                    <li>• <strong>Site Name:</strong> Displayed in the browser title and throughout the app</li>
                    <li>• <strong>Site URL:</strong> The full URL where your application is hosted (used for authentication redirects)</li>
                </ul>
            </div>
        </div>
    </div>

    <form action="{{ route('setup.site.save') }}" method="POST" class="space-y-5">
        @csrf

        <div>
            <label for="app_name" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
                Site Name <span class="text-red-500">*</span>
            </label>
            <input 
                type="text" 
                id="app_name" 
                name="app_name" 
                value="{{ old('app_name', $values['app_name']) }}"
                placeholder="My Awesome App"
                class="w-full px-4 py-2.5 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-500 focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white focus:border-transparent outline-none transition"
                required
            />
            @error('app_name')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">This will be displayed in the browser tab and throughout your application.</p>
        </div>

        <div>
            <label for="app_url" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
                Site URL <span class="text-red-500">*</span>
            </label>
            <input 
                type="url" 
                id="app_url" 
                name="app_url" 
                value="{{ old('app_url', $values['app_url']) }}"
                placeholder="https://example.com"
                class="w-full px-4 py-2.5 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-500 focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white focus:border-transparent outline-none transition"
                required
            />
            @error('app_url')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                The URL has been auto-detected from your current request. Update it if needed.
            </p>
        </div>

        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
            <div class="flex gap-3">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <h4 class="font-medium text-amber-800 dark:text-amber-200 text-sm">Important</h4>
                    <p class="text-sm text-amber-700 dark:text-amber-300 mt-1">
                        Make sure the Site URL matches the domain where your application is hosted.
                    </p>
                </div>
            </div>
        </div>


        <div class="flex justify-between pt-4">
            <a href="{{ route('setup.index') }}" class="inline-flex items-center justify-center px-5 py-2.5 text-zinc-700 dark:text-zinc-300 font-medium rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back
            </a>

            <button
                type="submit"
                class="inline-flex items-center justify-center px-6 py-2.5 bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 font-medium rounded-xl hover:bg-zinc-800 dark:hover:bg-zinc-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                @if(!$isWritable) disabled @endif
            >
                Save & Continue
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
    </form>
</div>
@endsection
