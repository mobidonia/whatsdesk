 <!-- CSS Files -->
 @if (in_array(config('app.locale'),['ar','he','fa','ur']))
    <link href="{{ asset('css') }}/rtl.css" rel="stylesheet" />
 @endif
