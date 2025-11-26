@extends('layouts.app', ['title' => __('Embedded SignUp Whatsapp Setup')])
@section('content')
<div class="header pb-8 pt-2 pt-md-7">
    <div class="container-fluid">
        <div class="header-body">
            <h1 class="mb-3 mt--3">💬 {{__('Embedded SignUp Whatsapp Setup')}}</h1>
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
        <form >
    
            <div class="row">
            <div class="col-lg-12">
                <div class="card shadow max-height-vh-70 overflow-auto overflow-x-hidden">
                    <div class="card-header shadow-lg">
                        <b>{{ __('Step 1: Create developer account and a new Facebook app') }}</b>
                    </div>
                    <div class="card-body overflow-auto overflow-x-hidden scrollable-div" ref="scrollableDiv" >
                    
                    
                      First, set up the Webhooks product for your app:
                      <br /><br />

                      1. Load your app in the <a target="_blank" href="https://developers.facebook.com/apps">App Dashboard</a> and add the Webhooks product if you have not already added it.
                        <br /><br />
                        2. Click the Webhooks product in the menu on the left.
                        <br /><br />
                        3. Select WhatsApp Business Account from the drop-down menu and then click Subscribe to this object.
                        <br /><br />
                        4. Add your Webhooks callback URL and verification token, verify, and save your changes.
                        <br /><br />

                  
                      
                      <b style="font-weight:bold">{{ (__('Callback URL'))}}</b><br />
                      <code style="color:green; font-weight:bold">
                        {{ rtrim(config('app.url'),'/')}}/webhook/wpbox/receive/{{$token}}
                      </code>
                      <br /><br />
                      <b style="font-weight:bold">{{ (__('Verify token'))}}</b><br />
                      <code style="color:green; font-weight:bold">
                        {{$token}}
                      </code>
                
                      <br /><br />
                      5. {{ __('Click on Webhook fields -> Manage and select the Messages') }}
                ​
                        </p>
                    </div>
                </div>
            </div>
            </div>

            
               

        </form>
    </div>
</div>
@endsection
