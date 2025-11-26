<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Akaunting\Module\Facade as Module;
use App\Models\Config;

class FrontEndController extends Controller
{
    public function register(): RedirectResponse
    {
        return redirect()->route('register');
    }

    public function index()
    {

        //1. Subdomain mode
        if ($this->getSubDomain()) {
            return $this->subdomainMode();
        }

        //1a. Custom domain mode
        $customDomain = $this->customDomainMode();
        if ($customDomain != '') {
            return $this->company($customDomain);
        }

        //2. Landing page
        //Check if landing is disabled
        if (config('settings.disable_landing_page', false)) {
            return redirect()->route('home');
        }

        //Try catch by the env settings

        try {
            $landingClassToUse = config('settings.landing_page');
            if (strpos($landingClassToUse, '\\') === false) {
                $landingClassToUse =  "Modules\\".config('settings.landing_page')."\\Http\\Controllers\\DashboardController";
            }else{
                $landingClassToUse = config('settings.landing_page');
            }

            $response = (new $landingClassToUse())->landing();
            return $response;
        } catch (\Exception $e) {
            //Continue with the default landing page
        }

        if(config('settings.app_code_name', '') == 'wpbox'){
            return (new \Modules\Wpbox\Http\Controllers\DashboardController())->landing();
        }  
        
        if(config('settings.app_code_name', '') == 'loyalty'){
            return (new \Modules\Cards\Http\Controllers\Main())->landing();
        }

      
        


       
    }

    /**
     * 2. Subdomain mode - directly show store.
     */
    public function subdomainMode()
    {
        $subDomain = $this->getSubDomain();
        if ($subDomain) {
            $company = Company::whereRaw('REPLACE(subdomain, "-", "") = ?', [str_replace('-', '', $subDomain)])->get();
            if (count($company) != 1) {
                //When Subdomain mode is disabled, show the error
                if (! config('settings.wildcard_domain_ready')) {
                    return view('companies.alertdomain', ['subdomain' => $subDomain]);
                } else {
                    abort(404);
                }

            }

            return $this->company($subDomain);
        }
    }

    /**
     * Gets subdomain.
     */
    public function getSubDomain()
    {
        return false; //At the moment we are not using subdomains
        $subdomain = substr_count(str_replace('www.', '', $_SERVER['HTTP_HOST']), '.') > 1 ? substr(str_replace('www.', '', $_SERVER['HTTP_HOST']), 0, strpos(str_replace('www.', '', $_SERVER['HTTP_HOST']), '.')) : '';
        if ($subdomain == '' | in_array($subdomain, config('settings.ignore_subdomains'))) {
            return false;
        }

        return $subdomain;
    }

    private function customDomainMode()
    {
        //1 - Make sure the module is installed
        if (! in_array('domain', config('global.modules', []))) {
            return '';
        }

        //2 - Extract the domain
        $domain = request()->getHost();

        //3 - Make sure, this is no the project domain itself,
        if (strpos(config('app.url'), $domain) !== false) {
            return '';
        }

        //4 - The extracted domain is in the list of custom values
        $theConfig = Config::where('value', 'like', '%'.$domain.'%')->first();

        //Make sure that is not th main domain
        if (strpos(config('app.url'), $domain) !== false) {
            return '';
        }

        if ($theConfig) {
            //5 - Return the company subdomain if company is active
            $vendor_id = $theConfig->model_id;

            $vendor = Company::where('id', $vendor_id)->first();
            if ($vendor) {
                return $vendor->subdomain;
            } else {
                return '';
            }

        } else {
            //By default return no domain
            return '';
        }
    }

    public function company($subdomain)
    {
        // Company page
        $pageClassToUse = config('settings.company_page');

        if (strpos($pageClassToUse, '\\') === false) {
            $pageClassToUse =  "Modules\\".config('settings.company_page')."\\Http\\Controllers\\Main";
        }else{
            $pageClassToUse = config('settings.company_page');
        }

        return (new $pageClassToUse())->companyLanding(Company::where('subdomain', $subdomain)->firstOrFail());
    }

    /**
     * Static page
     */
    public function staticPage($alias)  
    {
        //Check if vendor by this alias exists
        $company = Company::where('subdomain', $alias)->first();
        if ($company) {
            return $this->company($company->subdomain);
        }

        if (Module::has('blog')) {
            $staticPage = \Modules\Blog\Models\Blog::where('post_type', 'page')->where('slug', $alias)->first();
            return view('general.page', ['staticPage' => $staticPage]);
        }else{
            abort(404,'Blog module is not installed');
        }
    }
}
