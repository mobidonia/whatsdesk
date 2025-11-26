<?php

namespace Modules\Wpbox\Database\Seeds;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class CompanyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

       
        //Company owner 
        $demoOwnerId=DB::table('users')->insertGetId([
            'name' => 'Company owner',
            'email' =>  'owner@example.com',
            'password' => Hash::make('secret'),
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
            'plan_id'=> 2,
        ]);

        //Assign owner role
        $demoOwner=User::find($demoOwnerId);
        $demoOwner->assignRole('owner');

      

        // Pizza
        $lastCompanyId=DB::table('companies')->insertGetId([
            'name'=>'Mobidonia',
            'is_featured'=>1,
            'active'=>1,
            'logo'=>'https://mobidonia-demo.imgix.net/img/logo_mobidonia.png?w=100&h=100',
            'cover'=>'https://mobidonia-demo.imgix.net/img/hero_mobidonia.png?w=100&h=100',
            'subdomain'=>'mobidonia',
            'user_id'=>$demoOwnerId,
            'created_at' => now(),
            'updated_at' => now(),
            'address' => 'Vaska Kalajdjiska 25, Negotino',
            'phone' => '+38971203673',
            'whatsapp_phone' => '+38971203673',
            'description'=>'Mobidonia',
            'minimum'=>10
        ]);
        $vendor=Company::find($lastCompanyId);

        //Update the user company id
        $demoOwner->company_id=$lastCompanyId;
        $demoOwner->save();

        //Add company config
        $vendor->setConfig('enable_voiceflow',true);
        $vendor->setConfig('voiceflow_api_key',"VF.DM.661e7bc346a84b9685d499bf.nduvz3wi3GRzA3mC");


        Model::reguard();
    }
}
