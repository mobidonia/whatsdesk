<div class="row mb-4 mt--3">
    <div class="col-md-12">
        <div class="card bg-secondary shadow">
            <div class="card-header border-0">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h3 class="mb-0">{{ __('Your current plan') }}</h3>
                    </div>

                </div>
            </div>
            <div class="card-body">
                <p>{{ __('You are currently using the ').$planAttribute['plan']['name']." ".__('plan') }}<p>

                <!-- ORDERS -->
                <div class="alert alert-{{$planAttribute['ordersAlertType']}}" role="alert">
                    {{ $planAttribute['ordersMessage'] }}
                </div>

                <!-- ITEMS -->
                <div class="alert alert-{{$planAttribute['itemsAlertType']}}" role="alert">
                    {{ $planAttribute['itemsMessage'] }}
                </div>

                
                    


                @if(strlen(auth()->user()->plan_status)>0)
                <p>{{ __('Status').": "}} <strong>{{ __(auth()->user()->plan_status) }}</strong><p>
                @endif
            </div>

            @if(!$showLinkToPlans)
                @if(strlen(auth()->user()->cancel_url)>5 && ( config('settings.subscription_processor') == "Stripe"))
                    <div class="card-footer py-4">
                        🔒 {{ __('Subscriptions are managed by Stripe securely.') }}

                        <br />
                        <a href="{{ route('billing') }}" class="btn btn-sm btn-outline-danger">{{__('Manage subscription')}}</a>
                    </div>
                @endif

                @if (!(config('settings.subscription_processor') == "Stripe" || config('settings.subscription_processor') == "Local"))
                    <!-- Payment processor actions -->
                    @include($subscription_processor.'-subscribe::actions')
                @endif
            @else
                <div class="card-footer py-4 allign-right right">
                    <a href="{{ route('plans.current') }}" class="btn btn-success">{{__('Go to plans')}}</a>
                </div>
            @endif

            
        </div>

    </div>

</div>