<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        @yield('title')
        <title>{{ config('app.name', 'Site') }}</title>

         <!-- Fonts -->
         <link rel="preconnect" href="https://fonts.bunny.net">
         <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />


        
        <!-- Icons -->
        @if(config('settings.icon_type','nucleo') == 'hero')   
            <link href="{{ asset('vendor/argon') }}/vendor/hero/css/hero.css" rel="stylesheet">
        @else
            <link href="{{ asset('vendor/argon') }}/vendor/nucleo/css/nucleo.css" rel="stylesheet">
        @endif
        
        
        <link type="text/css" href="{{ asset('vendor/argon') }}/css/argon.css?v=1.0.0" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('vendor') }}/jasny/css/jasny-bootstrap.min.css">
   

        @yield('head')

        @include('layouts.rtl')

        <!-- Custom CSS defined by admin -->
        <link type="text/css" href="{{ asset('byadmin') }}/back.css" rel="stylesheet">

        <!-- Select2  -->
        <link type="text/css" href="{{ asset('vendor') }}/select2/select2.min.css" rel="stylesheet">

        <!-- Custom CSS defined by user -->
        <link type="text/css" href="{{ asset('custom') }}/css/custom.css?id={{ config('version.version')}}" rel="stylesheet">

        <!-- Flags -->
        <link type="text/css" href="{{ asset('vendor') }}/flag-icons/css/flag-icons.min.css" rel="stylesheet" />

        <!-- Bootstap VUE -->
        <link type="text/css" href="{{ asset('vendor') }}/vue/bootstrap-vue.css" rel="stylesheet" />

        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">


    </head>
    <body class="{{ $class ?? '' }}">
        @auth()
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            @include('admin.navbars.sidebar')
        @endauth

        <div class="main-content">
            @include('admin.navbars.navbar')
            @yield('content')
        </div>

        @guest()
           
        @endguest

            <!-- Commented because navtabs includes same script -->
           

            @yield('topjs')
    
            <script>
                var t="<?php echo 'translations'.App::getLocale() ?>";
               window.translations = {!! Cache::get('translations'.App::getLocale(),"[]") !!};
               
               
            </script>
            
            <!-- Navtabs -->
            <script src="{{ asset('vendor') }}/jquery/jquery.min.js" type="text/javascript"></script>
            <script src="{{ asset('vendor/argon') }}/js/popper.min.js" type="text/javascript"></script>
            

            <script src="{{ asset('vendor/argon') }}/vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    
            <!-- Nouslider -->
            <script src="{{ asset('vendor/argon') }}/vendor/nouislider/distribute/nouislider.min.js" type="text/javascript"></script>
    
            <!-- Latest compiled and minified JavaScript -->
            <script src="{{ asset('vendor') }}/jasny/js/jasny-bootstrap.min.js"></script>
    
   
            <!-- All in one -->
            <script src="{{ asset('custom') }}/js/js.js?id={{ config('version.version')}}"></script>

            <!-- Notify JS -->
            <script src="{{ asset('custom') }}/js/notify.min.js"></script>
    
            <!-- Argon JS -->
            <script src="{{ asset('vendor/argon') }}/js/argon.js?v=1.0.0"></script>

    
    
            <script>
                var ONESIGNAL_APP_ID = "{{ config('settings.onesignal_app_id') }}";
                var USER_ID = '{{  auth()->user()&&auth()->user()?auth()->user()->id:"" }}';
                var PUSHER_APP_KEY = "{{ config('broadcasting.connections.pusher.key') }}";
               
                var PUSHER_APP_CLUSTER = "{{ config('broadcasting.connections.pusher.options.cluster') }}";
            </script>
            @if (auth()->user()!=null&&auth()->user()->hasRole('staff'))
                <script>
                    //When staff, use the owner
                    USER_ID = '{{ auth()->user()->company->user_id }}';
                </script>
            @endif
           
    
            <!-- OneSignal -->
            @if(strlen( config('settings.onesignal_app_id'))>4)
                <script src="{{ asset('vendor') }}/OneSignalSDK/OneSignalSDK.js" async=""></script>
                <script src="{{ asset('custom') }}/js/onesignal.js"></script>
            @endif
    
            @stack('js')
            @yield('js')
    

    
             <!-- Pusher -->
             @if(strlen(config('broadcasting.connections.pusher.app_id'))>2)
                <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
                @if (config('settings.app_code_name','')=="qrpdf")
                    <script src="{{ asset('custom') }}/js/pusher.js"></script>    
                @endif
            @endif

            <!-- Import Select2 --->
            <script src="{{ asset('vendor') }}/select2/select2.min.js"></script>
    
            <!-- Custom JS defined by admin -->
            <script src="{{ asset('byadmin') }}/back.js"></script>

            <!-- Import Moment -->
            <script type="text/javascript" src="{{ asset('vendor') }}/moment/moment.min.js"></script>
            <script type="text/javascript" src="{{ asset('vendor') }}/moment/momenttz.min.js"></script>
            <script src="{{ asset('vendor/argon') }}/js/bootstrap.min.js" type="text/javascript"></script>
            
            <!-- Import Vue -->
            <script src="{{ asset('vendor') }}/vue/vue.js"></script>
            <script src="{{ asset('vendor') }}/vue/bootstrap-vue.min.js"></script> 
            
           
             
            <!-- Import AXIOS --->
            <script src="{{ asset('vendor') }}/axios/axios.min.js"></script>

            <?php 
                echo file_get_contents(base_path('public/byadmin/back.js')) 
            ?>
    </body>
</html>
