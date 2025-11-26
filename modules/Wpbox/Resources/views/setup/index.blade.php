@extends('layouts.app', ['title' => __('Whatsapp Setup')])
@section('content')
<div class="header pb-8 pt-2 pt-md-7">
    <div class="container-fluid">
        <div class="header-body">
            <h1 class="mb-3 mt--3">💬 {{__('WhatsApp Cloud API Setup')}}</h1>
            <div class="row align-items-center pt-2">
            </div>
        </div>
    </div>
</div>
<div class="container-fluid mt--8">  
    <div class="row">
        <div class="col-12">
            @include('partials.flash')
        </div>
        <form method="POST" action="{{ route('whatsapp.store') }}">
            @csrf
            <div class="row">
            <div class="col-lg-8 col-md-7">
                @include('wpbox::setup.step1')
                @include('wpbox::setup.step2')
                @include('wpbox::setup.step3')
                <div class="card mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>📞 WhatsApp Calling</span>
                        <a href="{{ route('whatsappcall.settings') }}" class="btn btn-sm btn-primary">{{ __('Open calling settings') }}</a>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">Enable Meta WhatsApp Calling and configure Infobip WebRTC. Make sure to subscribe your Facebook App to the <code>calls</code> webhook and enable calling on your business number.</p>
                        <ul class="mb-0">
                            <li>Enable/disable calling, inbound control, business hours</li>
                            <li>Set Infobip base URL and API key</li>
                            <li>BIC: request permission and send Infobip Call Link</li>
                            <li>UIC: accept inbound calls via webhooks</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-5">
                @include('wpbox::setup.verified')
            </div>
            </div>

            
               

        </form>
    </div>
</div>
@endsection
