## Creating new modules

### 1. Project structure
This project is a Laravel 10 project and we are using akaunting/laravel-module to manage modules.
Each module is a separate folder in the `modules` folder, and acts as a separate Laravel application.

### 2. Creating a new module
php artisan module:make <module-name>
php artisan module:make-migration create_<table-name>_table <module-name>
php artisan module:make-model <model-name> <module-name>
php artisan module:make-factory <factory-name> --model=<model-name> <module-name>
php artisan tinker
\Modules\<module-name>\Models\<model-name>::factory()->count(10)->create();

### 3. Structure of a module
Each module follows Laravel conventions with these directories:
- Config/ - Module configuration files
- Console/ - Artisan commands
- Database/
  - Migrations/ - Database migrations
  - Seeds/ - Database seeders
  - Factories/ - Model factories
- Events/ - Event classes
- Http/
  - Controllers/ - HTTP controllers
  - Middleware/ - HTTP middleware
  - Requests/ - Form request classes
- Jobs/ - Queueable jobs
- Listeners/ - Event listeners
- Models/ - Eloquent models
- Notifications/ - Notification classes
- Providers/ - Service providers (Main.php is the main provider)
- Resources/views/ - Blade templates
- Routes/ - Route definitions (api.php, web.php)
- Traits/ - PHP traits
- composer.json - Composer dependencies
- module.json - Module configuration
- package.json - NPM dependencies
- webpack.mix.js - Laravel Mix configuration

### 4. module.json Configuration
The module.json file is the heart of module configuration. Key properties include:

```json
{
    "alias": "modulealias",                    // Unique module identifier
    "version": "1.0",                         // Module version
    "nameSpace": "Modules\\ModuleName",       // PHP namespace
    "description": "",                        // Module description
    "keywords": [],                           // SEO keywords
    "active": 1,                              // Is module active
    "order": 0,                               // Load order
    "providers": [                            // Service providers to register
        "Modules\\ModuleName\\Providers\\Main"
    ],
    "aliases": {},                            // Class aliases
    "files": [],                              // Files to autoload
    "requires": [],                           // Module dependencies
    
    // UI Integration
    "hasSidebar": true,                       // Has sidebar apps
    "hasDashboardInfo": true,                 // Shows on dashboard
    "alwayson": true,                         // Always active
    "beforeMainMenus": true,                  // Menu position
    
    // Sidebar Apps (if hasSidebar is true)
    "sidebarData": [
        {
            "name": "App Name",
            "app": "appkey",
            "icon": "https://example.com/icon.svg",
            "brandColor": "#8966FE",
            "view": "modulealias::chat.sideapp.app",
            "script": "modulealias::chat.sideapp.script"
        }
    ],
    
    // Cost Configuration
    "cost_per_action": [
        {
            "name": "Action Name",
            "action": "action_key",
            "cost": 1,
            "default_cost": 1
        }
    ],
    
    // Configuration Fields
    "global_fields": [                        // System-wide settings
        {
            "separator": "Section Name",
            "title": "Setting Title",
            "key": "SETTING_KEY",
            "help": "Help text",
            "ftype": "input",                 // input, bool, select, textarea
            "value": "default_value",
            "data": {}                        // For select options
        }
    ],
    
    "vendor_fields": [                        // Company-specific settings
        {
            "separator": "Section Name",
            "title": "Setting Title",
            "key": "vendor_setting_key",
            "ftype": "input",
            "icon": "🔗",
            "value": ""
        }
    ],
    
    // Menu Configuration
    "staffmenus": [                           // Menus for staff users
        {
            "name": "Menu Item",
            "icon": "ni ni-icon-name text-color",
            "route": "module.route.name",
            "color": "#2dce89",
            "priority": 1,
            "onlyin": "modulealias",          // Show only in this module
            "isGroup": true,                  // Has submenu
            "menus": []                       // Submenu items
        }
    ],
    
    "ownermenus": [                           // Menus for owner users
        // Same structure as staffmenus
    ],
    
    // Additional configurations
    "actions_cost_config": {
        "action_name": 1
    },
    
    // Feature flags
    "isLinkFetcher": false,                   // Module fetches links
    "hasFlows": false                         // Module has workflow support
}
```

### 5. Service Provider (Main.php)
The main service provider handles:
- Configuration loading
- View registration
- Translation loading
- Route registration
- Migration loading
- View component registration

### 6. Model Guidelines
- Models should extend appropriate base classes
- Use CompanyScope for multi-tenant data isolation
- Define relationships properly
- Use Model::booted() for global scopes
- Example:
```php
namespace Modules\ModuleName\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\CompanyScope;

class ModelName extends Model
{
    protected $table = 'table_name';
    protected $fillable = ['field1', 'field2'];
    
    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);
    }
    
    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
}
```

### 7. Controller Guidelines
- Controllers should extend App\Http\Controllers\Controller
- Use traits for common functionality (e.g., Whatsapp trait)
- Implement proper authorization checks
- Follow RESTful conventions

### 8. Routes
- Define routes in Routes/web.php or Routes/api.php
- Use route names with module prefix: 'modulename.controller.action'
- Group routes with middleware and prefixes

### 9. Views
- Place views in Resources/views/
- Reference views as: 'modulealias::folder.viewname'
- Use Blade templating
- Extend appropriate layouts

### 10. Database Migrations
- Create migrations with: php artisan module:make-migration
- Follow Laravel migration conventions
- Include proper up() and down() methods
- Handle exceptions for existing tables/columns
