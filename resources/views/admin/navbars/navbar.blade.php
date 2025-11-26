@auth()
   
    @if(!isset($hideActions))
        @include('admin.navbars.navs.auth')
    @endif
        

@endauth

@guest()
    @if(\Request::route()->getName() != "order.success")
        @include('admin.navbars.navs.guest')
    @endif
@endguest
