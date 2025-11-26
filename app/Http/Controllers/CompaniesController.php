<?php

namespace App\Http\Controllers;

use Akaunting\Module\Facade as Module;
use App\Events\WebNotification;
use App\Exports\VendorsExport;
use App\Models\Company;
use App\Models\Plans;
use App\Traits\Fields;
use App\Traits\Modules;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class CompaniesController extends Controller
{
    use Fields;
    use Modules;

    private $imagePath = '';

    // Add these variables
    protected $title = 'Company';

    protected $titlePlural = 'Companies';

    protected $view_path = 'companies.';

    protected $webroute_path = 'admin.companies.';

    protected $parameter_name = 'company';

    private function getFields($class = 'col-md-4')
    {
        $fields = [];

        //Add name field
        $fields[0] = ['class' => $class, 'ftype' => 'input', 'name' => 'Name', 'id' => 'name', 'placeholder' => 'Enter name', 'required' => true];

        //Return fields
        return $fields;
    }

    private function getFilterFields()
    {
        $fields = $this->getFields('col-md-3');
        $fields[0]['required'] = true;

        return $fields;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->user()->hasRole('admin')) {
            $companies = Company::with('user');
            $categories=[];
            if(Module::has('admincategories')){
                try{
                    $categories = \Modules\Admincategories\Models\SystemCategory::where('type','company')->pluck('name', 'id')->toArray();
                } catch (\Exception $e) {
                    //Do nothing
                }
            }
            $plans = Plans::pluck('name', 'id')->toArray();

            //filter by name
            if (isset($_GET['name']) && strlen($_GET['name']) > 2) {
                $companies->where('name', 'like', '%'.$_GET['name'].'%');
            }

            if (isset($_GET['email']) && strlen($_GET['email']) > 2) {
                $companies->whereHas('user', function ($query) {
                    $query->where('email', 'like', '%'.$_GET['email'].'%');
                });
            }

            if (isset($_GET['category']) && $_GET['category'] != -1) {
                $companies->where('category_id', $_GET['category']);
            }

            try {
                //Filter by plan, plan is on the user table user.plan_id
                if (isset($_GET['plan']) && $_GET['plan'] != -1) {
                    $companies->whereHas('user', function ($query) {
                        $query->where('plan_id', $_GET['plan']);
                    });
                } elseif (isset($_GET['plan']) && $_GET['plan'] == -1) {
                    $companies->whereHas('user', function ($query) {
                        $query->whereNull('plan_id');
                    });
                }
            } catch (\Exception $e) {
                //Do nothing
            }

            //With downloaod
            if (isset($_GET['report'])) {
                $items = [];
                $vendorsToDownload = $companies->orderBy('id', 'desc')->get();
                foreach ($vendorsToDownload as $key => $vendor) {
                    $item = [
                        'company_name' => $vendor->name,
                        'company_id' => $vendor->id,
                        'created' => $vendor->created_at,
                        'owner_name' => $vendor->user->name,
                        'owner_email' => $vendor->user->email,
                    ];
                    array_push($items, $item);
                }

                return Excel::download(new VendorsExport($items), 'vendors_'.time().'.csv', \Maatwebsite\Excel\Excel::CSV);
            }

            $filterFields = $this->getFilterFields();

            //Add the field to look for the categories
            if(Module::has('admincategories')){     
               
                $filterFields[] = ['required' => false, 'class' => 'col-md-3', 'ftype' => 'select', 'name' => 'Category', 'id' => 'category', 'placeholder' => 'Select category', 'data' => $categories];
            }

            //Name is not required
            $filterFields[0]['required'] = false;

            //Add the field to look for the plans
            $filterFields[] = ['required' => false, 'class' => 'col-md-3', 'ftype' => 'select', 'name' => 'Plan', 'id' => 'plan', 'placeholder' => 'Select plan', 'data' => Plans::pluck('name', 'id')->toArray()];
            $filterFields[count($filterFields) - 1]['data'] = [-1 => 'No plan assigned'] + $filterFields[count($filterFields) - 1]['data'];

            //Add the field to look for the email
            $filterFields[] = ['required' => false, 'class' => 'col-md-3', 'ftype' => 'input', 'name' => 'Email', 'id' => 'email', 'placeholder' => 'Enter email', 'data' => []];

            return view($this->view_path.'index', ['setup' => [
                'categories' => $categories,
                'usefilter' => true,
                'title' => __('Companies'),
                'items' => $companies->orderBy('id', 'desc')->paginate(10),
                'item_names' => $this->titlePlural,
                'webroute_path' => $this->webroute_path,
                'fields' => $this->getFields(),
                'filterFields' => $filterFields,
                'custom_table' => true,
                'parameter_name' => $this->parameter_name,
                'parameters' => count($_GET) != 0,
                'action_link' => route('admin.companies.logout-create'),
                'action_name' => 'Create Company',

            ],
                'hasCloner' => Module::has('cloner') && auth()->user()->hasRole(['admin', 'manager']),
                'plans' => $plans,
            ]);
        } else {
            return redirect()->route('dashboard');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        //
    }

    private function verifyAccess($company)
    {
        return auth()->user()->id == $company->user_id || auth()->user()->hasRole('admin');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function edit($company_id)
    {
        $company = Company::findOrFail($company_id);

        //Languages
        $available_languages = [];
        $default_language = null;

        //currency
        if (strlen($company->currency) > 1) {
            $currency = $company->currency;
        } else {
            $currency = config('settings.cashier_currency');
        }

        //App fields = There is app managment now
        //$appFields = $this->convertJSONToFields($this->vendorFields($company->getAllConfigs()));
        $appFields = [];

        if ($this->verifyAccess($company)) {
            return view('companies.edit', [
                'hasCloner' => Module::has('cloner') && auth()->user()->hasRole(['admin', 'manager']),
                'company' => $company,
                'plans' => Plans::get()->pluck('name', 'id'),
                'available_languages' => $available_languages,
                'default_language' => $default_language,
                'currency' => $currency,
                'appFields' => $appFields,
            ]);
        }

        return redirect()->route('dashboard')->withStatus(__('No Access'));
    }

    public function updateApps(Request $request, Company $company): RedirectResponse
    {
        //Update custom fields
        if ($request->has('custom')) {
            $company->setMultipleConfig($request->custom);
        }

        return redirect()->route('admin.companies.edit', $company->id)->withStatus(__('Organization successfully updated.'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Company  $company
     */
    public function update(Request $request, $companyid): RedirectResponse
    {

        $this->imagePath = config('app.images_upload_path');

        $company = Company::findOrFail($companyid);
        $company->name = strip_tags($request->name);
        $thereIsCompanyAddressChange = $company->address.'' != $request->address.'';

        $company->address = strip_tags($request->address);
        $company->phone = strip_tags($request->phone);

        $company->description = strip_tags($request->description);

        //Update subdomain only if rest is not older than 1 day
        if (Carbon::parse($company->created_at)->diffInDays(Carbon::now()) < 2) {
            $company->subdomain = $this->makeAlias(strip_tags($request->name));
        }

        if (auth()->user()->hasRole('admin')) {
            $company->is_featured = $request->is_featured != null ? 1 : 0;
        }

        if(auth()->user()->hasRole('admin') && isset($request->category_id)){
            $company->category_id = intval($request->category_id);
        }

        if ($request->hasFile('company_logo')) {

            $company->logo = $this->saveImageVersions(
                $this->imagePath,
                $request->company_logo,
                [
                    ['name' => 'large', 'w' => 590, 'h' => 400],
                    ['name' => 'medium', 'w' => 295, 'h' => 200],
                    ['name' => 'thumbnail', 'w' => 200, 'h' => 200],
                ]
            );
        }

        if ($request->hasFile('company_cover')) {
            $company->cover = $this->saveImageVersions(
                $this->imagePath,
                $request->company_cover,
                [
                    ['name' => 'cover', 'w' => 2000, 'h' => 1000],
                    ['name' => 'thumbnail', 'w' => 400, 'h' => 200],
                ]
            );
        }

        //Change currency
        $company->currency = $request->currency;

        //Change do converstion
        $company->do_covertion = $request->do_covertion == 'true' ? 1 : 0;

        $company->update();

        //Update custom fields
        if ($request->has('custom')) {
            $company->setMultipleConfig($request->custom);
        }

        return redirect()->route('admin.companies.edit', $company->id)->withStatus(__('Organization successfully updated.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy($companyid): RedirectResponse
    {
        $company = Company::findOrFail($companyid);
        if (! auth()->user()->hasRole('admin') && auth()->user()->id != $company->user_id) {
            abort(403);
        }

        $company->active = 0;
        $company->save();

        return redirect()->route('admin.companies.index')->withStatus(__('Organization successfully deactivated.'));
    }

    public function remove($companyid): RedirectResponse
    {
        if (config('settings.is_demo')) {
            return redirect()->route('admin.companies.index')->withStatus(__('Disabled in demo'));
        }
        $company = Company::findOrFail($companyid);
        if (! auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $company->delete();

        return redirect()->route('admin.companies.index')->withStatus(__('Organization successfully deleted.'));
    }

    private function makeCompanyActive(Company $company)
    {
        //Activate the company
        $company->active = 1;
        $company->subdomain = $this->makeAlias($company->name);
        $company->update();
    }

    public function activateCompany($companyid): RedirectResponse
    {
        $company = Company::findOrFail($companyid);
        $this->makeCompanyActive($company);

        return redirect()->route('admin.companies.index')->withStatus(__('Organization successfully activated.'));
    }

    public function stopImpersonate(): RedirectResponse
    {

        Auth::user()->stopImpersonating();

        Session::forget('company_id');
        Session::forget('company_currency');
        Session::forget('company_convertion');

        return redirect()->route('home');
    }

    public function loginas($companyid): RedirectResponse
    {
        $company = Company::findOrFail($companyid);
        if (config('settings.is_demo', false)) {
            return redirect()->back()->withStatus('Not allowed in demo');
        }
        if ($this->verifyAccess($company)) {
            //Login as owner
            Session::put('impersonate', $company->user->id);

            //Set the company
            session(['company_id' => $company->id]);
            session(['company_currency' => $company->currency]);
            session(['company_convertion' => $company->do_covertion]);

            return redirect()->route('home');
        } else {
            abort(403);
        }
    }

    //Switch company
    public function switch($companyid): RedirectResponse
    {
        $company = Company::findOrFail($companyid);
        if ($this->verifyAccess($company)) {
            //Set the company
            session(['company_id' => $company->id]);
            session(['company_currency' => $company->currency]);
            session(['company_convertion' => $company->do_covertion]);

            return redirect()->route('home');
        } else {
            abort(403);
        }
    }

    public function manage(): View
    {
        return view('companies.manage');
    }

    public function createOrganization(Request $request): RedirectResponse
    {
        $company = Company::create([
            'name' => $request->name,
            'user_id' => auth()->user()->id,
            'subdomain' => strtolower(preg_replace('/[^A-Za-z0-9]/', '', $request->name)),
            'created_at' => now(),
            'updated_at' => now(),
            'logo' => asset('uploads').'/default/no_image.jpg',
        ]);

        return redirect()->route('admin.organizations.manage')->withStatus(__('Organization successfully created.'));
    }

    public function notify($type, $companyid, $message): JsonResponse
    {

        $company = Company::findOrFail($companyid);
        $CAN_USE_PUSHER = strlen(config('broadcasting.connections.pusher.app_id')) > 2 && strlen(config('broadcasting.connections.pusher.key')) > 2 && strlen(config('broadcasting.connections.pusher.secret')) > 2;
        $messageSend = false;
        $responseMessage = '';
        //Check if company has this notification enabled
        if ($company->getConfig('enable_notification_'.$type, true) && $CAN_USE_PUSHER) {
            event(new WebNotification($company, $message, $type));
            $responseMessage = $message;
            $messageSend = true;
        } else {
            $responseMessage = __('Notification not enabled');
            $messageSend = false;
        }

        //Respond in json
        return response()->json([
            'message' => $responseMessage,
            'messageSend' => $messageSend,
        ]);
    }

    public function share(): View
    {
        $url = auth()->user()->company->getLinkAttribute();

        return view('companies.share', ['url' => $url, 'name' => auth()->user()->company->name]);
    }

    public function logoutAndRedirectToRegister(): RedirectResponse
    {
        // Logout the current user
        Auth::logout();

        // Regenerate session to prevent session fixation
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        // Redirect to register route
        return redirect()->route('register');
    }
}
