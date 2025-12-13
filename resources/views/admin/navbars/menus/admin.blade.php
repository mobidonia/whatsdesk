<ul class="navbar-nav">
    <li class="nav-item">
        <a class="nav-link @if (Route::currentRouteName() == 'dashboard') active @endif"
            href="{{ route('dashboard') }}">
            <i class="ni ni-tv-2 text-primary"></i> {{ __('Dashboard') }}
        </a>
    </li>
    @if (config('settings.admin_companies_enabled',true))
        <li class="nav-item">
            <a class="nav-link @if (Route::currentRouteName() == 'admin.companies.index') active @endif"
                href="{{ route('admin.companies.index') }}">
                <i class="ni ni-shop text-info"></i> {{ __('Companies') }}
            </a>
        </li> 
    @endif

    @include('admin.navbars.menus.extra')
   
    @if (!config('settings.disable_landing_page',false))
        <li class="nav-item">
            <a class="nav-link @if (Route::currentRouteName() == 'admin.landing') active @endif" href="{{ route('admin.landing') }}">
                <i class="ni ni-html5 text-green"></i> {{ __('Landing Page') }}
            </a>
        </li>
    @endif
    @if (config('settings.pricing_enabled',true))
        <li class="nav-item ">
            <a class="nav-link @if (Route::currentRouteName() == 'plans.index') active @endif"
                href="{{ route('plans.index')}}">
                <i class="ni ni-credit-card text-orange"></i> {{ __('Pricing plans') }}
            </a>
        </li> 
    @endif

    <!-- if enable credits -->
    @if (config('settings.enable_credits'))
        <li class="nav-item">
            <a class="nav-link @if (Route::currentRouteName() == 'credits.index') active @endif" href="{{ route('credits.index') }}">
                <i class="ni ni-credit-card text-blue"></i> {{ __('Credits') }}
            </a>
        </li>
    @endif


    
    <li class="nav-item ">
        <a class="nav-link " target="_blank"
            href="{{ url('/tools/languages')."/".strtolower(config('settings.app_locale','en'))."/translations" }}">
            <i class="ni ni-world text-orange"></i> {{ __('Translations') }}
        </a>
    </li>
   
    <!--<li class="nav-item ">
        <a class="nav-link " target="_blank"
            href="{{ url('/translations') }}">
            <i class="ni ni-world text-orange"></i> {{ __('Translations') }}
        </a>
    </li>-->

    @if(!config('settings.hideApps',false))
        <li class="nav-item">
            <a class="nav-link @if (Route::currentRouteName() == 'admin.apps.index') active @endif " href="{{ route('admin.apps.index') }}">
                <i class="ni ni-spaceship text-red"></i> {{ __('Apps') }}
            </a>
        </li>
    @endif

    <li class="nav-item">
        <a class="nav-link " href="{{ url('/backup') }}">
            <i class="ni ni-time-alarm text-orange"></i> {{ __('Backup') }}
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link " target="_blank"
            href="{{ url('/log-viewer') }}">
            <i class="ni ni-single-copy-04 text-orange"></i> {{ __('Log Viewer') }}
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link @if (Route::currentRouteName() == 'admin.settings.index') active @endif"
            href="{{ route('admin.settings.index')}}">
            <i class="ni ni-settings text-black"></i> {{ __('Site Settings') }}
        </a>
    </li>





</ul>
