@if (in_array(config('app.locale'),['ar','he','fa','ur']))
<nav class="navbar navbar-vertical fixed-right navbar-expand-md navbar-light bg-white" id="sidenav-main">
    @else
    <nav class="navbar navbar-vertical fixed-left navbar-expand-md navbar-light bg-white" id="sidenav-main">
        @endif

        <div class="container-fluid">
            <!-- Toggler -->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#sidenav-collapse-main"
                aria-controls="sidenav-main" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Brand -->
            <a class="navbar-brand pt-0" href="/">
                <img src="{{ config('settings.logo') }}" class="navbar-brand-img" alt="...">
            </a>
            <!-- Mobile User Profile (simplified) -->
            <div class="d-md-none">
                <div class="media align-items-center">
                    <span class="avatar avatar-sm rounded-circle">
                        <img alt="..." src="{{'https://www.gravatar.com/avatar/'.md5(auth()->user()->email) }}">
                    </span>
                    <div class="media-body ml-2">
                        <span class="text-sm font-weight-bold">{{ auth()->user()->name }}</span>
                    </div>
                </div>
            </div>
            <!-- Collapse -->
            <div class="collapse navbar-collapse" id="sidenav-collapse-main">
                <!-- Collapse header -->
                <div class="navbar-collapse-header d-md-none">
                    <div class="row">
                        <div class="col-6 collapse-brand">
                            <a href="{{ route('dashboard') }}">
                                <img src="{{ config('global.site_logo') }}">
                            </a>
                        </div>
                        <div class="col-6 collapse-close">
                            <button type="button" class="navbar-toggler" data-toggle="collapse"
                                data-target="#sidenav-collapse-main" aria-controls="sidenav-main" aria-expanded="false"
                                aria-label="Toggle sidenav">
                                <span></span>
                                <span></span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Organization switcher -->
                @if(config('settings.enable_multi_organizations',true) && auth()->user()->hasRole('owner'))
                    <div class="px-3 mb-4">
                        <h6 class="text-uppercase text-muted text-xs font-weight-bolder mb-2">
                            {{ __('Organization') }}
                        </h6>
                        <div class="dropdown w-100">
                            <button class="btn btn-outline-primary dropdown-toggle w-100 text-left d-flex align-items-center" type="button" id="orgDropdown"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="ni ni-building text-primary mr-2"></i>
                                <span class="flex-grow-1 text-left">{{ auth()->user()->currentCompany()->name }}</span>
                                <i class="ni ni-bold-down ml-2"></i>
                            </button>
                            <div class="dropdown-menu w-100 shadow-sm" aria-labelledby="orgDropdown">
                                @foreach(auth()->user()->companies->where('active', 1) as $company)
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.companies.switch', $company->id) }}">
                                    <i class="ni ni-building text-primary mr-2"></i>
                                    <span>{{ $company->name }}</span>
                                </a>
                                @endforeach
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('admin.organizations.manage') }}" class="dropdown-item d-flex align-items-center">
                                    <i class="ni ni-settings text-primary mr-2"></i>
                                    <span>{{ __('Organizations') }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
                <!-- Navigation -->
                @if(Auth::user()->isImpersonating())
                <hr class="my-3">
                <ul class="navbar-nav ">
                    <li class="nav-item">
                        <a class="nav-link active active-pro" href="{{ route('admin.companies.stopImpersonate') }}">
                            <i class="ni ni-button-power text-red"></i>
                            <span class="nav-link-text">{{ __('Back to your account')}}</span>
                        </a>
                    </li>
                </ul>
                <hr class="my-3">
                @endif
                @if(auth()->user()->hasRole('admin'))
                @include('admin.navbars.menus.admin')
                @else
                <span></span>
                @endif



                @if(auth()->user()->hasRole('owner'))
                @include('admin.navbars.menus.owner')
                @else
                <span></span>
                @endif

                @if(auth()->user()->hasRole('staff'))
                @include('admin.navbars.menus.staff')
                @else
                <span></span>
                @endif

                @if(auth()->user()->hasRole('client'))
                @include('admin.navbars.menus.client')
                @else
                <span></span>
                @endif



                <!-- Bottom Profile Section -->
                <div class="mt-auto">
                    <!-- User Profile Card -->
                    <div class="">
                        <div class="dropdown">
                            <button class="hover:bg-white btn btn-link rounded-full text-left p-1 w-100 d-flex align-items-center" type="button" id="userProfileDropdown"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <div class="avatar avatar-sm mr-3">
                                    <img alt="..." src="{{'https://www.gravatar.com/avatar/'.md5(auth()->user()->email) }}">
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 text-sm font-weight-bold">{{ auth()->user()->name }}</h6>
                                    <p class="mb-0 text-xs text-muted">{{ auth()->user()->email }}</p>
                                </div>
                                <i class="ni ni-bold-up ml-2"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-up w-100 shadow-sm" aria-labelledby="userProfileDropdown">
                                <div class="dropdown-header">
                                    <h6 class="text-overflow m-0">{{ __('Welcome!') }}</h6>
                                </div>
                                <a href="{{ route('profile.show') }}" class="dropdown-item d-flex align-items-center">
                                    <i class="ni ni-single-02 text-primary mr-2"></i>
                                    <span>{{ __('My profile') }}</span>
                                </a>
                                @if ((config('settings.app_code_name','')=="wpbox" || config('settings.app_code_name','')=="whatssupport")&&auth()->user()->hasRole('owner'))
                                    @if (\Illuminate\Support\Facades\Route::has('whatsapp.setup'))
                                    <a href="{{ route('whatsapp.setup') }}" class="dropdown-item d-flex align-items-center">
                                            <i class="ni ni-world text-success mr-2"></i>
                                            <span>{{ __('Whatsapp API') }}</span>
                                        </a>
                                    @endif
                                @endif

                                @if ( (config('settings.app_code_name','')=="whatssupport" || config('settings.app_code_name','')=="wpbox") && auth()->user()->hasRole('owner'))
                                    @if (\Illuminate\Support\Facades\Route::has('whatsappcall.settings'))
                                    <a href="{{ route('whatsappcall.settings') }}" class="dropdown-item d-flex align-items-center">
                                            <i class="ni ni-world text-success mr-2"></i>
                                            <span>{{ __('Whatsapp Calls') }}</span>
                                        </a>
                                    @endif
                                @endif
                                
                                <!-- Management Menu Items -->
                                @if(auth()->user()->hasRole('owner'))
                                    <div class="dropdown-divider"></div>
                                    <h6 class="dropdown-header text-xs text-muted">{{ __('Management') }}</h6>
                                    
                                    @if (!config('settings.hide_company_profile',false))
                                        <a href="{{ route('admin.companies.edit', auth()->user()->currentCompany()->id) }}" 
                                           class="dropdown-item d-flex align-items-center @if (Route::currentRouteName() == 'admin.companies.edit') active @endif">
                                            <i class="ni ni-shop text-primary mr-2"></i>
                                            <span>{{ __('Company') }}</span>
                                        </a>
                                    @endif
                                    
                                    @if (!config('settings.hide_company_apps',false))
                                        <a href="{{ route('admin.apps.company') }}" 
                                           class="dropdown-item d-flex align-items-center @if (Route::currentRouteName() == 'admin.apps.company') active @endif">
                                            <i class="ni ni-spaceship text-red mr-2"></i>
                                            <span>{{ __('Apps') }}</span>
                                        </a>
                                    @endif
                                    
                                    @if(config('settings.enable_pricing'))
                                        <a href="{{ route('plans.current') }}" class="dropdown-item d-flex align-items-center">
                                            <i class="ni ni-credit-card text-orange mr-2"></i>
                                            <span>{{ __('Plan') }}</span>
                                        </a>
                                    @endif
                                    
                                    @if (!config('settings.hide_share_link',false))
                                        <a href="{{ route('admin.share') }}" class="dropdown-item d-flex align-items-center">
                                            <i class="ni ni-send text-green mr-2"></i>
                                            <span>{{ __('Share') }}</span>
                                        </a>
                                    @endif
                                @endif
                                
                                <!-- Home/Store Link -->
                                @if((auth()->user()->hasRole('owner')||auth()->user()->hasRole('staff')))
                                    @if (auth()->user()->hasRole('owner'))
                                        <?php $urlToVendor=auth()->user()->companies()->get()->first()->getLinkAttribute(); ?>
                                    @endif  
                                    @if (auth()->user()->hasRole('staff'))
                                        <?php $urlToVendor=auth()->user()->company->getLinkAttribute(); ?>
                                    @endif
                                    @if (config('settings.show_company_page',true))
                                        <a href="{{ $urlToVendor }}" target="_blank" class="dropdown-item d-flex align-items-center">
                                            <i class="ni ni-world text-info mr-2"></i>
                                            <span>{{ __('Portal')}}</span>
                                        </a>
                                    @endif
                                @endif
                                
                                <!-- Language Switcher -->
                                <?php
                                    $availableLanguagesENV = config('settings.front_languages');
                                    $exploded = explode(',', $availableLanguagesENV);
                                    $availableLanguages = [];
                                    for ($i = 0; $i < count($exploded); $i += 2) {
                                        $availableLanguages[$exploded[$i]] = $exploded[$i + 1];
                                    }
                                    $locale =isset($locale)?$locale:(Cookie::get('lang') ? Cookie::get('lang') : config('settings.app_locale'));
                                ?>
                                @if(isset($availableLanguages)&&count($availableLanguages)>1&&isset($locale))
                                    <div class="dropdown-divider"></div>
                                    <h6 class="dropdown-header text-xs text-muted">{{ __('Language') }}</h6>
                                    @foreach ($availableLanguages as $short => $lang)
                                        <a href="{{ route('home',$short)}}" class="dropdown-item d-flex align-items-center">
                                            
                                            <span>{{ __($lang) }}</span>
                                            @if(strtolower($short) == strtolower($locale))
                                                <i class="ni ni-check-bold text-success ml-auto"></i>
                                            @endif
                                        </a>
                                    @endforeach
                                @endif
                                
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('logout') }}" class="dropdown-item d-flex align-items-center" 
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="ni ni-user-run text-danger mr-2"></i>
                                    <span>{{ __('Logout') }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Compact Version Info - Only for Admins -->
                    @if(auth()->user()->hasRole('admin'))
                    <div class="px-3 pb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">{{ __('Version')}} {{ config('version.version')}} 
                                <span id="uptodate" class="badge badge-success" style="display:none;">{{ __('latest') }}</span>
                            </small>
                            <small class="text-muted">{{ \Carbon\Carbon::now()->format('m/d H:i') }}</small>
                        </div>
                        
                        <!-- Update Notifications (hidden by default) -->
                        <div id="update_notification" style="display:none;" class="alert alert-info mt-2 mb-0">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div id="uptodate_notification" style="display:none;" class="alert alert-success mt-2 mb-0">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                    @endif
                </div>

            </div>
        </div>
    </nav>
