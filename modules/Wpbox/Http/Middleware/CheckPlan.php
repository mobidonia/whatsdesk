<?php

namespace Modules\Wpbox\Http\Middleware;

use App\Models\Plans;
use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Akaunting\Module\Facade as Module;

class CheckPlan
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
        
        //Get the current plan of the aunthenticated user
        if(Auth::user()->hasRole('admin')|| ! Module::has('pricing') ){
            return $next($request);
        }else if(Auth::user()->hasRole('owner')){
            $plan = Plans::find(Auth::user()->mplanid());
        }else{
            $plan = Plans::find( Auth::user()->company->user->mplanid());
        }
        

        if(!$plan){
            return redirect(route('plans.current'))->withError(__('There is no plan assigned to your account. Please contact the administrator.'));
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
        $company = Auth::user()->currentCompany();

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
        if($allowedMessages > 0 && $messagesCount >= $allowedMessages){
            return redirect(route('plans.current'))->withError(__('You have exceeded the limit of messages allowed in your plan'));
        }

        if($allowedCampaigns > 0 && $campaignsCount >= $allowedCampaigns){
            return redirect(route('plans.current'))->withError(__('You have exceeded the limit of campaigns allowed in your plan'));
        }

        if($allowedContacts > 0 && $contactsCount >= $allowedContacts){
            return redirect(route('plans.current'))->withError(__('You have exceeded the limit of contacts allowed in your plan'));
        }


       





        return $next($request);
    }
}
