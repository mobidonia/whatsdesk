<div class="card shadow max-height-vh-70 overflow-auto overflow-x-hidden mt-4">
    <div class="card-header shadow-lg">
        <b>{{ __('Step 2: Get you permanent access token') }}</b>
    </div>
    <div class="card-body overflow-auto overflow-x-hidden scrollable-div" ref="scrollableDiv" >
        <p>
       1. {{__('The process of creating a permanent access token is explained in deteails in the Facebook Docs') }} 
           <a target="_blank" href="https://developers.facebook.com/docs/whatsapp/business-management-api/get-started#1--acquire-an-access-token-using-a-system-user-or-facebook-login"><img style="height: 16px" src="{{ asset('default/wpbox/open.png')}}" /></a>
       <br />
       2. {{__('Once you have the permanent access token, enter it here') }} 
       @include('partials.input',['value'=>$company->getConfig('whatsapp_permanent_access_token',''),'class'=>'col-md-6','required'=>false,'id'=>'token','name'=>"Permanent access token",'placeholder'=>"Permanent access token"])
       
          <button class="btn btn-secondary" type="submit">{{ __('Save access token')}}</button>
       <br />
    <p>
    </div>
</div>