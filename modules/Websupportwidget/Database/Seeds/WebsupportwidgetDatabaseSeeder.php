<?php

namespace Modules\Websupportwidget\Database\Seeds;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Company;
use Modules\Websupportwidget\Models\WebsupportWidget;

class WebsupportwidgetDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $companyId = Company::query()->value('id');

        if (! $companyId) {
            Model::reguard();
            return;
        }

        $exists = WebsupportWidget::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->exists();

        if (! $exists) {
            $businessHours = [
                'monday' => ['enabled' => true, 'open' => '09:00', 'close' => '17:00'],
                'tuesday' => ['enabled' => true, 'open' => '09:00', 'close' => '17:00'],
                'wednesday' => ['enabled' => true, 'open' => '09:00', 'close' => '17:00'],
                'thursday' => ['enabled' => true, 'open' => '09:00', 'close' => '17:00'],
                'friday' => ['enabled' => true, 'open' => '09:00', 'close' => '17:00'],
                'saturday' => ['enabled' => false, 'open' => '00:00', 'close' => '00:00'],
                'sunday' => ['enabled' => false, 'open' => '00:00', 'close' => '00:00'],
            ];

            $company = Company::query()->find($companyId);

            WebsupportWidget::withoutGlobalScopes()->create([
                'id' => 'demo',
                'company_id' => $companyId,

                'logo' => 'https://mobidonia-demo.imgix.net/img/logo_mobidonia.png?w=100&h=100',
                'company_name' => $company?->name ?? 'Demo Company',
                'welcome_message' => 'Welcome! How can we help you today?',
                'primary_color' => '#4F46E5',
                'secondary_color' => '#6366F1',
                'position' => 'bottom-right',

                'chat_enabled' => true,
                'whatsapp_number' => null,
                'chat_welcome_message' => 'Hi! 👋 Start a chat with us on WhatsApp.',
                'chat_button_text' => 'Start Chat',

                'email_enabled' => true,
                'email_recipient' => null,
                'email_subject_prefix' => 'Contact Form',
                'email_welcome_message' => 'Send us a message and we will reply by email.',
                'email_success_message' => 'Thank you! We will get back to you soon.',

                'help_enabled' => true,
                'help_welcome_message' => 'Browse help articles or search for answers.',
                'help_articles_limit' => 5,
                'help_show_search' => true,

                'show_company_logo' => true,
                'show_agent_status' => true,
                'offline_message' => 'We are currently offline. Leave a message and we will respond ASAP.',
                'business_hours' => $businessHours,
                'timezone' => 'UTC',

                // Call fields (migrations 2025_10_13_*)
                'call_enabled' => true,
                'call_info_message' => 'We are available Mon-Fri 9:00-17:00 UTC. Tap the button below to call us on WhatsApp.',
                'call_link_url' => 'https://mobidonia.com',
            ]);
        }

        Model::reguard();
    }
}
