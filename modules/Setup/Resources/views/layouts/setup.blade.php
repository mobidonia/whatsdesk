<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - Setup</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-zinc-50 dark:bg-zinc-950 antialiased">
        <div class="min-h-screen flex flex-col items-center justify-center p-6">
            <!-- Logo / Header -->
            <div class="mb-8 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-zinc-900 dark:bg-white mb-4">
                    <svg class="w-8 h-8 text-white dark:text-zinc-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-semibold text-zinc-900 dark:text-white">{{ config('app.name', 'Laravel') }}</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Installation Wizard</p>
            </div>

            <!-- Progress Steps -->
            @php
                $steps = [
                    ['name' => 'Welcome', 'route' => 'setup.index'],
                    ['name' => 'Site', 'route' => 'setup.site'],
                    ['name' => 'Database', 'route' => 'setup.database'],
                    ['name' => 'Finalize', 'route' => 'setup.finalize'],
                ];
                $displayStep = $currentStep ?? 0;
            @endphp

            <div class="mb-8 w-full max-w-lg">
                <div class="flex items-center justify-between">
                    @foreach($steps as $index => $step)
                        <div class="flex items-center {{ $index < count($steps) - 1 ? 'flex-1' : '' }}">
                            <div class="flex items-center justify-center w-8 h-8 rounded-full text-sm font-medium transition-colors
                                @if($index < $displayStep)
                                    bg-emerald-500 text-white
                                @elseif($index === $displayStep)
                                    bg-zinc-900 dark:bg-white text-white dark:text-zinc-900
                                @else
                                    bg-zinc-200 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400
                                @endif
                            ">
                                @if($index < $displayStep)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                @else
                                    {{ $index + 1 }}
                                @endif
                            </div>
                            <span class="ml-2 text-sm font-medium {{ $index === $displayStep ? 'text-zinc-900 dark:text-white' : 'text-zinc-500 dark:text-zinc-400' }} hidden sm:inline">
                                {{ $step['name'] }}
                            </span>
                            @if($index < count($steps) - 1)
                                <div class="flex-1 mx-4 h-0.5 {{ $index < $displayStep ? 'bg-emerald-500' : 'bg-zinc-200 dark:bg-zinc-800' }}"></div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Main Content Card -->
            <div class="w-full max-w-lg bg-white dark:bg-zinc-900 rounded-2xl shadow-xl shadow-zinc-200/50 dark:shadow-none border border-zinc-200 dark:border-zinc-800 overflow-hidden">
                @yield('content')
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</p>
            </div>
        </div>

    </body>
</html>
