<?php

namespace Modules\Wpbox\Database\Seeds;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class LandingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();


        $features = [
            [ 'type' => 'feature','title'=>'{"en":"Email marketing is 🏴‍☠️. Say hi to Whatsapp marketing"}', 'description'=>'{"en":"Whatsapp Marketing is a new fields in Direct Marketing. Experience around 98% read rate on your campaigns and never fear your account to get blocked from Whatsapp."}', 'image'=>'https://mobidonia-demo.imgix.net/img/campaign.png'],
            
        ];
        $main_features = [
            [
                'type' => 'mainfeature',
                'title' => '{"en":"Outbound Campaigns"}',
                'description' => '{"en":"Execute your outbound campaigns effectively."}',
                'image' =>"https://mobidonia-demo.imgix.net/img/camp.png"
            ],
            [
                'type' => 'mainfeature',
                'title' => '{"en":"Better Support"}',
                'description' => '{"en":"Experience superior customer support."}',
                'image' => "https://mobidonia-demo.imgix.net/img/rating.png"
            ],
            [
                'type' => 'mainfeature',
                'title' => '{"en":"AI Chat"}',
                'description' => '{"en":"Be there 24/7 for your users."}',
                'image' => "https://mobidonia-demo.imgix.net/img/robot.png"
            ],
        ];

        $faq = [
            [
                'type' => 'faq',
                'title' => '{"en":"Can I cancel anytime?"}',
                'description' => '{"en":"Yes, you can! We believe in flexibility, and there are no long-term commitments. You can cancel your subscription at any time with no hidden fees or penalties."}'
            ],
            [
                'type' => 'faq',
                'title' => '{"en":"How do I create a marketing campaign?"}',
                'description' => '{"en":"Creating a marketing campaign is easy with our platform. We provide a user-friendly interface that allows you to design, target, and launch campaigns effortlessly. If you need assistance, our support team is always here to help."}'
            ],
            [
                'type' => 'faq',
                'title' => '{"en":"Can I integrate this with my existing tools?"}',
                'description' => '{"en":"Yes, our platform is designed to be compatible with various third-party tools and services. We offer integrations that make it easy to connect with your existing marketing stack for a seamless experience."}'
            ]
        ];
        
        // You can now use the $faqs array in your PHP application to display the frequently asked questions.
        

        $testimonials = [
            [
                'type' => 'testimonial',
                'title' => '{"en":"I love using the system"}',
                'subtitle' => '{"en":"John Doe - CEO of Marketing LTD"}',
                'description' => '{"en":"This WhatsApp marketing platform has completely transformed how we engage with our customers. It\'s a game-changer for our marketing campaigns, and the direct WhatsApp chat feature has boosted our customer interactions. The platform is user-friendly, and the support team is incredibly responsive. Highly recommend!"}',
                'image'=>'https://mobidonia-demo.imgix.net/img/testimonials/0.png?w=100&h=100'
            ],
            [
                'type' => 'testimonial',
                'title' => '{"en":"Exceptional WhatsApp Marketing"}',
                'subtitle' => '{"en":"Jane Smith - Marketing Manager"}',
                'description' => '{"en":"Your WhatsApp marketing platform has been a game-changer for our marketing efforts. The campaigns are highly effective, and the direct chat feature allows us to connect with customers on a personal level. It\'s made a significant impact on our business growth!"}',
                'image'=>'https://mobidonia-demo.imgix.net/img/testimonials/1.png?w=100&h=100'
            ],
            [
                'type' => 'testimonial',
                'title' => '{"en":"Effortless Marketing Campaigns"}',
                'subtitle' => '{"en":"David Williams - Digital Marketer"}',
                'description' => '{"en":"Using your WhatsApp marketing platform has made managing campaigns effortless. The results have been outstanding, and the direct WhatsApp chat has improved our customer engagement. The platform\'s simplicity and the support team\'s assistance have been invaluable to our success."}',
                'image'=>'https://mobidonia-demo.imgix.net/img/testimonials/2.png?w=100&h=100'
            ],
            [
                'type' => 'testimonial',
                'title' => '{"en":"A Must-Have for Marketers"}',
                'subtitle' => '{"en":"Susan Brown - Marketing Director"}',
                'description' => '{"en":"Your WhatsApp marketing SaaS platform is a must-have for any marketer. It\'s streamlined our marketing efforts, and the direct chat feature has enhanced our customer relationships. The platform is intuitive, and the support team is top-notch. We couldn\'t be happier with the results!"}',
                'image'=>'https://mobidonia-demo.imgix.net/img/testimonials/3.png?w=100&h=100'
            ],
            [
                'type' => 'testimonial',
                'title' => '{"en":"Great Customer Service"}',
                'subtitle' => '{"en":"Alex Johnson - Customer Service Manager"}',
                'description' => '{"en":"The customer service from this WhatsApp marketing platform has been exceptional. They\'re always ready to assist and make using the platform a breeze. Highly recommend!"}',
                'image'=>'https://mobidonia-demo.imgix.net/img/testimonials/4.png?w=100&h=100'
            ],
            [
                'type' => 'testimonial',
                'title' => '{"en":"Improved Business Operations"}',
                'subtitle' => '{"en":"Emily Davis - Business Owner"}',
                'description' => '{"en":"This WhatsApp marketing platform has improved our business operations significantly. The direct chat feature has made communication with customers so much easier. It\'s a fantastic tool!"}',
                'image'=>'https://mobidonia-demo.imgix.net/img/testimonials/5.png?w=100&h=100'
            ],
            [
                'type' => 'testimonial',
                'title' => '{"en":"Excellent Marketing Tool"}',
                'subtitle' => '{"en":"Michael Miller - Marketing Specialist"}',
                'description' => '{"en":"This WhatsApp marketing platform is an excellent tool for any business. It\'s easy to use and has made our marketing campaigns much more effective. The customer service is also top-notch!"}',
                'image'=>'https://mobidonia-demo.imgix.net/img/testimonials/6.png?w=100&h=100'
            ],
            [
                'type' => 'testimonial',
                'title' => '{"en":"Incredible Results"}',
                'subtitle' => '{"en":"Sarah Thompson - Sales Manager"}',
                'description' => '{"en":"This WhatsApp marketing platform has delivered incredible results for our sales team. The direct chat feature has significantly improved our customer engagement. The platform is easy to use and the support team is always ready to help. Highly recommend!"}',
                'image'=>'https://mobidonia-demo.imgix.net/img/testimonials/7.png?w=100&h=100'
            ],
            [
                'type' => 'testimonial',
                'title' => '{"en":"Boosted Our Marketing"}',
                'subtitle' => '{"en":"Robert Anderson - Marketing Executive"}',
                'description' => '{"en":"Your WhatsApp marketing platform has boosted our marketing efforts. The campaigns are highly effective and the direct chat feature allows us to connect with customers on a personal level. It\'s made a significant impact on our business growth!"}',
                'image'=>'https://mobidonia-demo.imgix.net/img/testimonials/8.png?w=100&h=100'
            ]
        ];
        
        // You can now use the $testimonials array in your PHP application as needed.
        


    
        
        

        $content = array_merge($faq, $testimonials,$features,$main_features);
        
        

        foreach ($content as $key => $element) {
            DB::table('posts')->insert([
                'post_type' => $element['type'],
                'title' => $element['title'],
                'image' => isset($element['image'])?$element['image']:null,
                'description' => $element['description'],
                'link'=>isset($element['link'])?$element['link']:null,
                'subtitle' => isset($element['subtitle'])?$element['subtitle']:null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Model::reguard();
    }
}
