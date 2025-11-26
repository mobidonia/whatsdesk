<div class="card shadow max-height-vh-70 overflow-auto overflow-x-hidden">
    <div class="card-header shadow-lg">
        <b>{{ __('Step 1: Create developer account and a new Facebook app') }}</b>
    </div>
    <div class="card-body overflow-auto overflow-x-hidden scrollable-div" ref="scrollableDiv" >
      @if ($company->getConfig('whatsapp_webhook_verified','no')=='yes')
        <div class="alert alert-success fade show" role="alert">
          {{__('Congratulation. Webhook is succesfullly verified.')}}
        </div>
      @endif

        <p>
       1. {{__('Create a Developer account and a new Facebook app as described here') }} 
           <a target="_blank" href="https://developers.facebook.com/docs/whatsapp/cloud-api/get-started#set-up-developer-assets"><img style="height: 16px" src="{{ asset('default/wpbox/open.png')}}" /></a>
       <br />
      2. {{ __('Once you have your Facebook app created, in the dasboard of the app, locate the Whatsapp product -> Setup.')}}
      <br />
      3. {{ __('Then go to WhatsApp > Configuration and enter the following info')}}
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
      4. {{ __('Click on Webhook fields -> Manage and select the Messages') }}
​
        </p>
    </div>
</div>