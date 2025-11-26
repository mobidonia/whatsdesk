<!-- Exrta menus -->
@foreach (auth()->user()->getExtraMenus() as $menu)
@if (isset($menu['isGroup']) && $menu['isGroup'])

    <a class="nav-link " href="#navbar-{{  $menu['id'] }}" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="navbar-{{  $menu['id'] }}">
        <i class="{{ $menu['icon'] }}"></i>
        <span class="nav-link-text">{{ __($menu['name']) }}</span>
    </a>
    <div class="collapse @if (Route::currentRouteName() == $menu['route'] || collect($menu['menus'])->pluck('route')->contains(Route::currentRouteName())) show @endif"" id="navbar-{{  $menu['id'] }}" style="">
        <ul class="nav nav-sm flex-column">
            @foreach ($menu['menus'] as $submenu)
                <li class="nav-item">
                    <a class="nav-link @if (Route::currentRouteName() == $submenu['route']) active @endif" href="{{ route($submenu['route'],isset($submenu['params'])?$submenu['params']:[]) }}">
                        <i class="{{ $submenu['icon'] }}"></i> {{ __($submenu['name']) }}
                    </a>
                </li> 
            @endforeach
        </ul>
    </div>
@else
    <li class="nav-item">
        <a  class="nav-link @if (Route::currentRouteName() == $menu['route']) active @endif" href="{{ route($menu['route'],isset($menu['params'])?$menu['params']:[]) }}"   >
            @if (isset($menu['svg']))
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="{{ $menu['color'] }}" viewBox="0 0 16 16">
                    <path d="{{ $menu['svg'] }}"/>
                </svg>
                <span style="margin-left: 20px">{{ __($menu['name']) }}  </span>
            @else
                <i class="{{ $menu['icon'] }}"></i>{{ __($menu['name']) }}  
            @endif  
             
            
        </a>
    </li> 
@endif    
@endforeach