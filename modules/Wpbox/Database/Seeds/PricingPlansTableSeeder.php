<?php

namespace Modules\Wpbox\Database\Seeds;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PricingPlansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

         //Whatsapp
         DB::table('plan')->insert([
            'name' => 'Lite',
            'limit_items'=>0,
            'limit_orders'=>0,
            'price'=>0,
            'paddle_id'=>'',
            'description'=>'Our Light Plan is perfect for emerging businesses, offering essential communication tools and dedicated agent support to efficiently manage a focused client base.',
            'features'=>'100 Clients, 10 campaigns per month, 1000 messages, Simple bot replies, Basic Support, 1 agent',
            'created_at' => now(),
            'updated_at' => now(),
            'enable_ordering'=>0,
        ]);

        DB::table('plan')->insert([
            'name' => 'Unlimited',
            'limit_items'=>0,
            'limit_orders'=>0,
            'price'=>25,
            'paddle_id'=>'',    
            'period'=>1,
            'description'=>'The Unlimited Plan offers ultimate flexibility and scalability for growth-focused businesses, with limitless clients, campaigns, and advanced AI and support features.',
            'features'=>'Unlimited clients, Unlimited campaigns, Unlimited messages, AI bot replies, Priority Support, Unlimited agents',
            'created_at' => now(),
            'updated_at' => now(),
            'enable_ordering'=>1,
        ]);

        Model::reguard();
    }
}
