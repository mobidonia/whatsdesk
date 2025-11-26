<?php

namespace Modules\Dashboard\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Plans;
use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{

    public function index()
    {
        if(auth()->user()->hasRole(['admin'])){
            return $this->asAdmin();
        }else{
            return null;
        }
    }
    
    public function asAdmin()
    {

        $data=[
            'total_users' => '',
            'users_this_month' => '',
            'total_paying_users'=> '',
            'total_paying_users_this_month' => '',
            'mrr' => 0,
            'customers'=>[],
            'arr'=>[],
        ];
        $startOfMonth=Carbon::now()->startOfMonth();

        //Count total users
        $users=User::role('owner');
        $data['total_users'] = $users->count();

        //Count total cards this month of the year
        $data['users_this_month'] = $users->where('created_at', '>=',$startOfMonth )->count();

        //Count  paying users given
        $usersPaing=User::role('owner')->where('plan_id', '!=',intval(config('settings.free_pricing_id')));
        $data['total_paying_users'] = $usersPaing->count();

        //Count total cards this month of the year
        $data['total_paying_users_this_month'] = $usersPaing->where('created_at', '>=',$startOfMonth )->count();
        $usersPaing=User::role('owner')->where('plan_id', '!=',intval(config('settings.free_pricing_id')));
        
        //Count  MRR
        $plansMonthly=Plans::where('id', '!=',intval(config('settings.free_pricing_id')))->where('period',1)->pluck('price','id')->toArray();
        $plansYearly=Plans::where('id', '!=',intval(config('settings.free_pricing_id')))->where('period',2)->pluck('price','id')->toArray();
        $plans=Plans::pluck('name','id')->toArray();

        foreach($usersPaing->get() as $user){
            if(isset($plansMonthly[$user->plan_id])){
                $data['mrr'] += $plansMonthly[$user->plan_id];
            }
            if(isset($plansYearly[$user->plan_id])){
                $data['mrr'] += $plansYearly[$user->plan_id]/12;
            }
            
        }
  
        $data['arr']=Money($data['mrr']*12,config('settings.cashier_currency'),config('settings.do_convertion',true));
        $data['mrr']=Money($data['mrr'],config('settings.cashier_currency'),config('settings.do_convertion',true));


        //Get last 5 customers
        $data['clients']=Company::orderBy('created_at','desc')->take(5)->get();
        $data['plans']=$plans;

        //If plans are disabled, then calculatte monthly and total revenue so far
        if(!config('settings.enable_pricing')){
            $className = config('settings.model_for_payment');
            if($className=="none"){
                $thisMonth=0;
                $total=0;

                $thisMonthDocs=0;
                $totalDocs=0;
            
                $data['month']=0;
                $data['total']=0;
        
                $data['month_docs']=0;
                $data['total_docs']=0;

                $data['month_docs_np']=0;
                $data['total_docs_np']=0;
            }else{
                $theModel=new $className();
                $thisMonth=$theModel::where('created_at', '>=',$startOfMonth )->where('paid',1)->sum('price');
                $total=$theModel::where('paid',1)->sum('price');

                $thisMonthDocs=$theModel::where('created_at', '>=',$startOfMonth )->where('paid',1)->count();
                $totalDocs=$theModel::where('paid',1)->count();
            
                $data['month']=Money($thisMonth/100,config('settings.cashier_currency'),config('settings.do_convertion',true));
                $data['total']=Money($total/100,config('settings.cashier_currency'),config('settings.do_convertion',true));
        
                $data['month_docs']=$thisMonthDocs;
                $data['total_docs']=$totalDocs;

                $data['month_docs_np']=$theModel::where('created_at', '>=',$startOfMonth )->count();
                $data['total_docs_np']=$theModel::count();
            }
            
        }

        return $data;
    }
}