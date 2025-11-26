<?php

namespace Modules\Agents\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;


use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Main extends Controller
{
    /**
     * Provide class.
     */
    private $provider = User::class;

    /**
     * Web RoutePath for the name of the routes.
     */
    private $webroute_path = 'agent.';

    /**
     * View path.
     */
    private $view_path = 'agents::';

    /**
     * Parameter name.
     */
    private $parameter_name = 'agent';

    /**
     * Title of this crud.
     */
    private $title = 'agent';

    /**
     * Title of this crud in plural.
     */
    private $titlePlural = 'agent';

    /**
     * Auth checker functin for the crud.
     */
    private function authChecker()
    {
        if (!auth()->user()->hasRole('owner')) {
            abort(403, 'Unauthorized action.');
        }
    }

    private function getFields()
    {
        return [
            ['class'=>'col-md-4', 'ftype'=>'input', 'name'=>'Name', 'id'=>'name', 'placeholder'=>'First and Last name', 'required'=>true],
            ['class'=>'col-md-4', 'ftype'=>'input', 'name'=>'Email', 'id'=>'email', 'placeholder'=>'Enter email', 'required'=>true],
            ['class'=>'col-md-4', 'ftype'=>'input','type'=>"password", 'name'=>'Password', 'id'=>'password', 'placeholder'=>'Enter password', 'required'=>true],
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authChecker();
        $fields=$this->getFields();
        unset($fields[2]);

        return view($this->view_path.'index', ['setup' => [
            'title'=>__('crud.item_managment', ['item'=>__($this->titlePlural)]),
            'action_link'=>route($this->webroute_path.'create'),
            'action_name'=>__('crud.add_new_item', ['item'=>__($this->title)]),
            'items'=>$this->getCompany()->staff()->paginate(config('settings.paginate')),
            'item_names'=>$this->titlePlural,
            'webroute_path'=>$this->webroute_path,
            'fields'=>$fields,
            'parameter_name'=>$this->parameter_name,
        ]]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authChecker();

        return view('general.form', ['setup' => [
            'inrow'=>true,
            'title'=>__('crud.new_item', ['item'=>__($this->title)]),
            'action_link'=>route($this->webroute_path.'index'),
            'action_name'=>__('crud.back'),
            'iscontent'=>true,
            'action'=>route($this->webroute_path.'store'),
        ],
        'fields'=>$this->getFields(), ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authChecker();
        
        $email_exist = $this->provider::where('email', $request->email)->first();
        
        if(!$email_exist){
            $item = $this->provider::create([
                'name'=>$request->name,
                'email'=>$request->email,
                'password'=>$request->password,
                'password' => Hash::make($request->password),
                'api_token' => Str::random(80),
                'company_id'=>$this->getCompany()->id,
            ]);
            $item->save();
    
            $item->assignRole('staff');
    
            return redirect()->route($this->webroute_path.'index')->withStatus(__('crud.item_has_been_added', ['item'=>__($this->title)]));
        } else {
            
            return redirect()->route($this->webroute_path.'index')->withStatus(__('Error: This email address is already registered. Please use a different email address.', ['item'=>__($this->title)]));
        }
    }


    public function loginas($id){
        $this->authChecker();
        if (config('settings.is_demo', false)) {
            return redirect()->back()->withStatus('Not allowed in demo');
        }

        $agent=User::findOrFail($id);

        if ($agent->company->user->id!=auth()->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        Auth::login($agent, true);

        //Set the company
        Session::put('company_id', $agent->company->id);

        //Login as owner
        Session::put('impersonate', $agent->id);

        return  redirect(route('home'));


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authChecker();
        
        $item = $this->provider::findOrFail($id);
        if (!$this->getCompany()->id==$item->company_id) {
            abort(403, 'Unauthorized action.');
        }

        $fields = $this->getFields();
        $fields[0]['value'] = $item->name;
        $fields[1]['value'] = $item->email;

        $parameter = [];
        $parameter[$this->parameter_name] = $id;

        return view('general.form', ['setup' => [
            'inrow'=>true,
            'title'=>__('crud.edit_item_name', ['item'=>__($this->title), 'name'=>$item->name]),
            'action_link'=>route($this->webroute_path.'index'),
            'action_name'=>__('crud.back'),
            'iscontent'=>true,
            'isupdate'=>true,
            'action'=>route($this->webroute_path.'update', $parameter),
        ],
        'fields'=>$fields, ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->authChecker();
        $item = $this->provider::findOrFail($id);
        $item->name = $request->name;
        $item->email = $request->email;
        if($request->password&&strlen( $request->password)>2){
            $item->password = Hash::make($request->password);
        }
        $item->update();

        return redirect()->route($this->webroute_path.'index')->withStatus(__('crud.item_has_been_updated', ['item'=>__($this->title)]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authChecker();
        $item = $this->provider::findOrFail($id);
        $item->delete();
        return redirect()->route($this->webroute_path.'index')->withStatus(__('crud.item_has_been_removed', ['item'=>__($this->title)]));
    }
}


