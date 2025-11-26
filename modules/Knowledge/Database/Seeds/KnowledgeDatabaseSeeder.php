<?php

namespace Modules\Knowledge\Database\Seeds;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Knowledge\Models\KnowledgeArticle;
use Modules\Knowledge\Models\KnowledgeCategory;

class KnowledgeDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // Only seed in demo mode
        if (! config('settings.is_demo', false)) {
            return;
        }

        try {
            // Create sample categories with rich content
            $categories = [
                [
                    'name' => 'Installation',
                    'slug' => 'installation',
                    'description' => 'Step-by-step guide to install WhatsBox on your server.',
                    'sort_order' => 1,
                    'is_active' => true,
                    'company_id' => 1,
                ],
                [
                    'name' => 'Setup',
                    'slug' => 'setup',
                    'description' => 'Configure WhatsBox and connect your WhatsApp Business Account.',
                    'sort_order' => 2,
                    'is_active' => true,
                    'company_id' => 1,
                ],
                [
                    'name' => 'How to Update',
                    'slug' => 'how-to-update',
                    'description' => 'Keep your WhatsBox installation up to date with the latest features and security patches.',
                    'sort_order' => 3,
                    'is_active' => true,
                    'company_id' => 1,
                ],
                [
                    'name' => 'Create a Plugin for WhatsBox',
                    'slug' => 'create-a-plugin',
                    'description' => 'Developer guide to extend WhatsBox functionality with custom plugins.',
                    'sort_order' => 4,
                    'is_active' => true,
                    'company_id' => 1,
                ],
                [
                    'name' => 'FAQ',
                    'slug' => 'faq',
                    'description' => 'Frequently asked questions and answers about WhatsBox.',
                    'sort_order' => 5,
                    'is_active' => true,
                    'company_id' => 1,
                ],
                [
                    'name' => 'User Docs',
                    'slug' => 'user-docs',
                    'description' => 'Complete guide for using WhatsBox features and managing customer conversations.',
                    'sort_order' => 6,
                    'is_active' => true,
                    'company_id' => 1,
                ],
                [
                    'name' => 'Change Log',
                    'slug' => 'change-log',
                    'description' => 'Track all updates, new features, and improvements to WhatsBox.',
                    'sort_order' => 7,
                    'is_active' => true,
                    'company_id' => 1,
                ],
            ];

            foreach ($categories as $categoryData) {
                $category = KnowledgeCategory::create($categoryData);

                // Create multiple articles for each category
                $this->createArticlesForCategory($category);
            }

            //code...
        } catch (\Throwable $th) {
            //throw $th;
        }

        Model::reguard();
    }

    /**
     * Create articles for a specific category
     */
    private function createArticlesForCategory($category)
    {
        $articles = $this->getArticlesForCategory($category->slug);

        foreach ($articles as $index => $articleData) {
            KnowledgeArticle::create([
                'category_id' => $category->id,
                'company_id' => 1,
                'title' => $articleData['title'],
                'slug' => $articleData['slug'],
                'content' => $articleData['content'],
                'excerpt' => $articleData['excerpt'],
                'status' => 'published',
                'is_featured' => $index < 2, // First 2 articles are featured
                'sort_order' => $index + 1,
                'views_count' => rand(50, 1000),
            ]);
        }
    }

    /**
     * Get articles data for each category
     */
    private function getArticlesForCategory($categorySlug)
    {
        $articlesData = [
            'installation' => [
                [
                    'title' => 'Server Requirements for WhatsBox',
                    'slug' => 'server-requirements',
                    'excerpt' => 'Essential server requirements and specifications needed to run WhatsBox.',
                    'content' => '<h2>Server Requirements for WhatsBox</h2><p>Before installing WhatsBox, ensure your server meets these minimum requirements:</p><h3>Minimum Requirements</h3><ul><li><strong>PHP:</strong> 8.1 or higher</li><li><strong>MySQL:</strong> 5.7+ or MariaDB 10.3+</li><li><strong>Web Server:</strong> Apache 2.4+ or Nginx 1.18+</li><li><strong>RAM:</strong> 2GB minimum (4GB recommended)</li><li><strong>Disk Space:</strong> 2GB minimum</li><li><strong>SSL Certificate:</strong> Required for HTTPS</li></ul><h3>Required PHP Extensions</h3><ul><li>OpenSSL PHP Extension</li><li>PDO PHP Extension</li><li>Mbstring PHP Extension</li><li>Tokenizer PHP Extension</li><li>XML PHP Extension</li><li>Ctype PHP Extension</li><li>JSON PHP Extension</li><li>BCMath PHP Extension</li><li>cURL PHP Extension</li><li>GD Library or Imagick</li></ul><h3>Additional Requirements</h3><ul><li><strong>Composer:</strong> Latest version for dependency management</li><li><strong>NPM:</strong> For frontend asset compilation</li><li><strong>Cron:</strong> For scheduled tasks and queue processing</li></ul><blockquote>⚠️ <strong>Important:</strong> WhatsBox requires a valid SSL certificate to work with WhatsApp Business API. HTTP-only installations will not function properly.</blockquote>',
                ],
                [
                    'title' => 'Installing WhatsBox Step by Step',
                    'slug' => 'installing-whatsbox-step-by-step',
                    'excerpt' => 'Complete installation guide from downloading to first login.',
                    'content' => '<h2>Installing WhatsBox Step by Step</h2><p>Follow these steps to install WhatsBox on your server.</p><h3>Step 1: Download WhatsBox</h3><p>Download the latest version of WhatsBox from your customer portal or the provided download link.</p><h3>Step 2: Upload Files</h3><p>Extract the downloaded ZIP file and upload all files to your web server via FTP or cPanel File Manager. Place them in your domain\'s root directory (usually <code>public_html</code> or <code>www</code>).</p><h3>Step 3: Create Database</h3><ol><li>Log into your cPanel or hosting control panel</li><li>Navigate to MySQL Databases</li><li>Create a new database (e.g., <code>whatsbox_db</code>)</li><li>Create a database user with a strong password</li><li>Grant ALL PRIVILEGES to the user for the database</li></ol><h3>Step 4: Configure Environment</h3><p>Rename <code>.env.example</code> to <code>.env</code> and update these values:</p><pre><code>APP_URL=https://yourdomain.com\nDB_DATABASE=whatsbox_db\nDB_USERNAME=your_db_user\nDB_PASSWORD=your_db_password</code></pre><h3>Step 5: Run Installation Wizard</h3><ol><li>Navigate to <code>https://yourdomain.com/install</code></li><li>Follow the on-screen instructions</li><li>Enter your purchase code</li><li>Complete the installation</li></ol><h3>Step 6: Set Up Cron Job</h3><p>Add this cron job to run every minute:</p><pre><code>* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1</code></pre><p><strong>Congratulations!</strong> WhatsBox is now installed. Log in with the admin credentials you created during installation.</p>',
                ],
            ],
            'setup' => [
                [
                    'title' => 'Connecting WhatsApp Business API',
                    'slug' => 'connecting-whatsapp-business-api',
                    'excerpt' => 'Learn how to connect your WhatsApp Business API account to WhatsBox.',
                    'content' => '<h2>Connecting WhatsApp Business API</h2><p>WhatsBox supports multiple WhatsApp Business API providers. Follow this guide to connect your account.</p><h3>Supported Providers</h3><ul><li>Meta Cloud API (Official)</li><li>360Dialog</li><li>Twilio</li><li>Gupshup</li><li>MessageBird</li><li>Custom Webhook Providers</li></ul><h3>Meta Cloud API Setup</h3><ol><li>Go to <strong>Settings → WhatsApp Connection</strong></li><li>Select "Meta Cloud API" as your provider</li><li>Enter your Phone Number ID from Meta Business Suite</li><li>Add your WhatsApp Business Account ID</li><li>Paste your System User Access Token</li><li>Click "Verify Connection"</li></ol><h3>Required Permissions</h3><p>Ensure your access token has these permissions:</p><ul><li>whatsapp_business_messaging</li><li>whatsapp_business_management</li><li>business_management</li></ul><h3>Webhook Configuration</h3><p>Configure your webhook in Meta Business Suite:</p><ul><li><strong>Callback URL:</strong> <code>https://yourdomain.com/api/webhook/whatsapp</code></li><li><strong>Verify Token:</strong> Generate one in WhatsBox settings</li><li><strong>Subscribe to:</strong> messages, message_status</li></ul><h3>Testing Connection</h3><p>After setup, send a test message from WhatsBox to verify the connection is working properly.</p><blockquote>💡 <strong>Pro Tip:</strong> Store your access token securely and never share it publicly. Rotate tokens regularly for better security.</blockquote>',
                ],
                [
                    'title' => 'Configuring Business Hours and Auto-Responses',
                    'slug' => 'configuring-business-hours-auto-responses',
                    'excerpt' => 'Set up automated responses, business hours, and away messages.',
                    'content' => '<h2>Configuring Business Hours and Auto-Responses</h2><p>Automate your customer support with smart auto-responses and business hours settings.</p><h3>Setting Business Hours</h3><ol><li>Navigate to <strong>Settings → Business Hours</strong></li><li>Enable "Business Hours" toggle</li><li>Select your timezone</li><li>Configure hours for each day of the week</li><li>Add holidays and special closures</li></ol><h3>Welcome Messages</h3><p>Create a greeting message sent automatically when customers first contact you:</p><ul><li>Go to <strong>Settings → Auto-Responses</strong></li><li>Enable "Welcome Message"</li><li>Customize your greeting text</li><li>Add quick reply buttons (optional)</li><li>Include media like your logo or promo image</li></ul><h3>Away Messages</h3><p>Configure messages sent outside business hours:</p><ul><li>Enable "Away Message"</li><li>Write a custom message informing about business hours</li><li>Set expected response time</li><li>Include alternative contact methods if needed</li></ul><h3>Quick Replies</h3><p>Set up quick reply shortcuts for common questions:</p><ol><li>Go to <strong>Quick Replies</strong> section</li><li>Click "Add Quick Reply"</li><li>Set a shortcut code (e.g., /hours)</li><li>Write the response message</li><li>Save and test</li></ol><h3>Example Quick Replies</h3><ul><li><code>/hours</code> - Business hours information</li><li><code>/pricing</code> - Pricing and packages</li><li><code>/track</code> - Order tracking instructions</li><li><code>/support</code> - Contact support information</li></ul><p>Agents can type these shortcuts to quickly send pre-defined messages.</p>',
                ],
            ],
            'how-to-update' => [
                [
                    'title' => 'Updating WhatsBox via Auto-Updater',
                    'slug' => 'updating-whatsbox-auto-updater',
                    'excerpt' => 'Use the built-in auto-updater to update WhatsBox with one click.',
                    'content' => '<h2>Updating WhatsBox via Auto-Updater</h2><p>WhatsBox includes a built-in auto-updater for seamless updates without manual file uploads.</p><h3>Before You Update</h3><p><strong>Important:</strong> Always backup before updating!</p><ol><li>Backup your database via cPanel or phpMyAdmin</li><li>Backup all files via FTP or cPanel File Manager</li><li>Put site in maintenance mode (optional but recommended)</li></ol><h3>Using the Auto-Updater</h3><ol><li>Log in to your WhatsBox admin panel</li><li>Go to <strong>Settings → System Updates</strong></li><li>Click "Check for Updates"</li><li>If an update is available, click "Update Now"</li><li>Wait for the update to complete (do not close the browser)</li><li>After completion, clear your browser cache</li></ol><h3>Update Process</h3><p>The auto-updater will:</p><ul><li>Download the latest version</li><li>Extract and replace updated files</li><li>Run database migrations</li><li>Clear application cache</li><li>Rebuild assets if needed</li></ul><h3>Troubleshooting Updates</h3><p>If the update fails:</p><ul><li>Check file permissions (755 for folders, 644 for files)</li><li>Ensure your PHP max_execution_time is at least 300 seconds</li><li>Verify you have enough disk space</li><li>Check error logs at <code>storage/logs/laravel.log</code></li></ul><h3>After Update</h3><ul><li>Clear browser cache (Ctrl+Shift+Delete)</li><li>Test major features</li><li>Check WhatsApp connection status</li><li>Verify cron jobs are running</li></ul><blockquote>✅ <strong>Best Practice:</strong> Schedule updates during low-traffic periods to minimize disruption to your support operations.</blockquote>',
                ],
                [
                    'title' => 'Manual Update Process',
                    'slug' => 'manual-update-process',
                    'excerpt' => 'Step-by-step guide for manually updating WhatsBox files.',
                    'content' => '<h2>Manual Update Process</h2><p>If the auto-updater doesn\'t work, you can update WhatsBox manually by uploading files.</p><h3>Step 1: Backup Everything</h3><p><strong>Critical:</strong> Create complete backups before proceeding!</p><ul><li>Database backup (via phpMyAdmin or cPanel)</li><li>All files backup (via FTP)</li><li>Save your <code>.env</code> file separately</li><li>Note any custom modifications you\'ve made</li></ul><h3>Step 2: Download New Version</h3><ol><li>Log into your customer portal</li><li>Download the latest WhatsBox version</li><li>Extract the ZIP file on your computer</li></ol><h3>Step 3: Upload Files</h3><ol><li>Connect to your server via FTP</li><li>Upload all files from the update package</li><li>Overwrite existing files when prompted</li><li><strong>Do NOT overwrite:</strong> <code>.env</code> file and <code>storage/</code> folder</li></ol><h3>Step 4: Run Update Commands</h3><p>Connect via SSH and run these commands:</p><pre><code>cd /path/to/whatsbox\nphp artisan down\ncomposer install --no-dev\nphp artisan migrate --force\nphp artisan config:cache\nphp artisan route:cache\nphp artisan view:cache\nphp artisan up</code></pre><h3>Step 5: Set Correct Permissions</h3><pre><code>chmod -R 755 storage bootstrap/cache\nchmod -R 644 .env</code></pre><h3>Step 6: Verify Update</h3><ul><li>Check the version number in Settings</li><li>Test WhatsApp message sending</li><li>Verify all features are working</li><li>Check for any errors in logs</li></ul><h3>Common Issues</h3><p><strong>500 Internal Server Error:</strong> Check file permissions and clear cache</p><p><strong>Database errors:</strong> Run migrations again with <code>php artisan migrate --force</code></p><p><strong>WhatsApp not connecting:</strong> Verify webhook URL is still correct</p>',
                ],
            ],
            'create-a-plugin' => [
                [
                    'title' => 'Plugin Development Basics',
                    'slug' => 'plugin-development-basics',
                    'excerpt' => 'Learn the fundamentals of creating plugins for WhatsBox.',
                    'content' => '<h2>Plugin Development Basics</h2><p>Extend WhatsBox functionality with custom plugins using our modular architecture.</p><h3>Plugin Structure</h3><p>WhatsBox uses Laravel Modules for plugin architecture. Each plugin is a self-contained module:</p><pre><code>modules/YourPlugin/\n├── Config/\n├── Database/\n│   ├── Migrations/\n│   └── Seeds/\n├── Http/\n│   ├── Controllers/\n│   └── Requests/\n├── Models/\n├── Providers/\n├── Resources/\n│   └── views/\n├── Routes/\n│   ├── api.php\n│   └── web.php\n├── composer.json\n└── module.json</code></pre><h3>Creating Your First Plugin</h3><p>Generate a new plugin using Artisan:</p><pre><code>php artisan module:make YourPluginName</code></pre><h3>Module.json Configuration</h3><p>Configure your plugin in <code>module.json</code>:</p><pre><code>{\n  "name": "YourPluginName",\n  "alias": "yourplugin",\n  "description": "Plugin description",\n  "version": "1.0.0",\n  "active": 1,\n  "order": 100,\n  "providers": [\n    "Modules\\\\YourPlugin\\\\Providers\\\\YourPluginServiceProvider"\n  ]\n}</code></pre><h3>Registering Routes</h3><p>Define routes in <code>Routes/web.php</code>:</p><pre><code>Route::middleware([\'auth\'])->group(function () {\n    Route::get(\'/yourplugin\', [YourController::class, \'index\'])\n        ->name(\'yourplugin.index\');\n});</code></pre><h3>Creating Controllers</h3><pre><code>php artisan module:make-controller YourController YourPlugin</code></pre><h3>Database Migrations</h3><pre><code>php artisan module:make-migration create_your_table YourPlugin</code></pre><h3>Hooks and Events</h3><p>WhatsBox fires events you can listen to:</p><ul><li><code>MessageReceived</code> - When a WhatsApp message arrives</li><li><code>MessageSent</code> - When a message is sent</li><li><code>ConversationAssigned</code> - When an agent is assigned</li><li><code>TicketClosed</code> - When a conversation is closed</li></ul><blockquote>📚 <strong>Documentation:</strong> Full API documentation available at https://docs.whatsbox.com/api</blockquote>',
                ],
                [
                    'title' => 'WhatsBox API Hooks and Filters',
                    'slug' => 'whatsbox-api-hooks-filters',
                    'excerpt' => 'Comprehensive guide to available hooks and filters for plugin development.',
                    'content' => '<h2>WhatsBox API Hooks and Filters</h2><p>Use hooks and filters to extend WhatsBox without modifying core files.</p><h3>Available Event Hooks</h3><p><strong>Message Events:</strong></p><pre><code>// Listen for incoming messages\nEvent::listen(MessageReceived::class, function ($event) {\n    $message = $event->message;\n    $sender = $message->sender;\n    // Your custom logic\n});</code></pre><p><strong>Conversation Events:</strong></p><ul><li><code>ConversationCreated</code> - New conversation started</li><li><code>ConversationAssigned</code> - Assigned to agent</li><li><code>ConversationClosed</code> - Conversation marked as closed</li><li><code>ConversationReopened</code> - Closed conversation reopened</li></ul><h3>Filter Hooks</h3><p>Modify data before it\'s processed:</p><pre><code>// Modify outgoing message before sending\nadd_filter(\'whatsbox_outgoing_message\', function($message) {\n    // Add custom signature\n    $message .= "\\n\\n---\\nSent via WhatsBox";\n    return $message;\n});</code></pre><h3>Available Filters</h3><ul><li><code>whatsbox_incoming_message</code> - Modify incoming messages</li><li><code>whatsbox_outgoing_message</code> - Modify outgoing messages</li><li><code>whatsbox_contact_data</code> - Modify contact information</li><li><code>whatsbox_auto_reply</code> - Customize auto-replies</li><li><code>whatsbox_agent_assignment</code> - Control agent assignment logic</li></ul><h3>Custom Sidebar Widgets</h3><p>Add widgets to the conversation sidebar:</p><pre><code>// In your Service Provider\npublic function boot()\n{\n    View::composer(\'whatsbox::conversations.show\', function ($view) {\n        $view->with(\'sidebarWidgets\', [\n            \'yourplugin::widgets.custom-info\'\n        ]);\n    });\n}</code></pre><h3>Registering Settings</h3><p>Add settings to the admin panel:</p><pre><code>config([\'settings.your_plugin_setting\' => [\n    \'type\' => \'text\',\n    \'label\' => \'Your Setting\',\n    \'default\' => \'value\',\n    \'group\' => \'Your Plugin\'\n]]);</code></pre><h3>Queue Jobs</h3><p>Process long-running tasks in background:</p><pre><code>dispatch(new YourCustomJob($data))\n    ->onQueue(\'whatsbox-plugins\');</code></pre><h3>Testing Your Plugin</h3><ul><li>Use Laravel\'s testing features</li><li>Test hooks don\'t break core functionality</li><li>Verify WhatsApp message flow works</li><li>Check performance impact</li></ul>',
                ],
            ],
            'faq' => [
                [
                    'title' => 'Common Questions About WhatsBox',
                    'slug' => 'common-questions-whatsbox',
                    'excerpt' => 'Answers to frequently asked questions about WhatsBox features and functionality.',
                    'content' => '<h2>Common Questions About WhatsBox</h2><h3>General Questions</h3><p><strong>Q: What is WhatsBox?</strong></p><p>A: WhatsBox is a self-hosted WhatsApp support platform that helps businesses manage customer conversations through WhatsApp Business API. It includes features like multi-agent support, automated responses, conversation routing, and comprehensive analytics.</p><p><strong>Q: Do I need WhatsApp Business API?</strong></p><p>A: Yes, WhatsBox requires WhatsApp Business API access. Regular WhatsApp or WhatsApp Business app won\'t work. You can get API access through Meta directly or through BSP partners like 360Dialog, Twilio, or Gupshup.</p><p><strong>Q: Can I use WhatsBox with multiple phone numbers?</strong></p><p>A: Yes, WhatsBox supports multiple WhatsApp Business accounts. Each phone number can have its own set of agents, auto-replies, and configurations.</p><h3>Technical Questions</h3><p><strong>Q: What are the server requirements?</strong></p><p>A: You need PHP 8.1+, MySQL 5.7+, at least 2GB RAM, and a valid SSL certificate. See our Installation guide for complete requirements.</p><p><strong>Q: Is WhatsBox cloud-based or self-hosted?</strong></p><p>A: WhatsBox is self-hosted, meaning you install it on your own server or hosting. This gives you complete control over your data and conversations.</p><p><strong>Q: Can I customize the interface?</strong></p><p>A: Yes! WhatsBox is built on Laravel and is fully customizable. You can modify the design, add custom features through plugins, or integrate with other systems.</p><h3>Pricing & Licensing</h3><p><strong>Q: Is there a monthly fee?</strong></p><p>A: WhatsBox is a one-time purchase. You own the software forever. You only pay for WhatsApp API usage to your BSP provider and your hosting costs.</p><p><strong>Q: Can I use one license on multiple domains?</strong></p><p>A: Each license is valid for one production domain. You can use localhost and staging domains for testing without additional licenses.</p>',
                ],
                [
                    'title' => 'Troubleshooting Common Issues',
                    'slug' => 'troubleshooting-common-issues',
                    'excerpt' => 'Solutions to common problems and error messages in WhatsBox.',
                    'content' => '<h2>Troubleshooting Common Issues</h2><h3>Messages Not Receiving</h3><p><strong>Problem:</strong> WhatsApp messages aren\'t appearing in WhatsBox</p><p><strong>Solutions:</strong></p><ul><li>Verify webhook URL is correctly configured in your WhatsApp provider dashboard</li><li>Check webhook verify token matches</li><li>Ensure your server is accessible from the internet (not blocked by firewall)</li><li>Check webhook logs in WhatsApp provider dashboard</li><li>Verify SSL certificate is valid and not expired</li></ul><h3>Messages Not Sending</h3><p><strong>Problem:</strong> Unable to send messages from WhatsBox</p><p><strong>Solutions:</strong></p><ul><li>Check WhatsApp API connection status in Settings</li><li>Verify access token hasn\'t expired</li><li>Ensure phone number is not banned or restricted</li><li>Check message template is approved if required</li><li>Review API error logs in <code>storage/logs/laravel.log</code></li></ul><h3>500 Internal Server Error</h3><p><strong>Problem:</strong> White screen or 500 error</p><p><strong>Solutions:</strong></p><ol><li>Enable debug mode in <code>.env</code>: <code>APP_DEBUG=true</code></li><li>Check error logs: <code>storage/logs/laravel.log</code></li><li>Verify file permissions: <code>chmod -R 755 storage bootstrap/cache</code></li><li>Clear cache: <code>php artisan cache:clear</code></li><li>Regenerate config: <code>php artisan config:cache</code></li></ol><h3>Queue Jobs Not Processing</h3><p><strong>Problem:</strong> Messages stuck in queue, automated tasks not running</p><p><strong>Solutions:</strong></p><ul><li>Verify cron job is set up and running every minute</li><li>Check queue worker status: <code>php artisan queue:work --once</code></li><li>Review failed jobs: <code>php artisan queue:failed</code></li><li>Restart queue worker if using supervisor</li></ul><h3>Database Connection Error</h3><p><strong>Problem:</strong> Cannot connect to database</p><p><strong>Solutions:</strong></p><ul><li>Verify database credentials in <code>.env</code></li><li>Check database server is running</li><li>Ensure database user has proper privileges</li><li>Test connection: <code>php artisan tinker</code> then <code>DB::connection()->getPdo();</code></li></ul><h3>Still Need Help?</h3><p>If these solutions don\'t resolve your issue:</p><ul><li>Check our detailed logs for specific error messages</li><li>Search our support forum</li><li>Contact support with your error logs and system information</li><li>Join our community Slack channel for quick help</li></ul>',
                ],
            ],
            'user-docs' => [
                [
                    'title' => 'Managing Conversations',
                    'slug' => 'managing-conversations',
                    'excerpt' => 'Complete guide for agents to handle customer conversations in WhatsBox.',
                    'content' => '<h2>Managing Conversations</h2><p>Learn how to efficiently manage customer conversations as an agent in WhatsBox.</p><h3>Conversation Interface</h3><p>The main interface has three sections:</p><ul><li><strong>Left Sidebar:</strong> List of active conversations</li><li><strong>Center Panel:</strong> Message history and input area</li><li><strong>Right Sidebar:</strong> Contact information and conversation details</li></ul><h3>Responding to Messages</h3><ol><li>Click on a conversation from the sidebar</li><li>Type your response in the message input box</li><li>Press Enter or click Send</li><li>Use Shift+Enter for line breaks</li></ol><h3>Sending Media Files</h3><p>Click the attachment icon to send:</p><ul><li><strong>Images:</strong> JPG, PNG (max 5MB)</li><li><strong>Documents:</strong> PDF, DOC, XLS (max 100MB)</li><li><strong>Videos:</strong> MP4 (max 16MB)</li><li><strong>Audio:</strong> MP3, OGG (max 16MB)</li></ul><h3>Using Quick Replies</h3><p>Save time with quick reply shortcuts:</p><ul><li>Type <code>/</code> to see available quick replies</li><li>Select from dropdown or type the shortcut code</li><li>Quick replies support variables like {customer_name}</li></ul><h3>Conversation Actions</h3><p><strong>Assign to Agent:</strong> Click the assign icon and select an agent</p><p><strong>Add Tags:</strong> Organize conversations with tags like "urgent", "sales", "technical"</p><p><strong>Add Notes:</strong> Internal notes visible only to agents</p><p><strong>Close Conversation:</strong> Mark conversation as resolved</p><h3>Contact Management</h3><p>View and edit contact details in the right sidebar:</p><ul><li>Name and phone number</li><li>Email address</li><li>Custom fields</li><li>Conversation history</li><li>Tags and notes</li></ul><h3>Keyboard Shortcuts</h3><ul><li><code>Ctrl/Cmd + K</code> - Quick search conversations</li><li><code>Ctrl/Cmd + Enter</code> - Send message</li><li><code>/</code> - Open quick replies</li><li><code>Esc</code> - Close conversation</li></ul><h3>Best Practices</h3><ul><li>Respond within 24 hours to avoid session expiry</li><li>Use templates for common responses</li><li>Always add notes for context when transferring</li><li>Close conversations after resolution</li><li>Keep contact information updated</li></ul>',
                ],
                [
                    'title' => 'Using Message Templates',
                    'slug' => 'using-message-templates',
                    'excerpt' => 'How to create and use WhatsApp message templates for better engagement.',
                    'content' => '<h2>Using Message Templates</h2><p>Message templates allow you to send messages to customers outside the 24-hour customer care window.</p><h3>What are Message Templates?</h3><p>WhatsApp requires pre-approved templates for business-initiated conversations. Templates must be approved by WhatsApp before use.</p><h3>Template Categories</h3><ul><li><strong>Marketing:</strong> Promotional offers, product announcements</li><li><strong>Utility:</strong> Order updates, appointment reminders, account alerts</li><li><strong>Authentication:</strong> OTP codes, login verification</li></ul><h3>Creating Templates in WhatsBox</h3><ol><li>Go to <strong>Templates → Create New</strong></li><li>Choose template category</li><li>Select language (you can have multiple language versions)</li><li>Write template name (lowercase, underscores only)</li><li>Compose your message</li><li>Add variables using <code>{{1}}</code>, <code>{{2}}</code> format</li><li>Submit for WhatsApp approval</li></ol><h3>Template Components</h3><p><strong>Header (optional):</strong></p><ul><li>Text (60 characters max)</li><li>Image</li><li>Video</li><li>Document</li></ul><p><strong>Body (required):</strong></p><ul><li>Your main message (1024 characters max)</li><li>Can include variables</li><li>Formatting: *bold*, _italic_, ~strikethrough~</li></ul><p><strong>Footer (optional):</strong></p><ul><li>Additional text (60 characters max)</li><li>Cannot contain variables</li></ul><p><strong>Buttons (optional):</strong></p><ul><li>Call to action buttons (URLs, phone calls)</li><li>Quick reply buttons</li><li>Maximum 3 buttons</li></ul><h3>Using Templates</h3><p>To send a template message:</p><ol><li>Open conversation or select contacts</li><li>Click "Send Template" button</li><li>Select approved template</li><li>Fill in variable values</li><li>Preview and send</li></ol><h3>Template Variables</h3><p>Example template body:</p><pre><code>Hello {{1}}, your order {{2}} has been shipped!\n\nTracking number: {{3}}\n\nExpected delivery: {{4}}</code></pre><p>When sending, provide values:</p><ul><li>{{1}} = Customer name</li><li>{{2}} = Order number</li><li>{{3}} = Tracking ID</li><li>{{4}} = Delivery date</li></ul><h3>Approval Guidelines</h3><p>To increase approval chances:</p><ul><li>Use clear, professional language</li><li>Provide value to customers</li><li>Follow WhatsApp commerce policy</li><li>Don\'t use promotional language in utility templates</li><li>Include opt-out information if required</li></ul><h3>Template Status</h3><ul><li><strong>Pending:</strong> Waiting for WhatsApp review</li><li><strong>Approved:</strong> Ready to use</li><li><strong>Rejected:</strong> Not approved, review reasons and resubmit</li></ul>',
                ],
            ],
            'change-log' => [
                [
                    'title' => 'Version 2.5.0 - Latest Release',
                    'slug' => 'version-2-5-0',
                    'excerpt' => 'Major update with AI-powered features and performance improvements.',
                    'content' => '<h2>Version 2.5.0 - Latest Release</h2><p class="text-muted">Released: October 10, 2025</p><h3>🎉 New Features</h3><ul><li><strong>AI-Powered Response Suggestions:</strong> Get intelligent message suggestions based on conversation context</li><li><strong>Advanced Analytics Dashboard:</strong> New charts and metrics for better insights into your support performance</li><li><strong>Bulk Message Campaigns:</strong> Send template messages to multiple contacts at once</li><li><strong>Custom Chatbot Builder:</strong> Visual flow builder for creating automated conversation flows</li><li><strong>Multi-language Support:</strong> Interface now available in 15+ languages</li><li><strong>Video Call Integration:</strong> Start WhatsApp video calls directly from conversations</li></ul><h3>✨ Improvements</h3><ul><li>40% faster message loading for conversations with 1000+ messages</li><li>Improved mobile responsive design for tablet agents</li><li>Enhanced search with fuzzy matching and filters</li><li>Better file upload handling with progress indicators</li><li>Streamlined agent permission system</li><li>Optimized database queries for better performance</li></ul><h3>🐛 Bug Fixes</h3><ul><li>Fixed issue where emojis weren\'t displaying correctly in some browsers</li><li>Resolved conversation assignment notification delays</li><li>Fixed template variable replacement in certain edge cases</li><li>Corrected timezone handling in reports</li><li>Fixed webhook retry mechanism</li><li>Resolved media preview issues for large images</li></ul><h3>🔧 Technical Changes</h3><ul><li>Updated to Laravel 10.x</li><li>Migrated to Livewire 3.0</li><li>Added Redis support for better queue performance</li><li>Improved API rate limiting</li><li>Enhanced security with CSRF token rotation</li></ul><h3>📚 Documentation</h3><ul><li>New developer guides for plugin creation</li><li>Updated API documentation with more examples</li><li>Added video tutorials for common workflows</li></ul><h3>Upgrade Notes</h3><p>⚠️ <strong>Important:</strong> This version requires PHP 8.1+. Please backup before upgrading.</p><p>Run these commands after updating:</p><pre><code>php artisan migrate --force\nphp artisan config:cache\nnpm run build</code></pre>',
                ],
                [
                    'title' => 'Version 2.4.0 - Previous Releases',
                    'slug' => 'version-2-4-0-previous',
                    'excerpt' => 'Archive of previous version updates and changes.',
                    'content' => '<h2>Version 2.4.0</h2><p class="text-muted">Released: August 15, 2025</p><h3>New Features</h3><ul><li>Department-based routing system</li><li>Customer satisfaction surveys</li><li>Scheduled message sending</li><li>Export conversations to PDF</li></ul><h3>Improvements</h3><ul><li>Better conversation filtering</li><li>Enhanced contact import/export</li><li>Improved notification system</li></ul><hr><h2>Version 2.3.0</h2><p class="text-muted">Released: June 20, 2025</p><h3>New Features</h3><ul><li>WhatsApp Business API integration with Meta Cloud</li><li>Real-time typing indicators</li><li>Message read receipts</li><li>Contact tagging system</li></ul><h3>Bug Fixes</h3><ul><li>Fixed message ordering in high-traffic scenarios</li><li>Resolved webhook timeout issues</li><li>Fixed attachment download problems</li></ul><hr><h2>Version 2.2.0</h2><p class="text-muted">Released: April 5, 2025</p><h3>New Features</h3><ul><li>Conversation notes and internal comments</li><li>Advanced quick reply system with categories</li><li>Agent performance reports</li><li>Custom fields for contacts</li></ul><h3>Improvements</h3><ul><li>Faster message sync</li><li>Better error handling</li><li>Improved queue processing</li></ul><hr><h2>Version 2.1.0</h2><p class="text-muted">Released: February 12, 2025</p><h3>New Features</h3><ul><li>Multi-agent support</li><li>Conversation assignment rules</li><li>Auto-responder with business hours</li><li>Basic analytics dashboard</li></ul><h3>Technical</h3><ul><li>Migrated to Laravel 9</li><li>Added Redis cache support</li><li>Improved database indexing</li></ul><hr><h2>Version 2.0.0</h2><p class="text-muted">Released: January 10, 2025</p><h3>Major Release</h3><ul><li>Complete UI redesign</li><li>WhatsApp Business API support</li><li>Multi-phone number support</li><li>Template message system</li><li>Role-based permissions</li></ul><h3>Breaking Changes</h3><ul><li>Minimum PHP version now 8.0</li><li>Database schema changes - run migrations</li><li>New webhook endpoint format</li></ul>',
                ],
            ],
        ];

        return $articlesData[$categorySlug] ?? [];
    }
}
