@extends('layouts.app', ['title' => __('Pages')])

@section('content')
    <div class="header  pb-8 pt-5 pt-md-8">
    </div>

    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Extended license required') }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        @include('partials.flash')
                    </div>
                    
                    <div class="card-footer py-4">
                            <p>
                                Thank you for using our script.<br /> 
                                We are pleased to inform you that subscribtions (users paying you)  is available for use under an extended license. <br /> 
                                This license allows for a wider range of usage, including the ability to use the tool in a commercial setting (Charging your customers).<br />  
                                We understand that this may be an added expense, but we assure you that it is well worth it for the added benefits it provides, since with it you also get the plugins in the Apps Section.<br />  
                                <br />
                                Please let us know if you have any questions or would like to proceed with obtaining an extended license. <br /> 

                                If you already have an extended license, please <a target="blank" href="https://help.mobidonia.com/"><strong>send us a ticket</strong></a> to get the extended license key. <br />
                                Thank you for choosing our script.
                            </p>
                    </div>
                    <div class="card-footer py-4">
                        <p class="text-muted">
                            You can hide the Pricing Plans menu for both admin and users by disabling "Pricing plans" in Settings.
                        </p>
                </div>
                </div>
            </div>
        </div>

        @include('layouts.footers.auth')
    </div>
@endsection
