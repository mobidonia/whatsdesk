@extends('layouts.app', ['title' => __('Whatsapp API')])
@section('content')
<div class="header pb-8 pt-2 pt-md-7">
    <div class="container-fluid">
        <div class="header-body">
            <h1 class="mb-3 mt--3">🔗 {{__('API Info')}}</h1>
            <div class="row align-items-center pt-2">
            </div>
        </div>
    </div>
</div>
<div class="container-fluid mt--8">  
    <div class="row">
        <div class="col-12">
            @include('partials.flash')

            <div class="card shadow max-height-vh-70 overflow-auto overflow-x-hidden">
                <div class="card-header shadow-lg">
                    <b>{{ __('API endpoint') }}</b>
                </div>
                <div class="card-body overflow-auto overflow-x-hidden scrollable-div" ref="scrollableDiv" >
                    {{config('app.url')}}
                </div>
            </div>  
            <br />
            <div class="card shadow max-height-vh-70 overflow-auto overflow-x-hidden">
                <div class="card-header shadow-lg">
                    <b>{{ __('You API token') }}</b>
                </div>
                <div class="card-body overflow-auto overflow-x-hidden scrollable-div" ref="scrollableDiv" >
                    {{$token}}
                </div>
            </div>  


            

            <br />
            <div class="card shadow max-height-vh-70 overflow-auto overflow-x-hidden">
                <div class="card-footer">
                    <a href="{{ route('wpbox.api.index') }}" class="btn btn-success">🔗 {{ __('List of Camapaings for API') }}</a>
                    <a href="{{ config('wpbox.api_docs','https://documenter.getpostman.com/view/8538142/2s9Ykn8gvj') }}" target="_blank" class="btn btn-primary">🔗 {{ __('Documentation') }}</a>
                </div>
            </div>
        </div>  
    </div>
</div>
@endsection
