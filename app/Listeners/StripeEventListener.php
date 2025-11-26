<?php

namespace App\Listeners;

use App\Models\Plans;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Events\WebhookHandled;

class StripeEventListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(WebhookHandled $event): void
    {
        if ($event->payload['type'] === 'checkout.session.completed') {
            //Get the user
            try {
                $user = User::where('stripe_id', $event->payload['data']['object']['customer'])->first();
                if ($user) {
                    //Get the plan  
                    $plan = Plans::where('stripe_id', $event->payload['data']['object']['metadata']['plan_id'])->first();
                    if ($plan) {
                        $user->plan_id = $plan->id;
                        $user->plan_status = 'active';
                        $user->save();

                        //Activate credits
                       if($plan->credit_amount>0&&config('settings.enable_credits',true)){
                         $user->company->addCredits($plan->credit_amount,$plan->name." ".Carbon::now()->format('F Y'),Carbon::now()->addDays($plan->period==1?30:365));
                       }
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error activating credits: ' . $e->getMessage());
            }
        }
    }
}
