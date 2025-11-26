<?php

namespace Modules\Wpbox\Http\Middleware;

use App\Models\Plans;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;

class CheckAPIPlan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        //get the token
        $token = $request->token;
        $token = PersonalAccessToken::findToken($request->token);
        $plan=null;
        if(!$token){
            return response()->json(['status'=>'error','message'=>'Invalid token']);
        }else{
            $user=User::findOrFail($token->tokenable_id);
            $plan = Plans::find($user->mplanid());
        }
    
        if(!$plan){
            return response()->json(['status'=>'error','message'=>'Invalid plan']);
        }

        $daysToCheck = 30;

        $allowedCampaigns = intval($plan->limit_items);
        $allowedMessages = intval($plan->limit_views);
        $allowedContacts = intval($plan->limit_orders);

        //If it is yearly plan, check for the last 365 days
        if($plan->period==2){
            $daysToCheck = 365;
            $allowedCampaigns = intval($plan->limit_items)*12;
            $allowedMessages = intval($plan->limit_views)*12;
            $allowedContacts = intval($plan->limit_orders)*12;
        }

        //Get the current company of the aunthenticated user
        $company = $user->currentCompany();

        //Get the number of messages in the last 30 days
        $messagesCount = DB::table('messages')
        ->where('created_at', '>=', Carbon::now()->subDays($daysToCheck))
        ->where('company_id', $company->id)
        ->whereNotNull('fb_message_id')
        ->count();

        //Get the number of campaigns in the last 30 days
        $campaignsCount = DB::table('wa_campaings')
        ->where('created_at', '>=', Carbon::now()->subDays($daysToCheck))
        ->where('company_id', $company->id)
        ->count();

        //Get the number of contacts
        $contactsCount = DB::table('contacts')
        ->where('company_id', $company->id)
        ->count();

        //Check if the user has exceeded the limits
        if($allowedMessages > 0 && $messagesCount > $allowedMessages){
            //return redirect(route('plans.current'))->withError(__('You have exceeded the limit of messages allowed in your plan'));
            return response()->json(['status'=>'error','message'=>'You have exceeded the limit of messages allowed in your plan']);
        }

        if($allowedCampaigns > 0 && $campaignsCount > $allowedCampaigns){
            //return redirect(route('plans.current'))->withError(__('You have exceeded the limit of campaigns allowed in your plan'));
            return response()->json(['status'=>'error','message'=>'You have exceeded the limit of campaigns allowed in your plan']);
        }

        if($allowedContacts > 0 && $contactsCount > $allowedContacts){
            //return redirect(route('plans.current'))->withError(__('You have exceeded the limit of contacts allowed in your plan'));
            return response()->json(['status'=>'error','message'=>'You have exceeded the limit of contacts allowed in your plan']);
        }
        return $next($request);
    }
}
