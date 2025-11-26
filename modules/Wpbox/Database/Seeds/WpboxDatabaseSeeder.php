<?php

namespace Modules\Wpbox\Database\Seeds;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class WpboxDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

    
        if(config('settings.app_code_name')!='wpbox' && config('settings.app_code_name')!='whatssupport'){
            return;
        }

        //Insert landing page data
        $this->call(LandingSeeder::class);
        $this->call(LandingSupportSeeder::class);

       //if project is not in demo mode, don't insert demo data
       if(!config('settings.is_demo',false)){
            return;
        }

       
        Model::unguard();


        //Insert company
        try{
        $this->call(PricingPlansTableSeeder::class);
        }catch(\Exception $e){
        }

        try{
            //Insert company
            $this->call(CompanyTableSeeder::class);
        }catch(\Exception $e){
        }

        //Insert contacts and messages
        try{
            $this->call(ContactsAndMessagesTableSeeder::class);
        }catch(\Exception $e){
        }

        //Insert journies
        try{
            $this->call(JourniesDatabaseSeeder::class);
        }catch(\Exception $e){
        }

        //Insert reservations
        try{
            $this->call(ReservationsTableSeeder::class);
        }catch(\Exception $e){
        }
        
        Model::reguard();
    }


    
}
