<?php

namespace App\Http\Controllers;

use Akaunting\Module\Facade as Module;
use App\Models\Plans;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Laravel\Cashier\Exceptions\PaymentActionRequired;
use Laravel\Cashier\Exceptions\PaymentFailure;
use Stripe\Exception\InvalidRequestException;

class PlansController extends Controller
{
    public function current(): View
    {

        //The curent plan -- access for owner only
        if (! auth()->user()->hasRole('owner')) {
            abort(403, 'Unauthorized action.');
        }

        $theSelectedProcessor = strtolower(config('settings.subscription_processor', 'stripe'));

        $hasPricing=Module::has('pricing');
        if (
            ! ($theSelectedProcessor == 'stripe' || $theSelectedProcessor == 'local') &&
            auth()->user()->plan_status != 'set_by_admin' &&
            $hasPricing &&
            ! config('settings.is_demo') &&
            config('app.url') != 'http://localhost') {
            $className = '\Modules\\'.ucfirst($theSelectedProcessor).'Subscribe\Http\Controllers\App';
            $ref = new \ReflectionClass($className);
            $ref->newInstanceArgs()->validate(auth()->user());
        }

        $plans = config('settings.forceUserToPay', false) ? Plans::where('id', '!=', intval(config('settings.free_pricing_id')))->get()->toArray() : Plans::get()->toArray();

        $colCounter = [4, 12, 6, 4, 3, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4];

        $currentUserPlan = Plans::withTrashed()->find(auth()->user()->mplanid());
        $planAttribute = auth()->user()->company->getPlanAttribute();

        $data = [
            'col' => $colCounter[count($plans)],
            'plans' => $plans,
            'currentPlan' => $currentUserPlan,
            'planAttribute' => $planAttribute,
        ];

        if (
            $theSelectedProcessor == 'stripe'
            && config('app.url') != 'http://localhost'
            && ! config('settings.is_demo')) {
            $data['intent'] = auth()->user()->createSetupIntent();
        }

        $data['subscription_processor'] = $theSelectedProcessor;

        return view('plans.current', $data);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Plans $plans): View
    {
        $this->adminOnly();
        if (! $this->isExtended()) {
            return view('plans.extended');
        }

        return view('plans.index', ['plans' => $plans->paginate(10)]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->adminOnly();
        $theSelectedProcessor = strtolower(config('settings.subscription_processor', 'stripe'));

        return view('plans.create', [
            'allplugins' => $this->getAllPlugins(),
            'theSelectedProcessor' => $theSelectedProcessor]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->adminOnly();
        //Validate request
        $rules = [
            'name' => ['required'],
            'price' => ['numeric', 'required'],
            'description' => ['required'],
            'features' => ['required'],
            'stripe_id' => ['sometimes'],
            //'limit_items'=>['numeric','required'],
            //'limit_orders'=>['numeric','required']
        ];

        $request->validate($rules);

        $plan = new Plans;
        $plan->name = strip_tags($request->name);
        $plan->price = strip_tags($request->price);
        $plan->limit_items = strip_tags($request->limit_items);
        $plan->limit_views = strip_tags($request->limit_views);

        if (isset($request->subscribe)) {
            foreach ($request->subscribe as $key => $value) {
                $plan->$key = strip_tags($value);
            }
        }

        if (config('settings.enable_credits')) {
            $plan->credit_amount = strip_tags($request->credit_amount);
        }

        $plan->description = $request->description;
        $plan->features = $request->features;

        $plan->period = $request->period == 'monthly' ? 1 : 2;

        $plan->save();

        $this->updatePlanPlugins($plan, $request->pluginsSelector);

        return redirect()->route('plans.index')->withStatus(__('Plan successfully created!'));
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

    private function getAllPlugins()
    {
        $plugins = [];
        foreach (Module::all() as $key => $module) {
            if (is_array($module->get('vendor_fields'))) {
                array_push($plugins, $module);
            }

        }

        return $plugins;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     */
    public function edit(Plans $plan): View
    {
        $this->adminOnly();
        $theSelectedProcessor = strtolower(config('settings.subscription_processor', 'Stripe'));

        return view('plans.edit',
            [
                'allplugins' => $this->getAllPlugins(),
                'plan' => $plan,
                'theSelectedProcessor' => $theSelectedProcessor,
            ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     */
    public function update(Request $request, Plans $plan): RedirectResponse
    {
        $this->adminOnly();
        $plan->name = strip_tags($request->name);
        $plan->price = strip_tags($request->price);
        $plan->limit_items = strip_tags($request->limit_items);
        $plan->limit_views = strip_tags($request->limit_views);

        //Subscriptions plans
        if (isset($request->subscribe)) {
            foreach ($request->subscribe as $key => $value) {
                $plan->$key = strip_tags($value);
            }
        }

        //Default stripe
        if (isset($request->stripe_id)) {
            $plan->stripe_id = $request->stripe_id;
        }

        if (config('settings.enable_credits')) {
            $plan->credit_amount = strip_tags($request->credit_amount);
        }

        $plan->period = $request->period == 'monthly' ? 1 : 2;
        $plan->enable_ordering = $request->ordering == 'enabled' ? 1 : 2;
        $plan->limit_orders = $request->ordering == 'enabled' ? $request->limit_orders : 0;

        $plan->description = $request->description;
        $plan->features = $request->features;

        $plan->update();

        $this->updatePlanPlugins($plan, $request->pluginsSelector);

        return redirect()->route('plans.index')->withStatus(__('Plan successfully updated!'));
    }

    private function updatePlanPlugins($plan, $pluginsSelector)
    {
        if ($pluginsSelector) {
            $plan->setConfig('plugins', json_encode($pluginsSelector));
        } else {
            //Set to null
            $plan->setConfig('plugins', null);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy(Plans $plan): RedirectResponse
    {
        $this->adminOnly();
        $plan->delete();

        return redirect()->route('plans.index')->withStatus(__('Plan successfully deleted!'));
    }

    public function subscribe3dStripe(Request $request, Plans $plan, User $user): RedirectResponse
    {
        if ($request->success.'' == 'true') {
            //Assign user to plan
            $user->plan_id = $plan->id;
            $user->cancel_url = route('plans.cancel');

            $user->update();

            return redirect()->route('plans.current')->withStatus(__('Plan update!'));
        } else {
            return redirect()->route('plans.current')->withEror($request->message)->withInput();
        }
    }

    public function cancelStripeSubscription(): RedirectResponse
    {
        auth()->user()->subscription('main')->cancelNow();
        auth()->user()->cancel_url = '';
        auth()->user()->plan_id = intval(config('settings.free_pricing_id'));
        auth()->user()->update();

        return redirect()->route('plans.current')->withError(__('Subscription canceled'));

    }

    public function subscribe(Request $request): RedirectResponse
    {
        $plan = Plans::findOrFail($request->plan_id);

        if (config('settings.subscription_processor') == 'Stripe') {
            $plan_stripe_id = $plan->stripe_id;
            //Shold we do a swap
            try {
                if (auth()->user()->subscribed('main')) {

                    //SWAP
                    auth()->user()->subscription('main')->swap($plan_stripe_id);
                    auth()->user()->cancel_url = route('plans.cancel');
                } else {
                    //NEW Stripe subscription
                    $payment_stripe = auth()->user()->newSubscription('main', $plan_stripe_id)->create($request->stripePaymentId, []);
                    auth()->user()->cancel_url = route('plans.cancel');
                }
            } catch (PaymentActionRequired $e) {
                //On PaymentActionRequired - send the checkout link
                $paymentRedirect = route('cashier.payment', [$e->payment->id, 'redirect' => route('plans.subscribe_3d_stripe', ['plan' => $plan->id, 'user' => auth()->user()->id])]);

                return redirect($paymentRedirect);
            } catch (InvalidRequestException $e) {
                //Display the message
                //On PaymentActionRequired - send the checkout link
                $paymentRedirect = route('cashier.payment', [$e->payment->id, 'redirect' => route('plans.subscribe_3d_stripe', ['plan' => $plan->id, 'user' => auth()->user()->id])]);

                return redirect($paymentRedirect);
            } catch (IncompletePayment $e) {
                //On IncompletePayment - SCA - send the checkout link
                $paymentRedirect = route('cashier.payment', [$e->payment->id, 'redirect' => route('plans.subscribe_3d_stripe', ['plan' => $plan->id, 'user' => auth()->user()->id])]);

                return redirect($paymentRedirect);

            }

            //PaymentFailure
            catch (PaymentFailure $e) {
                //On Fail delete order and inform user
                return redirect()->route('plans.current')->withError(__('Payment Failure'));
            }
        }

        //Assign user to plan
        auth()->user()->plan_id = $plan->id;
        auth()->user()->update();

        return redirect()->route('plans.current')->withStatus(__('Plan update!'));
    }

    public function adminupdate(Request $request): RedirectResponse
    {
        $this->adminOnly();
        $user = User::findOrFail($request->user_id);
        $user->plan_id = $request->plan_id;
        $user->plan_status = 'set_by_admin';
        $user->update();

        return redirect()->route('admin.companies.edit', $request->company_id)->withStatus(__('Plan successfully updated.'));
    }

    public function isExtended()
    {
        //First check if pure saas
        if (config('settings.makePureSaaS')) {
            //Do a check if the APPs download code is set
            if (md5(config('settings.extended_license_download_code', '')) == 'd0398556dbecac06370bdc8baec559a9' || config('settings.is_demo', false)) {
                return true;
            } else {
                return true;
            }
        } else {
            //Old way
            return true;
        }
    }
}
