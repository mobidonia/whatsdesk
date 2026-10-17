@extends('setup::layouts.setup')

@section('content')
<div class="p-8">
    <div class="text-center mb-8">
        <h2 class="text-xl font-semibold text-zinc-900 dark:text-white mb-2">Welcome to the Setup Wizard</h2>
        <p class="text-zinc-500 dark:text-zinc-400">
            Let's get your application up and running in just a few minutes.
        </p>
    </div>

    <div class="space-y-4 mb-8">
        <div class="flex items-start gap-4 p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/50">
            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex-shrink-0">
                <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                </svg>
            </div>
            <div>
                <h3 class="font-medium text-zinc-900 dark:text-white">Site Configuration</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    Set up your application Environment.
                </p>
            </div>
        </div>

        <div class="flex items-start gap-4 p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/50">
            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex-shrink-0">
                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
                </svg>
            </div>
            <div>
                <h3 class="font-medium text-zinc-900 dark:text-white">Database Configuration</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    Connect to your MySQL database to store application data.
                </p>
            </div>
        </div>


        <div class="flex items-start gap-4 p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/50">
            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex-shrink-0">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h3 class="font-medium text-zinc-900 dark:text-white">Finalize Installation</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    Run database migrations and complete the setup process.
                </p>
            </div>
        </div>
    </div>

   
    <div class="flex justify-end">
        <a href="{{ route('setup.site') }}" class="inline-flex items-center justify-center px-6 py-3 bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 font-medium rounded-xl hover:bg-zinc-800 dark:hover:bg-zinc-100 transition-colors">
            Get Started
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    </div>
</div>
@endsection
