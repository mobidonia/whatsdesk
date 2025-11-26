<?php

namespace Modules\Wpbox\Database\Seeds;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Journies\Models\Journey;

class JourniesDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        try {

            //We  need to load the templates for our company
            //Load WhatsApp templates
            //Auth as the first company user
            $user = User::where('id', 2)->first();
            Auth::login($user);
            $templatesController = new \Modules\Wpbox\Http\Controllers\TemplatesController();
            $templatesController->loadTemplatesFromWhatsApp();

            //Make the wa_campaigns
            DB::table('wa_campaings')->insert([
                [
                    'id' => 1,
                    'name' => 'Marketing welcome',
                    'send_to' => 1,
                    'sended_to' => 0,
                    'delivered_to' => 0,
                    'read_by' => 0,
                    'total_contacts' => 2,
                    'timestamp_for_delivery' => null,
                    'variables' => '{"header":{"1":"Daniel"}}',
                    'variables_match' => '{"header":{"1":"-1"}}',
                    'media_link' => null,
                    'company_id' => 1,
                    'template_id' => 293671820450781,
                    'group_id' => null,
                    'contact_id' => null,
                    'created_at' => '2025-01-03 18:48:25',
                    'updated_at' => '2025-01-03 18:48:25',
                    'bot_type' => 2,
                    'trigger' => '',
                    'used' => 0,
                    'is_bot_active' => 1,
                    'is_bot' => 0,
                    'is_api' => 1,
                    'is_active' => 1,
                    'is_reminder' => 0
                ],
                [
                    'id' => 2,
                    'name' => 'Order renewal',
                    'send_to' => 1,
                    'sended_to' => 0,
                    'delivered_to' => 0,
                    'read_by' => 0,
                    'total_contacts' => 2,
                    'timestamp_for_delivery' => null,
                    'variables' => '{"header":{"1":"123"},"body":{"1":"Daniel","2":"12.04.2025"}}',
                    'variables_match' => '{"header":{"1":"-2"},"body":{"1":"-1","2":"-2"}}',
                    'media_link' => null,
                    'company_id' => 1,
                    'template_id' => 389865660153031,
                    'group_id' => null,
                    'contact_id' => null,
                    'created_at' => '2025-01-03 18:51:04',
                    'updated_at' => '2025-01-03 18:51:04',
                    'bot_type' => 2,
                    'trigger' => '',
                    'used' => 0,
                    'is_bot_active' => 1,
                    'is_bot' => 0,
                    'is_api' => 1,
                    'is_active' => 1,
                    'is_reminder' => 0
                ],
                [
                    'id' => 3,
                    'name' => 'Seasonal offer',
                    'send_to' => 1,
                    'sended_to' => 0,
                    'delivered_to' => 0,
                    'read_by' => 0,
                    'total_contacts' => 2,
                    'timestamp_for_delivery' => null,
                    'variables' => '{"header":{"1":"Summer Sale"},"body":{"1":"the end of August","2":"25OFF","3":"25%"}}',
                    'variables_match' => '{"header":{"1":"-2"},"body":{"1":"-2","2":"-2","3":"-2"}}',
                    'media_link' => null,
                    'company_id' => 1,
                    'template_id' => 544074895014925,
                    'group_id' => null,
                    'contact_id' => null,
                    'created_at' => '2025-01-03 18:52:39',
                    'updated_at' => '2025-01-03 18:52:39',
                    'bot_type' => 2,
                    'trigger' => '',
                    'used' => 0,
                    'is_bot_active' => 1,
                    'is_bot' => 0,
                    'is_api' => 1,
                    'is_active' => 1,
                    'is_reminder' => 0
                ],
                [
                    'id' => 4,
                    'name' => 'WhatsBox Offer',
                    'send_to' => 1,
                    'sended_to' => 0,
                    'delivered_to' => 0,
                    'read_by' => 0,
                    'total_contacts' => 2,
                    'timestamp_for_delivery' => null,
                    'variables' => '{"header":{"1":"Whats Box"},"buttons":[["1235465"],{"1":"whatsbox-the-whatsapp-marketing-bulk-sender-chat-bots-saas\/48623156"}]}',
                    'variables_match' => '{"header":{"1":"-2"},"buttons":[["-2"],{"1":"-2"}]}',
                    'media_link' => null,
                    'company_id' => 1,
                    'template_id' => 696201845814025,
                    'group_id' => null,
                    'contact_id' => null,
                    'created_at' => '2025-01-03 18:54:00',
                    'updated_at' => '2025-01-03 18:54:00',
                    'bot_type' => 2,
                    'trigger' => '',
                    'used' => 0,
                    'is_bot_active' => 1,
                    'is_bot' => 0,
                    'is_api' => 1,
                    'is_active' => 1,
                    'is_reminder' => 0
                ],
                [
                    'id' => 5,
                    'name' => 'Feedback',
                    'send_to' => 1,
                    'sended_to' => 0,
                    'delivered_to' => 0,
                    'read_by' => 0,
                    'total_contacts' => 2,
                    'timestamp_for_delivery' => null,
                    'variables' => '{"body":{"1":"Daniel"}}',
                    'variables_match' => '{"body":{"1":"-1"}}',
                    'media_link' => null,
                    'company_id' => 1,
                    'template_id' => 2555109624691478,
                    'group_id' => null,
                    'contact_id' => null,
                    'created_at' => '2025-01-03 18:54:44',
                    'updated_at' => '2025-01-03 18:54:44',
                    'bot_type' => 2,
                    'trigger' => '',
                    'used' => 0,
                    'is_bot_active' => 1,
                    'is_bot' => 0,
                    'is_api' => 1,
                    'is_active' => 1,
                    'is_reminder' => 0
                ]
            ]);
            


