<ul class="navbar-nav">
    <li class="nav-item">
        <a class="nav-link" href="{{ route('home') }}">
            <i class="ni ni-tv-2 text-primary"></i> {{ __('Dashboard') }}
        </a>
    </li>
    @foreach (auth()->user()->getExtraMenus() as $menu)
    @if ($menu['route']!="staff.index")
        <li class="nav-item">
            <a class="nav-link" href="{{ route($menu['route'],isset($menu['params'])?$menu['params']:[]) }}">
                <i class="{{ $menu['icon'] }}"></i> {{ __($menu['name']) }}
            </a>
        </li>
    @endif
            
    @endforeach

</ul>
