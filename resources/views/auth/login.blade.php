<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        @if (config('settings.show_demo_credentials',false))
        <div class="flex items-center justify-center mt-4  mb-8">

            <button onclick="document.getElementById('email').value='admin@example.com';document.getElementById('password').value='secret'; document.getElementById('loginform').submit()" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150" />
                {{ __('Log in as admin') }}
            </button>
            @if (config('settings.enable_login_as_company',true))
                <button onclick="document.getElementById('email').value='owner@example.com';document.getElementById('password').value='secret'; document.getElementById('loginform').submit()" class="ml-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150" />
                    {{ __('Log in as company') }}
                </button>
            @endif
            @if (config('settings.enable_login_as_client',false))
                <button onclick="document.getElementById('email').value='client@example.com';document.getElementById('password').value='secret'; document.getElementById('loginform').submit()" class="ml-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150" />
                    {{ __('Log in as client') }}
                </button>
            @endif
            
        </div>
        @endif

        @if (config('settings.enable_login_as_company',true))
            @include('auth.social')
        @endif

       

        <form method="POST" action="{{ route('login') }}" id="loginform">
            @csrf

            <div>
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-button class="ml-4">
                    {{ __('Log in') }}
                </x-button>
            </div>
        </form>    
      
        
        
        <x-slot name="links">
            @if (Route::has('register') && !config('app.disable_register',false))
                <a style="opacity: 0.5; text-center" class="text-center items-center justify-center   text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('register') }}">
                    {{ __('If you don\'t have account you can create new one') }} <span  class="underline">{{__('here')}}</span>.
                </a>
            @endif

             <!-- IF disable register, show a message -->
            @if (config('app.disable_register',false))
                <div class="mb-4 font-medium text-sm text-gray-600">
                    {{ __('Register is disabled. Maximum number of accounts reached.') }}
                </div>
            @endif
        </x-slot>



       
    </x-authentication-card>
    
    
</x-guest-layout>

