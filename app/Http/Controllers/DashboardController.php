<?php

namespace App\Http\Controllers;

use Akaunting\Module\Facade as Module;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;

class DashboardController extends Controller
{
    public function dashboard($lang = null)
    {

        //Check if there is special admin for owners
        if (config('settings.ownerAdmin', 'default') != 'default' && auth()->user()->hasRole('owner')) {
            return redirect()->route(config('settings.ownerAdmin'));
        }

        $locale = Cookie::get('lang') ? Cookie::get('lang') : config('settings.app_locale');
        if ($lang != null) {
            //this is language route
            $locale = $lang;

        }
        if ($locale != 'android-chrome-256x256.png') {
            App::setLocale(strtolower($locale));
            session(['applocale_change' => strtolower($locale)]);
        }

        $dataToDisplay = [
            'locale' => $locale,
        ];

        foreach (config('global.modulesWithDashboardInfo') as $moduleWithDashboardInfo) {
            $generatedClass = Module::get($moduleWithDashboardInfo)->get('nameSpace')."\Http\Controllers\DashboardController";
            $dataFromModule = (new $generatedClass())->index();
            if ($dataFromModule != null) {
                if ($dataFromModule instanceof RedirectResponse) {

                    return $dataFromModule;
                }
                $dataToDisplay[$moduleWithDashboardInfo] = $dataFromModule;
            }

        }

        //The current logged in user
        $currentUser = auth()->user();

        //Finish tasks
        if (isset($_GET['task_done'])) {
            $currentUser->setConfig('task_done_'.$_GET['task_done'], true);
        }

        //Get the task to be done for admins
        $taskToBeDone = [];

        if ($currentUser->hasRole('admin')) {
            for ($i = 1; $i < 7; $i++) {
                if (config('settings.task_'.$i, null)) {
                    if (! $currentUser->getConfig('task_done_'.$i, false)) {

                        array_push($taskToBeDone, [
                            'task' => config('settings.task_'.$i, ''),
                            'id' => $i,
                            'task_docs' => config('settings.task_'.$i.'_docs', ''),
                            'task_info' => str_replace('{url}', config('app.url'), config('settings.task_'.$i.'_info', '')),
                        ]);
                    }
                }
            }
        }else{
            //Check if current user company is active
            if (isset($currentUser->company)&&$currentUser->company->active == 0) {
                //Logout and redirect to home page
                auth()->logout();
                return redirect()->route('home')->withError(__('Your account is not active. Please contact the administrator.'));
            }
        }
        $dataToDisplay['tasks'] = $taskToBeDone;

       // dd($dataToDisplay);

        $response = new \Illuminate\Http\Response(view('dashboard::index', $dataToDisplay));
        $response->withCookie(cookie('lang', $locale, 120));
        App::setLocale(strtolower($locale));

        return $response;

    }
}
