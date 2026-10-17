@extends('setup::layouts.setup')

@section('content')
<div class="p-8">
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-100 dark:bg-emerald-900/30 mb-4">
            <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <h2 class="text-xl font-semibold text-zinc-900 dark:text-white mb-2">Ready to Finalize</h2>
        <p class="text-zinc-500 dark:text-zinc-400">
            Your configuration is complete. Let's set up the database.
        </p>
    </div>

    @if(session('error'))
        <div class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
            <div class="flex gap-3">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h4 class="font-medium text-red-800 dark:text-red-200 text-sm">Installation Failed</h4>
                    <p class="text-sm text-red-700 dark:text-red-300 mt-1">{{ session('error') }}</p>
                </div>
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

    @if(!$isStorageWritable)
        <div class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
            <div class="flex gap-3">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <h4 class="font-medium text-red-800 dark:text-red-200 text-sm">Permission Error</h4>
                    <p class="text-sm text-red-700 dark:text-red-300 mt-1">
                        The <code class="bg-red-100 dark:bg-red-800 px-1 rounded">storage</code> directory is not writable. Please set proper permissions before continuing.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="space-y-4 mb-8">
        <div class="flex items-start gap-4 p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/50">
            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex-shrink-0">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div>
                <h3 class="font-medium text-zinc-900 dark:text-white">Site Configured</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    Application name and URL have been set.
                </p>
            </div>
        </div>

        <div class="flex items-start gap-4 p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/50">
            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex-shrink-0">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div>
                <h3 class="font-medium text-zinc-900 dark:text-white">Database Connected</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    MySQL connection has been configured and tested.
                </p>
            </div>
        </div>
    </div>

    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 mb-8">
        <div class="flex gap-3">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <h4 class="font-medium text-blue-800 dark:text-blue-200 text-sm">What happens next?</h4>
                <ul class="text-sm text-blue-700 dark:text-blue-300 mt-2 space-y-1">
                    <li>• Database migrations will create all required tables</li>
                    <li>• Initial data will be seeded into the database</li>
                    <li>• An installation marker file will be created</li>
                    <li>• You'll be redirected to the login page — the first account you register becomes the admin</li>
                </ul>
            </div>
        </div>
    </div>

    <form action="{{ route('setup.complete') }}" method="POST" id="finalize-form">
        @csrf

        <div class="flex justify-between">
            <a href="{{ route('setup.database') }}" class="inline-flex items-center justify-center px-5 py-2.5 text-zinc-700 dark:text-zinc-300 font-medium rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back
            </a>

            <button 
                type="submit" 
                id="finalize-btn"
                class="inline-flex items-center justify-center px-6 py-2.5 bg-emerald-600 text-white font-medium rounded-xl hover:bg-emerald-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                @if(!$isStorageWritable) disabled @endif
            >
                <span id="btn-text">Complete Installation</span>
                <svg id="btn-spinner" class="hidden w-4 h-4 ml-2 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg id="btn-arrow" class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </button>
        </div>
    </form>
</div>

<script>
    document.getElementById('finalize-form').addEventListener('submit', function(e) {
        var btn = document.getElementById('finalize-btn');
        var btnText = document.getElementById('btn-text');
        var btnSpinner = document.getElementById('btn-spinner');
        var btnArrow = document.getElementById('btn-arrow');
        
        btn.disabled = true;
        btnText.textContent = 'Installing...';
        btnSpinner.classList.remove('hidden');
        btnArrow.classList.add('hidden');
    });
</script>
@endsection
