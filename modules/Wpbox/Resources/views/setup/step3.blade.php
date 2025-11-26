<div class="card shadow max-height-vh-70 overflow-auto overflow-x-hidden mt-4">
    <div class="card-header shadow-lg">
        <b>{{ __('Step 3: Get you Account ID and Phone number ID') }}</b>
    </div>
    <div class="card-body overflow-auto overflow-x-hidden scrollable-div" ref="scrollableDiv" >
        <p>
       1. {{__('In the facebook app, in Whatasppa -> API setup, you will find your Phone number ID and your WhatsApp Business Account ID.') }} 
       <br />
       2. {{__('Copy them and enter it here') }} 
       @include('partials.input',['value'=>$company->getConfig('whatsapp_phone_number_id',''),'class'=>'col-md-6','required'=>false,'id'=>'phone','name'=>"Phone number ID",'placeholder'=>"Phone number ID"])
       @include('partials.input',['value'=>$company->getConfig('whatsapp_business_account_id',''),'class'=>'col-md-6','required'=>false,'id'=>'account','name'=>"WhatsApp Business Account ID",'placeholder'=>"WhatsApp Business Account ID"])
        
          <button class="btn btn-secondary" type="submit">{{ __('Submit')}}</button>
       <br />
    <p>
    </div>
</div>