<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="font-sans text-gray-900 antialiased">
            <section class="section">
                <div class="container mx-10">
                    <br /><br />
                    <div class="alert alert-danger" role="alert">
                        Install is ok. But looks like you are running the site under subdomain. 
                    </div>
                    <p>
                        When you run the site in subdomain, you need to declare that subdomain (just the subdomain) in Site setting->Setup->Subdomains and add your domain there ex www,app <br />
                    <br />
                    <a href="{{ route('login') }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Login to admin area</a>
                </p>
                </div>
               </section>
        </div>
    </body>
</html>
