<?php

namespace Modules\Wpbox\Database\Seeds;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class LandingSupportSeeder extends Seeder
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
            [
                'type' => 'feature',
                'title' => '{"en":"WhatsApp AI Chat Agents 🤖"}',
                'description' => '{"en":"Revolutionary AI-powered chat agents that provide instant, intelligent responses to customer inquiries 24/7. Our AI agents learn from your knowledge base, understand context, and can handle multiple conversations simultaneously. When needed, they seamlessly transfer to human agents. Say goodbye to long wait times and hello to instant support."}',
                'image' => 'https://mobidonia-demo.imgix.net/img/ai_chat4.png'
            ],
            [
                'type' => 'feature',
                'title' => '{"en":"WhatsApp Calls 📞"}',
                'description' => '{"en":"Groundbreaking WhatsApp voice call integration that lets your customers connect with your support team instantly. No need for traditional phone systems - handle voice support directly through WhatsApp. Record calls, track call duration, and provide personalized voice assistance that your customers prefer."}',
                'image' => 'https://mobidonia-demo.imgix.net/img/wcall2.png'
            ],
            [
                'type' => 'feature',
                'title' => '{"en":"Multi-Agent Chat System"}',
                'description' => '{"en":"Powerful chat system designed for support teams. Send documents, images, videos, and use quick replies to resolve customer issues faster. Assign conversations to specific agents, set priorities, and track response times. Built-in templates help your team provide consistent, professional support."}',
                'image' => 'https://mobidonia-demo.imgix.net/img/full_chat4.png'
            ],
            [
                'type' => 'feature',
                'title' => '{"en":"AI Flows"}',
                'description' => '{"en":"Groundbreaking AI-powered chat agent that are trained on your knowledge base, online shop, website and previous conversations to provide accurate, context-aware responses. When they can\'t handle a query, they automatically route it to a human agent with full conversation context."}',
                'image' => 'https://mobidonia-demo.imgix.net/img/flowssupport.png'
            ],
           
            [
                'type' => 'feature',
                'title' => '{"en":"Help Center"}',
                'description' => '{"en":"Build a comprehensive knowledge base that powers your AI agents and helps customers find answers instantly. Create articles, FAQs, and guides that are searchable and easily accessible. Your AI agents automatically pull information from the knowledge base to provide accurate responses."}',
                'image' => 'https://mobidonia-demo.imgix.net/img/support3.png'
            ],
            [
                'type' => 'feature',
                'title' => '{"en":"Analytics & Reports 📊"}',
                'description' => '{"en":"Get deep insights into your support operations. Track response times, resolution rates, customer satisfaction scores, and agent performance. Identify trends, bottlenecks, and opportunities to improve. Make data-driven decisions to optimize your support team\'s efficiency."}',
                'image' => 'https://mobidonia-demo.imgix.net/img/analyticswps.png'
            ],
        ];
        $main_features = [
            [
                'type' => 'mainfeature',
                'title' => '{"en":"AI Chat Agents"}',
                'description' => '{"en":"24/7 intelligent automated support."}',
                'image' => "https://mobidonia-demo.imgix.net/img/robot.png"
            ],
            [
                'type' => 'mainfeature',
                'title' => '{"en":"WhatsApp Calls"}',
                'description' => '{"en":"Voice support directly on WhatsApp."}',
                'image' => "https://mobidonia-demo.imgix.net/img/phone.png"
            ],
            [
                'type' => 'mainfeature',
                'title' => '{"en":"Smart Analytics"}',
                'description' => '{"en":"Track performance and optimize support."}',
                'image' => "https://mobidonia-demo.imgix.net/img/analytics_icon.png"
            ],
        ];

        $faq = [
            [
                'type' => 'faq',
                'title' => '{"en":"How do AI Chat Agents work?"}',
                'description' => '{"en":"Our AI Chat Agents are powered by advanced AI technology. They learn from your knowledge base and previous conversations to provide accurate, context-aware responses. When they can\'t handle a query, they automatically route it to a human agent with full conversation context."}'
            ],
            [
                'type' => 'faq',
                'title' => '{"en":"Can I handle voice calls through WhatsApp?"}',
                'description' => '{"en":"Absolutely! Our groundbreaking WhatsApp Call feature allows your customers to make voice calls directly to your support team through WhatsApp. No traditional phone systems needed. You can record calls, track duration, and manage everything from our unified dashboard."}'
            ],
            [
                'type' => 'faq',
                'title' => '{"en":"Is my customer data secure?"}',
                'description' => '{"en":"Yes. We take data security very seriously. Our platform employs end-to-end encryption, follows WhatsApp\'s security protocols, and complies with GDPR and other data protection regulations. Your customer conversations and data are completely secure."}'
            ],
            [
                'type' => 'faq',
                'title' => '{"en":"Can I integrate with my existing helpdesk or CRM?"}',
                'description' => '{"en":"Yes! WhatsAppHelp is designed to work seamlessly with your existing tools. We offer integrations with popular helpdesk platforms, CRMs, and other customer service tools through our API and webhooks."}'
            ],
            [
                'type' => 'faq',
                'title' => '{"en":"How many agents can use the system?"}',
                'description' => '{"en":"You can add as many agents as your plan allows. Each agent gets their own dashboard to manage conversations, view analytics, and access the knowledge base. Team collaboration features help your agents work together efficiently."}'
            ]
        ];
        

        
        // You can now use the $testimonials array in your PHP application as needed.
        

       
        $content = array_merge($faq,$features,$main_features);
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
