@extends('setup::layouts.setup')

@section('content')
<div class="p-8">
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-zinc-900 dark:text-white mb-2">Database Configuration</h2>
        <p class="text-zinc-500 dark:text-zinc-400">
            Connect your MySQL database to store application data.
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
                <h4 class="font-medium text-blue-800 dark:text-blue-200 text-sm">Where to find these credentials</h4>
                <ul class="text-sm text-blue-700 dark:text-blue-300 mt-1 space-y-1">
                    <li>• <strong>cPanel:</strong> MySQL Databases → Create or select a database</li>
                    <li>• <strong>Plesk:</strong> Databases → Add Database</li>
                    <li>• <strong>Local (MAMP/XAMPP):</strong> Default is usually localhost:3306, root with no password</li>
                    <li>• <strong>Docker:</strong> Check your docker-compose.yml for credentials</li>
                </ul>
            </div>
        </div>
    </div>

    <form action="{{ route('setup.database.save') }}" method="POST" class="space-y-5">
        @csrf

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="host" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
                    Host <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="host" 
                    name="host" 
                    value="{{ old('host', $values['host']) }}"
                    placeholder="127.0.0.1"
                    class="w-full px-4 py-2.5 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-500 focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white focus:border-transparent outline-none transition"
                    required
                />
                @error('host')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="port" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
                    Port <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="port" 
                    name="port" 
                    value="{{ old('port', $values['port']) }}"
                    placeholder="3306"
                    class="w-full px-4 py-2.5 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-500 focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white focus:border-transparent outline-none transition"
                    required
                />
                @error('port')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="database" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
                Database Name <span class="text-red-500">*</span>
            </label>
            <input 
                type="text" 
                id="database" 
                name="database" 
                value="{{ old('database', $values['database']) }}"
                placeholder="supersaas"
                class="w-full px-4 py-2.5 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-500 focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white focus:border-transparent outline-none transition"
                required
            />
            @error('database')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">The database must already exist. Create it first if needed.</p>
        </div>

        <div>
            <label for="username" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
                Username <span class="text-red-500">*</span>
            </label>
            <input 
                type="text" 
                id="username" 
                name="username" 
                value="{{ old('username', $values['username']) }}"
                placeholder="root"
                class="w-full px-4 py-2.5 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-500 focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white focus:border-transparent outline-none transition"
                required
            />
            @error('username')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
                Password
            </label>
            <input 
                type="password" 
                id="password" 
                name="password" 
                value="{{ old('password') }}"
                placeholder="••••••••"
                class="w-full px-4 py-2.5 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-500 focus:ring-2 focus:ring-zinc-900 dark:focus:ring-white focus:border-transparent outline-none transition"
            />
            @error('password')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Leave empty if your database has no password.</p>
        </div>

        <div class="flex justify-between pt-4">
            <a href="{{ route('setup.site') }}" class="inline-flex items-center justify-center px-5 py-2.5 text-zinc-700 dark:text-zinc-300 font-medium rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
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
                Test & Save
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
    </form>
</div>
@endsection
