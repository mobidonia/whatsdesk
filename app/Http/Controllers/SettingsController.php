<?php

namespace App\Http\Controllers;

use Akaunting\Module\Facade as Module;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Image;

class SettingsController extends Controller
{
    protected static $currencies;

    protected static $jsfront;

    protected $imagePath = '/uploads/settings/';

    /**
     * Validates if the current user has admin access.
     * Aborts with a 404 error if the user is not an admin.
     */
    private function validateAccess()
    {
        if (! auth()->user()->hasRole('admin')) {
            abort(404);
        }
    }

    /**
     * Loads and sets the current environment
     */
    /**
     * Loads and sets the current environment configurations.
     * Merges extra fields from included modules.
     *
     * @return array Merged environment configurations.
     */
    public function getCurrentEnv()
    {

        
        if (Module::has('blog')) {
            //Get the static pages
            $staticPages = \Modules\Blog\Models\Blog::where('post_type', 'page')->get();
            $staticPagesData = [];
            foreach ($staticPages as $page) {
                $staticPagesData[$page->slug] = $page->title;
            }

            try {
                    config(['config.env.0.fields.21.data' => $staticPagesData]);
                    config(['config.env.0.fields.22.data' => $staticPagesData]);
                    //dd( config('config.env.0.fields.21'));
            } catch (\Exception $e) {
                //throw $th;
            }
        }

        $envConfigs = config('config.env');

       

        //Extra fields from included modules
        $extraFields = [];
        foreach (Module::all() as $key => $module) {
            if ($module->get('global_fields')) {
                $extraFields = array_merge($extraFields, $module->get('global_fields'));
            }

        }
        $envConfigs['3']['fields'] = array_merge($extraFields, $envConfigs['3']['fields']);

        //Since 2.2.x there is custom modules
        $envMerged = [];
        foreach ($envConfigs as $key => $group) {
            $theMegedGroupFields = [];
            foreach ($group['fields'] as $key => $field) {
                if (! (isset($field['onlyin']) && str_contains( $field['onlyin'],config('settings.app_project_type')))) {

                    $shouldBeAdded = true;

                    //Hide on specific env config
                    if (isset($field['hideon'])) {
                        $hideOn = explode(',', $field['hideon']);
                        foreach ($hideOn as $hideSpecific) {
                            if (config('settings.app_code_name', '') == $hideSpecific) {
                                $shouldBeAdded = false;
                            }
                        }
                    }
                    if ($shouldBeAdded) {
                        $value = env($field['key'], $field['value']);
                        if($field['key'] == 'DB_PASSWORD'){
                            $value = config('database.connections.mysql.password');
                        }
                        if($field['key'] == 'DB_DATABASE'){
                            $value = config('database.connections.mysql.database');
                        }
                        if($field['key'] == 'DB_USERNAME'){
                            $value = config('database.connections.mysql.username');
                        }
                        if($field['key'] == 'DB_HOST'){ 
                            $value = config('database.connections.mysql.host');
                        }
                        if($field['key'] == 'DB_PORT'){
                            $value = config('database.connections.mysql.port');
                        }
                        if($field['key'] == 'DB_URL'){
                            $value = config('database.connections.mysql.url');
                        }
                        array_push($theMegedGroupFields, [
                            'ftype' => isset($field['ftype']) ? $field['ftype'] : 'input',
                            'type' => isset($field['type']) ? $field['type'] : 'text',
                            'id' => 'env['.$field['key'].']',
                            'name' => isset($field['title']) && $field['title'] != '' ? $field['title'] : $field['key'],
                            'placeholder' => isset($field['placeholder']) ? $field['placeholder'] : '',
                            'value' => $value,
                            'required' => false,
                            'separator' => isset($field['separator']) ? $field['separator'] : null,
                            'additionalInfo' => isset($field['help']) ? $field['help'] : null,
                            'data' => isset($field['data']) ? $field['data'] : [],
                        ]);
                    }

                   
                }
            }
            array_push($envMerged, [
                'name' => $group['name'],
                'slug' => $group['slug'],
                'icon' => $group['icon'],
                'fields' => $theMegedGroupFields,
            ]);
        }

        // Apply database settings from env_settings table
        try {
            $envDbSettings = DB::table('env_settings')->get();
            
            foreach ($envDbSettings as $setting) {
                // Find and update the corresponding field in envMerged
                foreach ($envMerged as $groupIndex => $group) {
                    foreach ($group['fields'] as $fieldIndex => $field) {
                        // Extract the key from the field id (env[KEY_NAME])
                        if (preg_match('/env\[(.+)\]/', $field['id'], $matches)) {
                            $fieldKey = $matches[1];
                            if ($fieldKey === $setting->key) {
                                $envMerged[$groupIndex]['fields'][$fieldIndex]['value'] = $setting->value;
                                break 2; // Break out of both loops once found
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // If table doesn't exist or any other error, continue without database settings
            // This ensures the function works even if the migration hasn't been run yet
        }

        return $envMerged;
    }

    /**
     * Show the settings edit screen
     */
    /**
     * Shows the settings edit screen.
     * Performs migrations and loads settings and files for the view.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        try{
            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('module:migrate', ['--force' => true]);
        }catch(\Exception $e){
            Log::error('Error migrating: '.$e->getMessage());
        }

        if (auth()->user()->hasRole('admin')) {

            $curreciesArr = [];
            static::$currencies = require __DIR__.'/../../../config/money.php';

            foreach (static::$currencies as $key => $value) {
                array_push($curreciesArr, $key);
            }

            $jsfront = '';
            $jsback = '';
            $cssfront = '';
            $cssback = '';
            try {
                $jsfront = File::get(base_path('public/byadmin/front.js'));

                $jsback = File::get(base_path('public/byadmin/back.js'));
                $cssfront = File::get(base_path('public/byadmin/front.css'));

                $cssback = File::get(base_path('public/byadmin/back.css'));
            } catch (\Throwable $th) {
                //throw $th;
            }

            return view('settings.index', [
                'currencies' => $curreciesArr,
                'jsfront' => $jsfront,
                'jsback' => $jsback,
                'cssfront' => $cssfront,
                'cssback' => $cssback,
                'envConfigs' => $this->getCurrentEnv(),
            ]);
        } else {
            return redirect()->route('dashboard')->withStatus(__('No Access'));
        }
    }

    /**
     * Updates environment variables in the .env file.
     *
     * @param  array  $values  Key-value pairs of environment variables to update.
     * @return bool True if the update was successful, false otherwise.
     */
    public function setEnvironmentValue(array $values)
    {

        //Log::info('setEnvironmentValue', ['values' => $values]);

        $envFile = app()->environmentFilePath();
        $str = "\n";
        $str .= file_get_contents($envFile);
        $str .= "\n"; // In case the searched variable is in the last line without \n
        if (count($values) > 0) {
            foreach ($values as $envKey => $envValue) {
                if ($envValue == trim($envValue) && strpos($envValue, ' ') !== false) {
                    $envValue = '"'.$envValue.'"';
                }

                $keyPosition = strpos($str, "{$envKey}=");
                $endOfLinePosition = strpos($str, "\n", $keyPosition);
                $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);

                // If key does not exist, add it
                if ((! $keyPosition && $keyPosition != 0) || ! $endOfLinePosition || ! $oldLine) {
                    $str .= "{$envKey}={$envValue}\n";
                } else {
                    if ($envKey == 'DB_PASSWORD') {
                        $str = str_replace($oldLine, "{$envKey}=\"{$envValue}\"", $str);
                    } else {
                        $str = str_replace($oldLine, "{$envKey}={$envValue}", $str);
                    }

                }
            }
        }

        $str = substr($str, 1, -1);
        if (! file_put_contents($envFile, $str)) {
            return false;
        }

        //Save the env settings to the database
        $this->saveEnvSettings($str);

        return true;
    }

    /**
     * Save the env settings to the database
     */
    public function saveEnvSettings(string $str)
    {
        //Delete all the env settings from the database, use raw query
        DB::table('env_settings')->delete();

        //Parse lines and prepare data for batch insert
        $lines = explode("\n", $str);
        $dataToInsert = [];
        $usedKeys = [];

        foreach ($lines as $line) {
            $line = trim($line);

            // Skip empty lines and comments
            if (empty($line) || str_starts_with($line, '#')) {
                continue;
            }

            // Check if line contains '=' and split properly
            if (str_contains($line, '=')) {
                $parts = explode('=', $line, 2); // Limit to 2 parts in case value contains '='
                $key = trim($parts[0]);
                $value = isset($parts[1]) ? trim($parts[1]) : '';

                // Remove quotes from value if present
                if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                    (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                    $value = substr($value, 1, -1);
                }

                if (! empty($key) && !in_array($key, $usedKeys)) {
                    $usedKeys[] = $key;
                    $dataToInsert[] = [
                        'key' => $key,
                        'value' => $value
                    ];
                }
            }
        }

        // Perform batch insert if we have data
        if (! empty($dataToInsert)) {
            DB::table('env_settings')->insert($dataToInsert);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * Updates the specified resource in storage.
     * Handles file uploads and updates environment values.
     *
     * @param  \Illuminate\Http\Request  $request  The request object containing input data.
     * @param  int  $id  The ID of the resource to update.
     * @return \Illuminate\Http\RedirectResponse Redirects to the settings index with a status message.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        if (config('settings.is_demo')) {
            //Demo, don;t allow
            return redirect()->route('admin.settings.index')->withStatus(__('Settings not allowed to be updated in DEMO mode!'));
        }

        if ($request->hasFile('site_logo')) {
            $LOGO_URL = $this->saveImageVersions(
                $this->imagePath,
                $request->site_logo,
                [
                    ['name' => 'logo', 'type' => 'png'],
                ],
                true
            );
            Log::info('LOGO_URL', ['LOGO_URL' => $LOGO_URL]);
            $envs = $request->env;
            if(str_contains($LOGO_URL, 'http')){
                $envs['LOGO_URL'] = $LOGO_URL;
            }else{
                $envs['LOGO_URL'] = $this->imagePath.''.$LOGO_URL.'_logo.jpg';
            }
            $request->merge(['env' => $envs]);
        }

        $this->setEnvironmentValue($request->env);
       

        //Update the custom js and css files created by admin
        fwrite(fopen(__DIR__.'/../../../public/byadmin/front.js', 'w'), str_replace('tagscript', 'script', $request->jsfront));
        fwrite(fopen(__DIR__.'/../../../public/byadmin/back.js', 'w'), str_replace('tagscript', 'script', $request->jsback));
        fwrite(fopen(__DIR__.'/../../../public/byadmin/front.css', 'w'), str_replace('tagscript', 'script', $request->cssfront));
        fwrite(fopen(__DIR__.'/../../../public/byadmin/back.css', 'w'), str_replace('tagscript', 'script', $request->cssback));
        fwrite(fopen(__DIR__.'/../../../public/byadmin/frontmenu.js', 'w'), str_replace('tagscript', 'script', $request->jsfrontmenu));
        fwrite(fopen(__DIR__.'/../../../public/byadmin/frontcss.css', 'w'), str_replace('tagscript', 'script', $request->cssfrontmenu));

        if ($request->hasFile('favicons')) {
            $imAC256 = Image::make($request->favicons->getRealPath())->fit(512, 512);
            $imgAC192 = Image::make($request->favicons->getRealPath())->fit(192, 192);
            $imgMS150 = Image::make($request->favicons->getRealPath())->fit(150, 150);

            $imgApple = Image::make($request->favicons->getRealPath())->fit(120, 120);
            $img32 = Image::make($request->favicons->getRealPath())->fit(32, 32);
            $img16 = Image::make($request->favicons->getRealPath())->fit(16, 16);

            $imAC256->save(public_path().'/android-chrome-512x512.png');
            $imgAC192->save(public_path().'/android-chrome-192x192.png');
            $imgMS150->save(public_path().'/mstile-150x150.png');

            $imgApple->save(public_path().'/apple-touch-icon.png');
            $img32->save(public_path().'/favicon-32x32.png');
            $img16->save(public_path().'/favicon-16x16.png');

        }

        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Cache::flush();

        return redirect()->route('admin.settings.index')->withStatus(__('Settings successfully updated!'));
    }

    /**
     * Displays the landing page with language settings and sections.
     *
     * @return \Illuminate\View\View The view for the landing page.
     */
    public function landing(): View
    {

        $locale = Cookie::get('lang') ? Cookie::get('lang') : config('settings.app_locale');
        if (isset($_GET['lang'])) {
            //3. Change locale to the new local
            app()->setLocale($_GET['lang']);
            $locale = $_GET['lang'];
            session(['applocale_change' => $_GET['lang']]);
        }

        $this->validateAccess();

        $availableLanguagesENV = config('settings.front_languages');
        $exploded = explode(',', $availableLanguagesENV);
        $availableLanguages = [];
        for ($i = 0; $i < count($exploded); $i += 2) {
            $availableLanguages[$exploded[$i]] = $exploded[$i + 1];
        }

        $sections = [];
        $landiingPageFunctions = explode(',', config('settings.landing_page_functions'));
        $landingPageTitles = explode(',', config('settings.landing_page_titles'));
        foreach ($landiingPageFunctions as $key => $value) {
            $sections[$landingPageTitles[$key]] = $value;
        }
        // $sections = ["Features"=>"feature", "Testimonials"=>"testimonial", "Processes"=>"process","FAQs"=>"faq","Blog links"=>"blog"];

        $currentEnvLanguage = isset(config('config.env')[2]['fields'][0]['data'][config('app.locale')]) ? config('config.env')[2]['fields'][0]['data'][config('app.locale')] : 'UNKNOWN';

        return view('settings.landing.index', [
            'sections' => $sections,
            'locale' => $locale,
            'availableLanguages' => $availableLanguages,
            'currentLanguage' => $currentEnvLanguage,
        ]);
    }

    //Validate
    /**
     * Validates an activation token and logs the activation data.
     * Redirects to the login page.
     *
     * @param  string  $token  The activation token to validate.
     * @return \Illuminate\Http\RedirectResponse Redirects to the login route.
     */
    public function activation($token)
    {
        $data = [
            'url' => config('app.url'),
            'date' => date('Y/m/d h:i:s'),
            'token' => $token,
        ];
        file_put_contents(storage_path('activation'), json_encode($data, JSON_THROW_ON_ERROR), FILE_APPEND | LOCK_EX);

        //Redirect to the login page
        return redirect()->route('login');
    }
}
