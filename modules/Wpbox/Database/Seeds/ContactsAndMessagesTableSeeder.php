<?php

namespace Modules\Wpbox\Database\Seeds;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Modules\Contacts\Models\Field;
use Modules\Wpbox\Models\Reply;
use Illuminate\Support\Facades\DB;
use Modules\Contacts\Models\Group;
use Modules\Wpbox\Models\Contact;
use Modules\Wpbox\Models\Message;

class ContactsAndMessagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        //Contact fields and groups

        //Fields [rating,orders_made,email]
        $rating=Field::create([
            'name'=>"rating",
            'company_id'=>1,
            'type'=>"text"
        ]);
        $orders_made=Field::create([
            'name'=>"orders_made",
            'company_id'=>1,
            'type'=>"number"
        ]);
        $email=Field::create([
            'name'=>"email",
            'company_id'=>1,
            'type'=>"email"
        ]);


        $active=Group::create([
            'name'=>"Active clients",
            'company_id'=>1,
        ]);
        $noactive=Group::create([
            'name'=>"Potential clients",
            'company_id'=>1,
        ]);
        $europe=Group::create([
            'name'=>"Europe clients",
            'company_id'=>1,
        ]);
       
        //Contact 1
        $demoContact1=Contact::create([
            'name' => 'Daniel Dimov',
            'phone' =>  '+38978203673',
            'avatar'=> 'https://secure.gravatar.com/avatar/e2909c35cdbad84bf2b6059fe7eab2444cd6bd9fbf8af59918a1d0f4901c8ad2?s=128',
            'company_id'=>1,
            'has_chat'=>true,
            'created_at' => now()->subHour(),
            'updated_at' => now()->subHour(),
            'last_support_reply_at'=>now(),
            'last_reply_at'=>now(),
            "last_message"=>"Sure, you can visit us from 09AM - 08PM",
            "is_last_message_by_contact"=>false,  
            "email"=>"daniel@mobidonia.com"  
        ]);
        $demoContact1->fields()->attach($rating->id, ['value' => 5]);
        $demoContact1->fields()->attach($orders_made->id, ['value' => 10]);
        $demoContact1->groups()->sync([$active->id,$europe->id]);
        

         //Contact 2
         $demoContact2=Contact::create([
            'name' => 'Aleksandra Dimova',
            'phone' =>  '+38978514084',
            'avatar'=> 'https://ca.slack-edge.com/T0JNGF37X-U109XCZC1-a1e054cf2aa3-512',
            'company_id'=>1,
            'has_chat'=>true,
            'last_reply_at'=>now(),
            'last_support_reply_at'=>now(),
            'created_at' => now()->subHour(2),
            'updated_at' => now()->subHour(2),
            "last_message"=>"Thanks for the information. I'll review the details.",
            "is_last_message_by_contact"=>true, 
            "email"=>"aleks@mobidonia.com"
        ]);
        $demoContact2->fields()->attach($rating->id, ['value' => 5]);
        $demoContact2->fields()->attach($orders_made->id, ['value' => 0]);
        $demoContact2->groups()->sync([$noactive->id,$europe->id]);

        //Add 7 other contacts
        for($i=0;$i<15;$i++){
            Contact::create([
                'name' => \Faker\Factory::create()->name(),
                'phone' =>  '+' . \Faker\Factory::create()->numerify('###########'),
                'avatar'=> 'https://i.pravatar.cc/300?u=' . \Faker\Factory::create()->uuid(),
                'last_message'=>\Faker\Factory::create()->sentence(),
                'last_reply_at'=>now()->subHour(2+$i),
                'last_support_reply_at'=>now()->subHour(2+$i),
                'company_id'=>1,
                'has_chat'=>true,
                'created_at' => now()->subHour(2+$i),
                'updated_at' => now()->subHour(2+$i),
            ]);
        }

        $messagesArray = [
            [
                'value' => "Hi, I'd like to place an order for a custom pizza, please.",
                'is_message_by_contact' => true,
            ],
            [
                'value' => "Of course! We'd be happy to help you create your perfect pizza. What toppings would you like?",
                'is_message_by_contact' => false,
            ],
            [
                'value' => "Great! I'd like to start with a thin crust, please.",
                'is_message_by_contact' => true,
            ],
            [
                'value' => "Noted! Thin crust it is. What type of sauce would you prefer? We have marinara, garlic white sauce, and pesto.",
                'is_message_by_contact' => false,
            ],
            [
                'value' => "I'll go with marinara sauce, please.",
                'is_message_by_contact' => true,
            ],
            [
                'value' => "Got it, marinara sauce. Now, let's move on to the toppings. How about cheese? We have mozzarella, cheddar, feta, and parmesan.",
                'is_message_by_contact' => false,
            ],
            [
                'value' => "I'll go with mozzarella cheese, please.",
                'is_message_by_contact' => true,
            ],
            [
                'value' => "Mozzarella it is. And what about the main toppings? We offer pepperoni, sausage, mushrooms, onions, bell peppers, and olives.",
                'is_message_by_contact' => false,
            ],
            [
                'value' => "I'd like pepperoni and mushrooms, please.",
                'is_message_by_contact' => true,
            ],
            [
                'value' => "Perfect, pepperoni and mushrooms. Any additional toppings? We also have options like bacon, spinach, tomatoes, and pineapple.",
                'is_message_by_contact' => false,
            ],
            [
                'value' => "I'll add some bacon and tomatoes.",
                'is_message_by_contact' => true,
            ],
            [
                'value' => "Bacon and tomatoes, got it. Lastly, any extra flavor with herbs or spices? We have options like garlic, oregano, and red pepper flakes.",
                'is_message_by_contact' => false,
            ],
            [
                'value' => "I'll sprinkle some garlic and oregano, please.",
                'is_message_by_contact' => true,
            ],
            [
                'value' => "Excellent choices! To summarize, you'd like a thin crust pizza with marinara sauce, mozzarella cheese, topped with pepperoni, mushrooms, bacon, and tomatoes. Finished off with a sprinkle of garlic and oregano.",
                'is_message_by_contact' => false,
            ],
            [
                'value' => "That's correct! Can't wait to try it.",
                'is_message_by_contact' => true,
            ],
            [
                'value' => "Your custom pizza sounds delicious! We'll get it prepared for you right away. Is there anything else you'd like to add to your order?",
                'is_message_by_contact' => false,
            ],
            [
                'value' => "No, that'll be all. Thank you!",
                'is_message_by_contact' => true,
            ],
            [
                'value' => "You're welcome! Your order will be ready in about 20 minutes. Feel free to stop by for pickup. Enjoy your meal!",
                'is_message_by_contact' => false,
            ],
        ];
        

        //
         foreach ($messagesArray as $key => $message) {
            $demoContact1->sendDemoMessage($message['value'],$message['is_message_by_contact']);
         }


         $messagesArray = [
            [
                'value' => "Hi, I'm interested in renting a space for a party. Can you provide me with some information?",
                'is_message_by_contact' => true,
            ],
            [
                'value' => "Certainly! We offer various event spaces for rent. Could you please let us know the date and approximate number of guests for your party?",
                'is_message_by_contact' => false,
            ],
            [
                'value' => "I'm looking to host the party on the 15th of next month. I expect around 50-60 guests.",
                'is_message_by_contact' => true,
            ],
            [
                'value' => "Great! Our event spaces can accommodate that size. Do you have any specific theme or requirements for the party?",
                'is_message_by_contact' => false,
            ],
            [
                'value' => "Yes, I'm planning a 'retro arcade' theme. It would be fantastic if the space could be decorated accordingly.",
                'is_message_by_contact' => true,
            ],
            [
                'value' => "Sounds like a fun theme! We can definitely arrange decorations to match. Are you looking for any particular amenities, like audio systems or catering services?",
                'is_message_by_contact' => false,
            ],
            [
                'value' => "I'll need audio systems for some nostalgic tunes. As for catering, can you provide a list of available options and menus?",
                'is_message_by_contact' => true,
            ],
            [
                'value' => "Certainly! We have different catering packages, including finger foods, buffet, and sit-down dinner options. I can send you the menu for you to choose from.",
                'is_message_by_contact' => false,
            ],
            [
                'value' => "That would be great, thanks. Also, how long can we rent the space for, and what's the pricing like?",
                'is_message_by_contact' => true,
            ],
            [
                'value' => "Our rental durations vary, but typically range from 4 to 8 hours. As for pricing, it depends on the size of the space, amenities, and services you choose. I'll email you the detailed pricing options shortly.",
                'is_message_by_contact' => false,
            ],
            [
                'value' => "Sounds good. I'll be waiting for the email. Lastly, is there any parking available for guests?",
                'is_message_by_contact' => true,
            ],
            [
                'value' => "Absolutely! We have a spacious parking area for your guests' convenience. If you have any more questions or want to proceed with the booking, feel free to reach out.",
                'is_message_by_contact' => false,
            ],
            [
                'value' => "Thanks for the information. I'll review the details and get back to you soon.",
                'is_message_by_contact' => true,
            ]
        ];

        foreach ($messagesArray as $key => $message) {
            $demoContact2->sendDemoMessage($message['value'],$message['is_message_by_contact']);
         }


         //Add a note
        try {
            Message::create([
                'contact_id' => 1,
                'company_id' => 1,
                'header_text' => 'Note',
                'footer_text' => '',
                'header_image' => '',
                'header_video' => '',
                'header_location' => '',
                'header_document' => '',
                'buttons' => '[]',
                'value' => 'Daniel is looking for pizza that we need to add in our menu. Pizza with magic mushrooms',
                'error' => '',
                'is_campign_messages' => 0,
                'is_message_by_contact' => 0,
                'message_type' => 1,
                'status' => 4,
                'created_at' => now(),
                'updated_at' => now(),
                'scchuduled_at' => null,
                'components' => '',
                'campaign_id' => null,
                'header_audio' => null,
                'bot_has_replied' => 0,
                'ai_bot_has_replied' => 0,
                'original_message' => '',
                'sender_name' => 'Company owner',
                'extra' => '',
                'is_note' => 1
            ]);
        } catch (\Exception $e) {
            
        }


        //Add some replies
        $replies = [
            [
                "name" => "Working Time",
                "text" => "Our working time is from 9AM - 10PM every day except Sunday when we work from 9AM-2PM.",
                "trigger" => "working time"
            ],
            [
                "name" => "Working Time 2",
                "text" => "Our working time is from 9AM - 10PM every day except Sunday when we work from 9AM-2PM.",
                "trigger" => "working hours"
            ],
            [
                "name" => "Script info",
                "text" => "Hi, glad to have your interest. In this demo we are acting like a pizza restaurant. So ask us about our working time, delivery, menu and our simple bot will reply.",
                "trigger" => "cool script"
            ]

            
        ];

        foreach ($replies as $key => $reply) {
            Reply::create([
                'name'=>$reply['name'],
                'text'=>$reply['text'],
                'trigger'=>$reply['trigger'],
                'type'=>3,
                'company_id'=>1
            ]);
           
         }


        //SETUP THE ClOUD API
        $company=Company::findOrFail(1);

        //Get  demo token from .env
        if(config('settings.demo_val1')){
            $company->setConfig('whatsapp_permanent_access_token',config('settings.demo_val1',""));
            $company->setConfig('whatsapp_phone_number_id',config('settings.demo_val2',""));
            $company->setConfig('whatsapp_business_account_id',config('settings.demo_val3',""));       
        }


        $company->setConfig('whatsapp_webhook_verified',"yes");
        $company->setConfig('whatsapp_settings_done',"yes");
        $company->setConfig('plain_token',"wi9DM0WGlvjtSzx7O8mXB6rEHOIWXbZzQPGZtyzd8622eb1d");
        $company->setConfig('black_listed_phone_numbers','601158554611,+601158554611,6285176999198,+6285176999198');


        //The Personal Access Tokens
        DB::table('personal_access_tokens')->insertGetId([
            'tokenable_type' =>'App\Models\User',
            'tokenable_id' => 2,
            'name' => 'Whatstapp',
            'token' => '1b823baa3f0ae42edcedd2160a9ceb3c8186823254bf835a421e8aef0cfdb912',
            'abilities' => '["*"]',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        



        
        Model::reguard();
    }
}
