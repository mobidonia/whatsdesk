<?php

namespace App\Http\Controllers;

use Akaunting\Module\Facade as Module;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use ZipArchive;
use Illuminate\Support\Str;
use App\Traits\Fields;
use App\Traits\Modules;

class AppsController extends Controller
{
    use Fields;
    use Modules;


    public function companyApps(): View
    {

        $company = $this->getCompany();

        //App fields
        $rawFields = $this->vendorFields($company->getAllConfigs());
        

        $appFields = $this->convertJSONToFields($rawFields);

        $vendorModules = [];
        foreach (Module::all() as $key => $module) {
            if ($module->get('isVendorModule')) {
                array_push($vendorModules, $module->get('alias'));
            }
        }

        $separators = [];
        $icons = [];
        try {
            foreach ($appFields as $key => $field) {
                if ($field['separator']) {
                    $snake = Str::snake($field['separator']);
                    if (isset($field['icon'])) {
                        $icon = $field['icon'];
                    } elseif (isset($icons[$snake])) {
                        $icon = $icons[$snake];
                    } else {
                        $icon = '⚙️';
                    }
                    $field['snake'] = $snake;
                    //Check if separators is empty array

                    array_push($separators, ['icon' => $icon, 'name' => $field['separator'], 'snake' => $snake]);
                    $separators[count($separators) - 1]['fields'][] = $field;

                } else {
                    //Get the last separator
                    $snake = $separators[count($separators) - 1]['snake'];
                    $field['snake'] = $snake;
                    $separators[count($separators) - 1]['fields'][] = $field;
                }
            }
        } catch (\Throwable $th) {
            if (config('app.debug')) {
                dd($th);
            }
        }

        return view('apps.company', [
            'company' => $company,
            'separators' => $separators,
        ]);
    }

    public function updateApps(Request $request): RedirectResponse
    {
        //Update custom fields
        if ($request->has('custom')) {
            $this->getCompany()->setMultipleConfig($request->custom);
        }

        return redirect()->route('admin.apps.company')->withStatus(__('Company app settings successfully updated.'));
    }

    public function index(): View
    {

        $this->adminOnly();

        //1. Get all available apps
        $appsLink = config('settings.apps_link','https://gist.githubusercontent.com/dimovdaniel/b1621923f8bb30327a6a53a7d6562216/raw/apps.json');

        $installed = [];
        foreach (Module::all() as $key => $module) {
            array_push($installed, $module->alias);

        }
        $installedAsString = implode(',', $installed);
        //Code
        $response = (new \GuzzleHttp\Client())->get($appsLink);

        $rawApps = [];
        if ($response->getStatusCode() == 200) {
            $rawApps = json_decode($response->getBody());
        }

        //2. Merge info
        foreach ($rawApps as $key => &$app) {
            
            $app->installed = Module::has($app->alias);
           
            if ($app->installed) {
                $app->version = Module::get($app->alias)->get('version');
               
                if ($app->version == '') {
                    $app->version = '1.0';
                }
              

                //Check if app needs update
                if ($app->latestVersion) {
                    $app->updateAvailable = $app->latestVersion != $app->version.'';
                } else {
                    $app->updateAvailable = false;
                }

            }
            if (! isset($app->category)) {
                $app->category = ['tools'];
            }
        }

        

        //Filter apps by type
        $apps = [];
        $newRawApps = unserialize(serialize($rawApps));
        foreach ($newRawApps as $key => $app) {
            if (isset($app->rule) && $app->rule) {
                $rules = explode(',', $app->rule);
                $alreadyAdded = false;
                foreach ($rules as $keyrule => $rule) {
                    if (! $alreadyAdded && config('settings.app_code_name', '') == $rule) {
                        $alreadyAdded = true;
                        array_push($apps, $app);
                    }
                }
            } else {
                $alreadyAdded = true;
                array_push($apps, $app);
            }

            //remove
            if ($alreadyAdded && isset($app->rulenot) && $app->rulenot) {
                $alreadyRemoved = false;
                $rulesNot = explode(',', $app->rulenot);
                foreach ($rulesNot as $keyrulnot => $rulenot) {
                    if (! $alreadyRemoved && config('app.'.$rulenot)) {
                        $alreadyRemoved = true;
                        array_pop($apps);
                    }
                }
            }
        }
        //3. Return view
        return view('apps.index', compact('apps'));

    }

    public function remove($alias): RedirectResponse
    {
        if (! auth()->user()->hasRole('admin') || strlen($alias) < 2 || (config('settings.is_demo') || config('settings.is_demo'))) {
            abort(404);
        }
        $destination = Module::get($alias)->getPath();
        if (File::exists($destination)) {
            File::deleteDirectory($destination);

            return redirect()->route('apps.index')->withStatus(__('Removed'));
        } else {
            abort(404);
        }
    }
   

    public function store(Request $request): RedirectResponse
    {
        $this->adminOnly(); 
        if ($request->has('file_url')) {
            
            // Get the file content from URL
            $fileContent = file_get_contents($request->file_url);

            // Store the file content to storage/app/appupload
            $fullPath = storage_path('app/appupload/'.basename($request->file_url));
            
            file_put_contents($fullPath, $fileContent);
        }else{
            $path = $request->appupload->storeAs('appupload', $request->appupload->getClientOriginalName());
            $fullPath = storage_path('app/'.$path);
        }

        

       
        $zip = new ZipArchive;

        if ($zip->open($fullPath)) {

            //Modules folder - for plugins
            $destination = public_path('../modules');
            $message = __('App is installed');

            //If it is language pack
            if (strpos($fullPath, '_lang') !== false) {
                $destination = public_path('../lang');
                $message = __('Language pack is installed');
            }else if(strpos($fullPath, '_update') !== false){
                $destination = public_path('../');
                $message = __('Update is installed. Please go to settings.');
            }

            // Extract file
            $zip->extractTo($destination);

            // Close ZipArchive
            $zip->close();

            return redirect()->route('admin.apps.index')->withStatus($message);
        } else {
            return redirect(route('admin.apps.index'))->withError(__('There was an error on app install. Please try manual install'));
        }
    }
}
