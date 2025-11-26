<div class="card shadow max-height-vh-70 overflow-auto overflow-x-hidden">
    <div class="card-header shadow-lg">
        <b>{{ __('Whatsapp Cloud API - Connection Status') }}</b>
    </div>
    <div class="card-body overflow-auto overflow-x-hidden scrollable-div"  >
        
        @if ($company->getConfig('whatsapp_webhook_verified','no')!='yes' || $company->getConfig('whatsapp_settings_done','no')!='yes')
            <div class="alert alert-danger" role="alert">
                <strong>{{ __('Not connected!') }}</strong> {{ __('Please complete all the steps in order to connect to Whatsapp Cloud API')}}
            </div>
            <button onclick="location.reload()" class="btn btn-outline-success" type="button">{{ __('Refresh status')}}</button>

        @else  
            <div class="alert alert-success" role="alert">
                <strong>{{ __('Success!')}}</strong> {{ __('You are now connected to Whatsapp Cloud API. You can start use the system') }}
            </div> 
                
        @endif
        
        
        
        
    </div>
</div>