            //Make 2 journies - Marketing and CRM for pizza restaurant
            $journey1 = Journey::create([
                'name' => 'Marketing Journey',
                'description' => 'Journey for managing marketing campaigns, promotions and customer engagement for our pizza restaurant',
                'company_id' => 1,
            ]);

            $journey2 = Journey::create([
                'name' => 'Customer Relations Journey', 
                'description' => 'Journey for managing customer relationships, feedback and loyalty programs for our pizza restaurant',
                'company_id' => 1,
            ]);


            //Add the stages to the journey
            //Add stages for Marketing Journey
            DB::table('journey_stages')->insert([
                [
                    'id' => 1,
                    'journey_id' => 1,
                    'name' => 'Lead',
                    'campaign_id' => 1,
                    'created_at' => '2025-01-03 19:00:25',
                    'updated_at' => '2025-01-03 19:00:25'
                ],
                [
                    'id' => 2, 
                    'journey_id' => 1,
                    'name' => 'Prospect',
                    'campaign_id' => 4,
                    'created_at' => '2025-01-03 19:00:50',
                    'updated_at' => '2025-01-03 19:00:50'
                ],
                [
                    'id' => 3,
                    'journey_id' => 1,
                    'name' => 'Advocate',
                    'campaign_id' => 3,
                    'created_at' => '2025-01-03 19:02:44',
                    'updated_at' => '2025-01-03 19:02:44'
                ]
            ]);

            //Add stages for Customer Relations Journey
            DB::table('journey_stages')->insert([
                [
                    'id' => 4,
                    'journey_id' => 2,
                    'name' => 'Feedback',
                    'campaign_id' => 5,
                    'created_at' => '2025-01-03 19:03:43',
                    'updated_at' => '2025-01-03 19:03:43'
                ],
                [
                    'id' => 5,
                    'journey_id' => 2,
                    'name' => 'Closing',
                    'campaign_id' => 2,
                    'created_at' => '2025-01-03 19:04:03',
                    'updated_at' => '2025-01-03 19:04:03'
                ]
            ]);

            //Add contacts to the journey
            DB::table('journey_stage_contacts')->insert([
                'id' => 1,
                'stage_id' => 1,
                'contact_id' => 1,
                'created_at' => null,
                'updated_at' => null
            ]);

        } catch(\Exception $e) {
            //Do nothing
            Log::error('Error creating journeys: ' . $e->getMessage());
        }

        Model::reguard();
    }
}
