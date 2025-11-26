<x-guest-layout>
    <x-authentication-card>
        <link rel="stylesheet" href="{{ asset('vendor/IntlTelInput/intlTelInput.css')}}">  
        <script src="{{ asset('vendor/IntlTelInput/intlTelInput.min.js')}}"></script>

        @if (request('company_id')&&request('company_name')&&request('company_image'))
            <x-slot name="logo">
                <a href="javascript:history.back()">
                     <img src="{{ request('company_image') }}" alt="{{ request('company_name') }}" class="rounded-full h-20">
                </a>
            </x-slot>
        @else
            <x-slot name="logo">
                <x-authentication-card-logo />
            </x-slot>
        @endif

       

        <x-validation-errors class="mb-4" />

        @include('auth.social')

        <form method="POST" action="{{ route('register') }}">
            @csrf
            @if (request('company_id')&&request('company_name')&&request('company_image'))
                <input type="hidden" name="company_id" value="{{ request('company_id') }}">
                <input type="hidden" name="company_name" value="{{ request('company_name') }}">
            @endif
            <div>
                <x-label for="name" value="{{ __('Name') }}" />
                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', request('email'))" required autocomplete="username" />
            </div>

            <!-- Phone -->
            <div class="mt-4">
                <x-label for="phone" value="{{ __('Phone') }}" />
                <x-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" required autocomplete="phone" />
            </div>

            <!-- Hidden Country Code -->
            <input type="hidden"  id="country_code" name="country_code" value="" />

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />

                            <div class="ml-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            @if (config('settings.privacy_policy') && config('settings.terms_of_service'))
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />
                            <div class="ml-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('static-page', config('settings.terms_of_service')).'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('static-page', config('settings.privacy_policy')).'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-button class="ml-4">
                    {{ __('Register') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
    <script>
        const input = document.querySelector("#phone");
        const country_code = document.querySelector("#country_code");
        const iti = window.intlTelInput(input, {
            initialCountry: "auto",
            separateDialCode: true, 
            geoIpLookup: callback => {
                fetch("https://ipapi.co/json")
                .then(res => res.json())
                .then(data => callback(data.country_code))
                .catch(() => callback("us"));
            },
            utilsScript: "{{ asset('vendor/IntlTelInput/utils.js')}}",
        });
        input.addEventListener("countrychange", function() {
            // do something with iti.getSelectedCountryData()
            country_code.value = iti.getSelectedCountryData().dialCode;
        });
        </script>
</x-guest-layout>
