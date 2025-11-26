@include('partials.input',['name'=>'Name','id'=>"name",'placeholder'=>"Plan name",'required'=>true,'value'=>(isset($plan)?$plan->name:null)])
<div class="row">
    <div class="col-md-12">
        @include('partials.input',['name'=>'Plan description','id'=>"description",'placeholder'=>"Plan description...",'required'=>false,'value'=>(isset($plan)?$plan->description:null)])
    </div>
    <div class="col-md-12">
        @include('partials.input',['name'=>'Features list (separate features with comma)','id'=>"features",'placeholder'=>"Plan Features comma separated...",'required'=>false,'value'=>(isset($plan)?$plan->features:null)])
    </div>
</div>

@include('partials.input',['type'=>'number','name'=>'Price','id'=>"price",'placeholder'=>"Plan prce",'required'=>true,'value'=>(isset($plan)?$plan->price:null)])

@if (config('settings.enable_credits'))
    <div style="width: 50%;">
        @include('partials.input',['class'=>'','additionalInfo'=>'Number of credits that will be added to the user\'s account when they subscribe to this plan, on the interval selected below','type'=>'number','name'=>'Credit amount','id'=>"credit_amount",'placeholder'=>"Plan credit amount",'required'=>true,'value'=>(isset($plan)?$plan->credit_amount:null)])
    </div>
@endif

<div class="row">
    <!-- THIS IS SPECIAL -->
    <div class="col-md-6">
        <label class="form-control-label">{{ __("Plan period") }}</label>
        <div class="custom-control custom-radio mb-3">
            <input name="period" class="custom-control-input" id="monthly"  @if (isset($plan))  @if ($plan->period == 1) checked @endif @else checked @endif  value="monthly" type="radio">
            <label class="custom-control-label" for="monthly">{{ __('Monthly') }}</label>
        </div>
        <div class="custom-control custom-radio mb-3">
            <input name="period" class="custom-control-input" id="anually" value="anually" @if (isset($plan) && $plan->period == 2) checked @endif type="radio">
            <label class="custom-control-label" for="anually">{{ __('Anually') }}</label>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 mt-4"><h6 class="heading text-muted mb-4">{{ __('Payment processor') }}</h6></div>
    @if(config('settings.subscription_processor',"Stripe")=='Stripe')
    <div class="col-md-6">
        @include('partials.input',['name'=>'Stripe Pricing Plan ID','id'=>"stripe_id",'placeholder'=>"Product price plan id from Stripe starting with price_xxxxxx",'required'=>false,'value'=>(isset($plan)?$plan->stripe_id:null)])
    </div>
@else
    @if(strtolower(config('settings.subscription_processor'))!='local')
        @include($theSelectedProcessor."-subscribe::planid")
    @endif
@endif
</div>

<div class="row">
    
    <div class="col-12 mt-4"><h6 class="heading text-muted mb-4">{{ __('Plan limits') }}</h6></div>
    @if (config('settings.limit_items_show',true))
        <div class="col-md-6">
            @include('partials.input',['type'=>"number", 'name'=>config('settings.limit_items_name',"Limit items"),'id'=>"limit_items",'placeholder'=>"Number of allowed usage",'required'=>false,'additionalInfo'=>"0 is unlimited numbers of usage per plan period",'value'=>(isset($plan)?$plan->limit_items:null)])
        </div>
    @endif

    @if (config('settings.limit_views_show',false))
        <div class="col-md-6">
            @include('partials.input',['type'=>"number", 'name'=>config('settings.limit_views_name',"Limit views"),'id'=>"limit_views",'placeholder'=>"Number of allowed usage",'required'=>false,'additionalInfo'=>"0 is unlimited numbers of usage per plan period",'value'=>(isset($plan)?$plan->limit_views:null)])
        </div>
   @endif

   @if (config('settings.limit_orders_show',false))
        <div class="col-md-6">
            @include('partials.input',['type'=>"number", 'name'=>config('settings.limit_orders_name',"Limit orders"),'id'=>"limit_orders",'placeholder'=>"Number of allowed usage",'required'=>false,'additionalInfo'=>"0 is unlimited numbers of usage per plan period",'value'=>(isset($plan)?$plan->limit_orders:null)])
        </div>
   @endif
   
   

</div>
   
<input name="ordering" value="enabled" type="hidden" /> 

@include('plans.plugins')

<div class="text-center">
    <button type="submit" class="btn btn-success mt-4">{{ isset($plan)?__('Update plan'):__('SAVE') }}</button>
</div>
