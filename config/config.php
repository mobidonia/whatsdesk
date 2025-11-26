<?php

$env = [
    [
        'name' => 'Setup',
        'slug' => 'setup',
        'icon' => 'ni ni-settings',
        'fields' => [
            ['separator' => '🖥️ System', 'title' => 'Project name', 'key' => 'APP_NAME', 'value' => 'Site name'],
            ['title' => 'Link to your site', 'key' => 'APP_URL', 'value' => 'http://localhost'],
            ['title' => 'Subdomains', 'key' => 'IGNORE_SUBDOMAINS', 'value' => 'www,127', 'help' => 'Subdomain your app works in. ex if your subdomain is app.yourdomain.com, here you should have www,app '],
            ['title' => '🚨 App debugging', 'key' => 'APP_DEBUG', 'value' => 'true', 'ftype' => 'bool', 'help' => 'Enable if you experience error 500'],
            ['title' => 'Disable the landing page', 'help' => 'When landing page is disabled, the project will start from the login page. In this case it is best to have the system in subdomain', 'key' => 'DISABLE_LANDING', 'value' => 'false', 'ftype' => 'bool'],
            ['title' => 'Wildcard domain', 'help' => 'If you have followed the procedure to enable wildcard domain, select this so you can have shopname.yourdomain.com', 'key' => 'WILDCARD_DOMAIN_READY', 'value' => 'false', 'ftype' => 'bool'],
            ['title' => 'Pagination count', 'key' => 'PAGINATE_COUNT', 'value' => 10, 'ftype' => 'input', 'type' => 'number'],
            ['separator' => '☁️ Storage settings', 'title' => 'File system for storage', 'help' => 'Where the system will store the media - images, videos, sounds.', 'key' => 'STORAGE_TYPE', 'value' => 'public_uploads', 'ftype' => 'select', 'data' => ['public_uploads' => 'Local filesystem', 's3' => 'Amazon S3 | DigitalOcean Spaces']],
            ['title' => 'S3 AWS_ACCESS_KEY', 'key' => 'AWS_ACCESS_KEY_ID', 'value' => ''],
            ['title' => 'S3 AWS_SECRET_ACCESS_KEY', 'key' => 'AWS_SECRET_ACCESS_KEY', 'value' => ''],
            ['title' => 'S3 AWS_DEFAULT_REGION', 'key' => 'AWS_DEFAULT_REGION', 'value' => ''],
            ['title' => 'S3 AWS_BUCKET', 'key' => 'AWS_BUCKET', 'value' => ''],

            ['separator' => '🔐 Login services', 'title' => 'Google client id for sign in', 'key' => 'GOOGLE_CLIENT_ID', 'value' => ''],
            ['title' => 'Google client secret for sign in', 'key' => 'GOOGLE_CLIENT_SECRET', 'value' => ''],
            ['title' => 'Google redirect link for sign in', 'key' => 'GOOGLE_REDIRECT', 'value' => ''],
            ['title' => 'Facebook client id', 'key' => 'FACEBOOK_CLIENT_ID', 'value' => ''],
            ['title' => 'Facebook client secret', 'key' => 'FACEBOOK_CLIENT_SECRET', 'value' => ''],

            ['title' => 'Facebook redirect', 'key' => 'FACEBOOK_REDIRECT', 'value' => ''],
            ['title' => 'Facebook app id (ES)', '' => 'Used for WhatsApp Signup', 'key' => 'FACEBOOK_APP_ID', 'value' => ''],
            ['title' => 'Facebook app secret (ES)', 'key' => 'FACEBOOK_APP_SECRET', 'value' => ''],
            ['title' => 'Facebook config id (ES)', '' => 'Used for WhatsApp Embeded Signup', 'key' => 'FACEBOOK_CONFIG_ID', 'value' => ''],

            ['separator' =>'Static pages', 'title' => 'Privacy Policy', 'key' => 'PRIVACY_POLICY', 'value' => '','ftype' => 'select', 'data' => []],
            ['title' => 'Terms of Service', 'key' => 'TERMS_OF_SERVICE', 'value' => '', 'ftype' => 'select', 'data' => []],
     

            ['separator' => 'Other settings', 'title' => 'Enable Multiple Organizations', 'key' => 'ENABLE_MULTI_ORGANIZATIONS', 'value' => 'true', 'ftype' => 'bool'],
            ['title' => 'Vendor entity name', 'help' => 'Ex. Company, Company, Shop, Business etc', 'key' => 'VENDOR_ENTITY_NAME', 'value' => 'Company'],
            ['title' => 'Vendor entity name in plural', 'help' => 'Ex. Companies, Companies, Shops, Businesses etc', 'key' => 'VENDOR_ENTITY_NAME_PLURAL', 'value' => 'Companies'],

            ['title' => 'Url route for vendor', 'help' => 'If you want to change the link the vendor is open in. ex yourdomain.com/shop/shopname. shop - should be the value here', 'key' => 'URL_ROUTE', 'value' => 'company', 'hideon' => 'wpbox'],
            ['title' => 'Url route for vendor in plural', 'help' => 'If you want to change the link the vendor management is open in. ex yourdomain.com/shops. shops - should be the value here', 'key' => 'URL_ROUTE_PLURAL', 'value' => 'companies', 'hideon' => 'wpbox'],

            ['title' => 'Demo vendor slug',  'help' => 'Enter the domain - slug of your demo vendor that will show on the landing page', 'key' => 'demo_company_slug', 'value' => 'leukapizza', 'onlyin' => 'qrsaas'],
            ['title' => 'Apps download code', 'help' => 'If you have extended license, or some specific product, we will send you App download code. Send us ticket.', 'key' => 'EXTENDED_LICENSE_DOWNLOAD_CODE', 'value' => ''],
            ['title' => 'App environment', 'key' => 'APP_ENV', 'value' => 'local', 'ftype' => 'select', 'data' => ['local' => 'Local', 'prodcution' => 'Production']],
            ['title' => 'Debug app level', 'type' => 'hidden', 'key' => 'APP_LOG_LEVEL', 'value' => 'debug', 'data' => ['debug' => 'Debug', 'error' => 'Error']],
        ],
    ],

    [
        'name' => 'Finances',
        'slug' => 'finances',
        'icon' => 'ni ni-money-coins',
        'fields' => [
            ['separator' => 'General', 'title' => 'Tool used for subscriptions', 'key' => 'SUBSCRIPTION_PROCESSOR', 'value' => 'Stripe', 'ftype' => 'select', 'data' => []],
            ['title' => 'Enable Pricing', 'key' => 'ENABLE_PRICING', 'value' => 'false', 'ftype' => 'bool', 'help' => 'If enabled, users will be able to subscribe to plans. If disabled, users will us the platform without any restrictions.'],
            ['title' => 'Default plan', 'key' => 'FREE_PRICING_ID', 'value' => '1', 'ftype' => 'select', 'data' => [], 'help' => 'This is the plan that will be automatically assigned to new users, or when user subscription is paused or cancelled'],
            ['title' => 'Force users to use paid plan', 'key' => 'FORCE_USERS_TO_PAY', 'value' => 'false', 'ftype' => 'bool', 'help' => 'If enabled, users will be forced to use a paid plan. If disabled, users will be able to use the any plan.'],
            ['separator' => 'Credits System (Beta)', 'title' => 'Enable Credits System', 'key' => 'ENABLE_CREDITS', 'value' => 'true', 'ftype' => 'bool', 'help' => 'If enabled, users will be charged credits for each action. Base on the plan, the credits will be different and given to the user on subscription payment.'],
            ['separator' => 'Stripe', 'title' => 'Stripe API key', 'key' => 'STRIPE_KEY', 'value' => 'pk_test_XXXXXXXXXXXXXX'],
            ['title' => 'Stripe API Secret', 'key' => 'STRIPE_SECRET', 'value' => 'sk_test_XXXXXXXXXXXXXXX'],

            ['separator' => 'Local bank transfer', 'title' => 'Local bank transfer explanation', 'key' => 'LOCAL_TRANSFER_INFO', 'value' => 'Wire us the plan amount on the following bank account. And inform us about the wire.'],
            ['title' => 'Bank Account', 'key' => 'LOCAL_TRANSFER_ACCOUNT', 'value' => 'IBAN: 12112121212121'],

        ],
    ],
    [],
    [
        'name' => 'Apps & Plugins',
        'slug' => 'plugins',
        'icon' => 'ni ni-spaceship',
        'fields' => [

            ['separator' => 'Tools', 'title' => 'Recaptcha secret', 'help' => "Make empty if you can't make submit on register screen", 'key' => 'RECAPTCHA_SECRET_KEY', 'value' => ''],
            ['separator' => 'Pusher live notifications', 'title' => 'Pusher app id', 'help' => 'Pusher is used for live notifications', 'key' => 'PUSHER_APP_ID', 'value' => ''],
            ['title' => 'Pusher app key', 'key' => 'PUSHER_APP_KEY', 'value' => ''],
            ['title' => 'Pusher app secret', 'key' => 'PUSHER_APP_SECRET', 'value' => ''],
            ['title' => 'Pusher app cluster', 'key' => 'PUSHER_APP_CLUSTER', 'value' => 'eu'],
            ['title' => 'Broadcast Driver', 'key' => 'BROADCAST_DRIVER', 'value' => 'log', 'ftype' => 'select', 'data' => ['pusher' => 'Pusher', 'log' => 'Log']],

            ['separator' => 'Share this', 'title' => 'Share this property id', 'help' => 'You can find this number in Share this import link', 'key' => 'SHARE_THIS_PROPERTY', 'value' => ''],
        ],
    ],
    [
        'name' => 'SMTP',
        'slug' => 'smtp',
        'icon' => 'ni ni-email-83',
        'fields' => [
            ['title' => 'Mail driver', 'key' => 'MAIL_MAILER', 'value' => 'smtp', 'ftype' => 'select', 'data' => ['smtp' => 'SMTP', 'sendmail' => 'PHP Sendmail - best of port 465']],
            ['title' => 'Host', 'key' => 'MAIL_HOST', 'value' => 'smtp.mailtrap.io', 'hint' => 'Your SMTP send server'],
            ['title' => 'Port', 'key' => 'MAIL_PORT', 'value' => '2525', 'help' => 'Common ports are 26, 465, 587'],
            ['title' => 'Encryption', 'key' => 'MAIL_ENCRYPTION', 'value' => '', 'ftype' => 'select', 'data' => ['null' => 'Null - best for port 26', '' => 'None - best for port 587', 'ssl' => 'SSL - best for port 465', 'tls' => 'TLS', 'starttls' => 'STARTTLS']],

            ['title' => 'Username', 'key' => 'MAIL_USERNAME', 'value' => '802fc656dd8029'],
            ['title' => 'Password', 'key' => 'MAIL_PASSWORD', 'value' => 'bbcf39d313eac6'],
            ['title' => 'From address', 'key' => 'MAIL_FROM_ADDRESS', 'value' => 'bd5d577b7c-be3ae1@inbox.mailtrap.io'],
            ['title' => 'From Name', 'key' => 'MAIL_FROM_NAME', 'value' => 'Your Site'],

            ['title' => '', 'key' => 'DB_CONNECTION', 'value' => 'mysql', 'data' => ['mysql' => 'MySql'], 'type' => 'hidden'],
            ['title' => '', 'key' => 'DB_HOST', 'value' => '127.0.0.1', 'hint' => 'Your SMTP send server', 'type' => 'hidden'],
            ['title' => '', 'key' => 'DB_PORT', 'value' => '3306', 'type' => 'hidden'],
            ['title' => '', 'key' => 'DB_DATABASE', 'value' => 'laravel', 'type' => 'hidden'],
            ['title' => '', 'key' => 'DB_USERNAME', 'value' => 'laravel', 'type' => 'hidden'],
            ['title' => '', 'key' => 'DB_PASSWORD', 'value' => 'laravel', 'type' => 'hidden'],

            ['title' => '', 'key' => 'CACHE_DRIVER', 'value' => 'file', 'type' => 'hidden'],
            ['title' => '', 'key' => 'SESSION_DRIVER', 'value' => 'file', 'type' => 'hidden'],
            ['title' => '', 'key' => 'REDIS_HOST', 'value' => '127.0.0.1', 'type' => 'hidden'],
            ['title' => '', 'key' => 'REDIS_PASSWORD', 'value' => 'null', 'type' => 'hidden'],
            ['title' => '', 'key' => 'REDIS_PORT', 'value' => '6379', 'type' => 'hidden'],

        ],
    ],
];

return [
    'env' => $env,
];
