@extends('layouts.app', ['title' => __('Pages')])

@section('content')
    <div class="header  pb-8 pt-5 pt-md-8">
    </div>


    <div class="container-fluid mt--9">
        
        @if($currentPlan)
            <!-- Show Current form actions -->
            @include("plans.info",['planAttribute'=> $planAttribute,'showLinkToPlans'=>false])
        @endif

        <div class="row">

            
            <!-- Notifications -->
            <div class="col-12">
                @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                 <!-- Errors display -->
                @if (session('error'))
                 <div role="alert" class="alert alert-danger">{{ session('error') }}</div>
                @endif

            </div>

            <!-- Print the plans -->
            @foreach ($plans as $plan)
                @if(   !( config('settings.forceUserToPay',false)&& intval(config('settings.free_pricing_id')).""==$plan['id']."")  )
                    <div class="col-md-{{ $col}}">
                        <!-- single plan -->
                        <div class="card shadow">
                            <div class="card-header border-0">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <h3 class="mb-0">{{ $plan['name'] }}</h3>
                                    </div>
                                    <div class="col-4">
                                        <h3 class="mb-0">@money($plan['price'], config('settings.site_currency','usd'),config('settings.site_do_currency',true))/{{ $plan['period']==1?__('m'):__('y') }}</h3>
                                    </div>

                                </div>
                            </div>


                            @if(count($plans))
                            <div class="table-responsive">
                                <table class="table align-items-center table-flush">
                                    <thead class="thead-light">
                                        <tr>
                                            <th scope="col">{{ __('Features') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach (explode(",",$plan['features']) as $feature)
                                            <tr>
                                                <td>{{ __(trim($feature)) }} </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>


                            </div>
                            @endif
                            <div class="card-footer py-4">
                                @if($currentPlan&&$plan['id'].""==$currentPlan->id."")
                                    <a href="" class="btn btn-primary disabled">{{__('Current Plan')}}</a>
                                @else

                                <!-- Button holder -->
                                <div id="button-container-plan-{{$plan['id']}}"></div>

                                    
                                    
                                    @if(strlen($plan['stripe_id'])>2&&config('settings.subscription_processor')=='Stripe')
                                        <a href="javascript:showStripeCheckout({{ $plan['id'] }} , '{{ $plan['name'] }}')" class="btn btn-primary">{{__('Switch to')." ".$plan['name']}}</a>
                                    @endif

                                    @if($plan['price']>0&&(config('settings.subscription_processor')=='Local'||config('settings.subscription_processor')=='local'))
                                        <button  data-toggle="modal" data-target="#paymentModal{{ $plan['id']  }}" class="btn btn-primary">{{__('Switch to')." ".$plan['name']}}</button>

                                        <!-- Modal -->
                                        <div class="modal fade" id="paymentModal{{ $plan['id']  }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-danger modal-dialog-centered modal-" role="document">
                                                <div class="modal-content bg-gradient-danger">
                                                <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">{{ $plan['name'] }}</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                                </div>
                                                <div class="modal-body">
                                                {{ config('settings.local_transfer_info')}}
                                                <br /><br />
                                                {{ config('settings.local_transfer_account')}}
                                                <hr /><br />
                                                {{ __('Plan price ')}}<br />
                                                @money($plan['price'], config('settings.site_currency','usd'),config('settings.site_do_currency',true))/{{ $plan['period']==1?__('m'):__('y') }}
                                                </div>
                                                <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                                                </div>
                                            </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- END TO BE REMOVED -->

                                    
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach


        </div>


        <!-- Stripe Subscription form -->
        <div class="row mt-4" id="stripe-payment-form-holder" style="display: none">
            <div class="col-md-12">
                <div class="card bg-secondary shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Subscribe to') }} <span id="plan_name">PLAN_NAME</span></h3>
                            </div>

                        </div>
                    </div>
                    <div class="card-body">

                    <form action="{{ route('plans.subscribe') }}" method="post" id="stripe-payment-form"   >
                            @csrf
                            <input name="plan_id" id="plan_id" type="hidden" />
                            <div style="width: 100%;" class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
                                <input name="name" id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" placeholder="{{ __( 'Name on card' ) }}" value="{{auth()->user()?auth()->user()->name:""}}" required>
                                @if ($errors->has('name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form">
                                <div style="width: 100%;" #stripecardelement  id="card-element" class="form-control">

                                <!-- A Stripe Element will be inserted here. -->
                              </div>

                              <!-- Used to display form errors. -->
                              <br />
                              <div class="" id="card-errors" role="alert">

                              </div>
                          </div>
                          <div class="text-center" id="totalSubmitStripe">
                            <button
                                v-if="totalPrice"
                                type="submit"
                                class="btn btn-success mt-4 paymentbutton"
                                >{{ __('Subscribe') }}</button>
                          </div>

                          </form>


                    </div>
                </div>
            </div>
        </div>

       


        @include('layouts.footers.auth')
    </div>
@endsection
@section('js')


<script type="text/javascript">
    $(".btn-sub-actions").on('click',function() {
        var action = $(this).attr('data-action');

        $('#action').val(action);
        document.getElementById('form-subscription-actions').submit();
    });

    function showLocalPayment(plan_name,plan_id){
        alert(plan_name);
    }
    
    var plans = <?php echo json_encode($plans) ?>;
    var user = <?php echo json_encode(auth()->user()) ?>;
    var payment_processor = <?php echo json_encode(config('settings.subscription_processor')) ?>;

    
</script>

@if (config('settings.subscription_processor') == "Stripe")
<!-- Stripe -->
<script src="https://js.stripe.com/v3/"></script>

<script>
  "use strict";
  var STRIPE_KEY="{{ config('settings.stripe_key') }}";
  var ENABLE_STRIPE="{{ config('settings.subscription_processor')=='Stripe' }}";
  if(ENABLE_STRIPE){
      js.initStripe(STRIPE_KEY,"stripe-payment-form");
  }

  function validateOrderFormSubmit(){
      return true;
  }

  function showStripeCheckout(plan_id,plan_name){
   $('#plan_id').val(plan_id);
   $('#plan_name').html(plan_name);
   $('#stripe-payment-form-holder').show();
  }
</script>
@else 
    @if (!(config('settings.subscription_processor') == "Local"))
        <!-- Payment Processors JS Modules -->
        @include($subscription_processor.'-subscribe::subscribe')
    @endif

@endif







@endsection
