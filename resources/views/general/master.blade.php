@extends('layouts.app', ['title' => __($title)])
@section('js')
    @yield('js')
@endsection

@section('content')

<div class="header  pb-8 pt-5 pt-md-8 mt--4">
 </div>
    
    <div class="container-fluid mt--9">

        <div class="col-12">
            @include('partials.flash')
        </div>

        <div class="row">
            <div class="col-12">
                @yield('thecontent')
            </div>
           
        </div>
    </div>
@endsection
