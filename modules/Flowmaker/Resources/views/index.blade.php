@extends('layouts.app', ['title' => __('Flowmaker not installed')])
@section('content')
<div class="header pb-8 pt-2 pt-md-7">
    <div class="container-fluid">
    </div>
</div>
<div class="container-fluid mt--8">  
    <div class="row">
        <div class="col-12">
            @include('partials.flash')
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6 text-center">
            <script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.8.5/dist/dotlottie-wc.js" type="module"></script>
            <dotlottie-wc src="https://lottie.host/807f19a5-225e-47b5-a2d4-098bd37d93cc/oJdJr5HP47.lottie" style="width: 300px;height: 300px; margin-left: 150px" autoplay loop></dotlottie-wc>
            <h2 class="mb-1">{{ __('Upgrade to use AI Flow Builder') }}</h2>
            <p class="lead mb-0" style="font-size: 1rem;">
                {{ __('Upgrade your plan to get access to our Flow Builder, AI assistants, copilot and more. Please reach out to your administrator for the upgrade.') }}
            </p>
        </div>
    </div>
</div>
@endsection