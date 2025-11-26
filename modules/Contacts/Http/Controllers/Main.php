<?php

namespace Modules\Contacts\Http\Controllers;

use Modules\Contacts\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\Plans;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Contacts\Exports\ContactsExport;
use Modules\Contacts\Imports\ContactsImport;
use Modules\Contacts\Models\Country;
use Modules\Contacts\Models\Field;
use Modules\Contacts\Models\Group;

class Main extends Controller
{
    /**
     * Provide class.
     */
    private $provider = Contact::class;

    /**
     * Web RoutePath for the name of the routes.
     */
    private $webroute_path = 'contacts.';

    /**
     * View path.
     */
    private $view_path = 'contacts::contacts.';

    /**
     * Parameter name.
     */
    private $parameter_name = 'contact';

    /**
     * Title of this crud.
     */
    private $title = 'contact';

    /**
     * Title of this crud in plural.
     */
    private $titlePlural = 'contacts';

    private function hasAccessToAIBots(){
        return false;
        $allowedPluginsPerPlan = auth()->user()->company?auth()->user()->company->getPlanAttribute()['allowedPluginsPerPlan']:[];
        if($allowedPluginsPerPlan==null||in_array("flowiseai",$allowedPluginsPerPlan)){
            return true;
        }else{
            return false;
        }
    }

    private function getFields($class='col-md-4',$getCustom=true)
    {
        $fields=[];

        //Avatar
        $fields[0]=['class'=>$class, 'ftype'=>'image', 'name'=>'Avatar', 'id'=>'avatar','style'=>'width: 200px; height:200'];
        
        //Add name field
        $fields[1]=['class'=>$class, 'ftype'=>'input', 'name'=>'Name', 'id'=>'name', 'placeholder'=>'Enter name', 'required'=>true];

        //Add phone field
        $fields[2]=['class'=>$class, 'ftype'=>'input','type'=>"phone", 'name'=>'Phone', 'id'=>'phone', 'placeholder'=>'Enter phone', 'required'=>true];

        //Groups
        $fields[3]=['class'=>$class, 'multiple'=>true, 'classselect'=>"select2init", 'ftype'=>'select', 'name'=>'Groups', 'id'=>'groups[]', 'placeholder'=>'Select group', 'data'=>Group::get()->pluck('name','id'), 'required'=>true];
        
        //Country
        $fields[4]=['class'=>$class, 'ftype'=>'select', 'name'=>'Country', 'id'=>'country_id', 'placeholder'=>'Select country', 'data'=>Country::get()->pluck('name','id'), 'required'=>true];

        //Email
        $fields[5]=['class'=>$class, 'ftype'=>'input', 'name'=>'Email', 'id'=>'email', 'placeholder'=>'Enter email', 'required'=>false];
        
        //AI Bot enabled
        $customFieldStart=5;

       if($this->hasAccessToAIBots()){
            $customFieldStart=6;
            $fields[5]=['class'=>$class, 'ftype'=>'bool', 'name'=>'Enable AI bot Replies', 'id'=>'enabled_ai_bot', 'placeholder'=>'AI Bot replies enabled', 'required'=>false];

        }


        if($getCustom){
            $customFields=Field::get()->toArray();
            $i=$customFieldStart;   
            foreach ($customFields as $filedkey => $customField) {
                $i++;
                $fields[$i]=['class'=>$class, 'ftype'=>'input', 'type'=>$customField['type'], 'name'=>__($customField['name']), 'id'=>"custom[".$customField['id']."]", 'placeholder'=>__($customField['name']), 'required'=>false];
    
            }
        }
        

        //Return fields
        return $fields;
    }


    private function getFilterFields(){
        $fields=$this->getFields('col-md-3',false);
        unset($fields[0]);
        $fields[1]['required']=false;
        $fields[2]['required']=false;

        $fields[3]['required']=false;
        $fields[3]['multiple']=false;
        $fields[3]['id']='group';
        unset($fields[3]['multiple']);

        $fields[4]['required']=false;
        $fields[4]['multiple']=false;
        unset($fields[4]['multiple']);

        $fields[5]['required']=false;

        unset($fields[6]);

        $fields[6]=['class'=>'col-md-3', 'ftype'=>'select', 'name'=>'Subscribed', 'id'=>'subscribed', 'placeholder'=>'Select status', 'data'=>['1'=>"Subscribed",'0'=>"Opted out"], 'required'=>false];


        //unset($fields[2]);
        return $fields;
    }

    /**
     * Auth checker functin for the crud.
     */
    private function authChecker()
    {
        $this->ownerAndStaffOnly();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authChecker();

        $items=$this->provider::orderBy('id', 'desc');
        if(isset($_GET['name'])&&strlen($_GET['name'])>1){
            $items=$items->where('name',  'like', '%'.$_GET['name'].'%');
        }
        if(isset($_GET['phone'])&&strlen($_GET['phone'])>1){
            $items=$items->where('phone',  'like', '%'.$_GET['phone'].'%');
        }

        if(isset($_GET['email'])&&strlen($_GET['email'])>1){
            $items=$items->where('email',  'like', '%'.$_GET['email'].'%');
        }

        if(isset($_GET['group'])&&strlen($_GET['group']."")>0){
            $items=$items->whereHas('groups', function ($query) {
                $query->where('groups.id',  $_GET['group']);
            });
        }
        if(isset($_GET['country_id'])&&strlen($_GET['country_id'])>0){
            $items=$items->where('country_id', $_GET['country_id'] );
        }
        
        //Check subscribed
        if(isset($_GET['subscribed'])&&strlen($_GET['subscribed'])>0){
            $items=$items->where('subscribed', $_GET['subscribed'] );
        }

        if(isset($_GET['report'])){
            //dd($items->with(['fields','groups'])->get());      
            return $this->exportCSV($items->with(['fields','groups'])->get());
            
        }
        $totalItems=$items->count();
        $items=$items->paginate(config('settings.paginate'));


        return view($this->view_path.'index', ['setup' => [
            'usefilter'=>true,
            'title'=>__('crud.item_managment', ['item'=>__($this->titlePlural)]),
            'subtitle'=>$totalItems==1?__('1 Contact'):$totalItems." ".__('Contacts'),
            'action_link'=>route($this->webroute_path.'create'),
            'action_name'=>__('crud.add_new_item', ['item'=>__($this->title)]),
            'action_link2'=>route($this->webroute_path.'groups.index'),
            'action_name2'=>__('Groups'),
            'action_link3'=>route($this->webroute_path.'fields.index'),
            'action_name3'=>__('Fields'),
            'action_link4'=>route($this->webroute_path.'index',['report'=>true]),
            'action_name4'=>__('Export'),
            'items'=>$items,
            'item_names'=>$this->titlePlural,
            'webroute_path'=>$this->webroute_path,
            'fields'=>$this->getFields(),
            'filterFields'=>$this->getFilterFields(),
            'custom_table'=>true,
            'parameter_name'=>$this->parameter_name,
            'parameters'=>count($_GET) != 0,
            'groups'=>Group::get(),
        ]]);
    }

    public function exportCSV($contactsToDownload){
        $items=[];
        $cf=Field::get();
        foreach ($contactsToDownload as $key => $contact) {
            $item = [
                'id'=>$contact->id,
                'name'=>$contact->name,
                'phone'=>$contact->phone,
                'avatar'=>$contact->avatar,
                'email'=>$contact->email,
            ];

            foreach( $cf as $keycf => $scf) {
                $item[$scf->name]="";
                foreach ($contact->fields as $key => $value) {
                    if($scf->name==$value['name']){
                        $item[$value['name']]=$value['pivot']['value'];
                    }
                    
                }
            }

           
            array_push($items, $item);
        }
        return Excel::download(new ContactsExport($items), 'contacts_'.time().'.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authChecker();


        return view($this->view_path.'edit', ['setup' => [
            'title'=>__('crud.new_item', ['item'=>__($this->title)]),
            'action_link'=>route($this->webroute_path.'index'),
            'action_name'=>__('crud.back'),
            'iscontent'=>true,
            'action'=>route($this->webroute_path.'store'),
        ],
        'fields'=>$this->getFields() ]);
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
        
        //Create new contact
        $contact = $this->provider::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
        ]);
        $contact->save();

        if($request->has('avatar')){
            if(config('settings.use_s3_as_storage',false)){
                //S3
                $contact->avatar=Storage::disk('s3')->url($request->avatar->storePublicly("uploads/".$contact->company_id."/contacts",'s3'));
            }else{
                $contact->avatar=Storage::disk('public_media_upload')->url($request->avatar->store(null,'public_media_upload'));
            }

            $contact->update();
        }

        // Attaching groups to the contact
        $contact->groups()->attach($request->groups);

        if(isset($request->custom)){
            $this->syncCustomFieldsToContact($request->custom,$contact);
        }

        return redirect()->route($this->webroute_path.'index')->withStatus(__('crud.item_has_been_added', ['item'=>__($this->title)]));
    }

    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Contact  $contacts
     * @return \Illuminate\Http\Response
     */
    public function edit(Contact $contact)
    {
        $this->authChecker();

        $fields = $this->getFields();
        $fields[0]['value'] = $contact->avatar;
        $fields[1]['value'] = $contact->name;
        $fields[2]['value'] = $contact->phone;

        $fields[3]['multipleselected'] = $contact->groups->pluck('id')->toArray();
        $fields[4]['value'] = $contact->country_id;

        $fields[5]['value'] = $contact->email;

        if($this->hasAccessToAIBots()){
            $fields[6]['value'] = $contact->enabled_ai_bot.""=="1";
        }


        $customFieldsValues=$contact->fields->toArray();
        foreach ($customFieldsValues as $key => $fieldWithPivot) {
            foreach ( $fields as $key => &$formField) {
               if($formField['id']=="custom[".$fieldWithPivot['id']."]"){
                $formField['value']=$fieldWithPivot['pivot']['value'];
               }
            }
        }


        $parameter = [];
        $parameter[$this->parameter_name] = $contact->id;
        $title=__('crud.edit_item_name', ['item'=>__($this->title), 'name'=>$contact->name]);
        return view($this->view_path.'edit', ['setup' => [
            'title'=>$title ." - ". ($contact->subscribed=="1" ? __('Subscribed') : __('Opted out')),
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
     * @param  \App\Contact  $contacts
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->authChecker();
        $item = $this->provider::findOrFail($id);
        $item->name = $request->name;
        $item->phone = $request->phone;
        $item->country_id = $request->country_id;
        $item->email = $request->email;
        if($this->hasAccessToAIBots()){
            $item->enabled_ai_bot = $request->enabled_ai_bot=="true";
        }


        if($request->has('avatar')){
            if(config('settings.use_s3_as_storage',false)){
                //S3
                $item->avatar=Storage::disk('s3')->url($request->avatar->storePublicly("uploads/".$item->company_id."/contacts",'s3'));
            }else{
                $item->avatar=Storage::disk('public_media_upload')->url($request->avatar->store(null,'public_media_upload'));
            }

            
        }

        $item->update();

        if(isset($request->custom)){
            $this->syncCustomFieldsToContact($request->custom,$item);
        }
        

        // Attaching groups to the contact
        $item->groups()->sync($request->groups);
        $item->update();

        return redirect()->route($this->webroute_path.'index')->withStatus(__('crud.item_has_been_updated', ['item'=>__($this->title)]));
    }


    public function syncCustomFieldsToContact($fields,$contact){
        $contact->fields()->sync([]);
        foreach ($fields as $key => $value) {
            if($value){
                $contact->fields()->attach($key, ['value' => $value]);
            }
          
        }
        $contact->update();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Contact  $contacts
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authChecker();
        $item = $this->provider::findOrFail($id);
        $item->delete();
        return redirect()->route($this->webroute_path.'index')->withStatus(__('crud.item_has_been_removed', ['item'=>__($this->title)]));
    }

    public function bulkremove($ids)
    {
        $this->authChecker();
        $ids = explode(",", $ids);
        $this->provider::destroy($ids);

        // Return a JSON response
        return response()->json([
            'status' => 'success',
            'message' => __('crud.items_have_been_removed', ['item' => __($this->titlePlural)])
        ], 200);
    }

    public function subscribe($ids)
    {
        $this->authChecker();
        $ids = explode(",", $ids);
        $this->provider::whereIn('id', $ids)->update(['subscribed' => 1]);

        // Return a JSON response
        return response()->json([
            'status' => 'success',
            'message' => __('crud.item_has_been_updated', ['item' => __($this->titlePlural)])
        ], 200);
    }

    public function unsubscribe($ids)
    {
        $this->authChecker();
        $ids = explode(",", $ids);
        $this->provider::whereIn('id', $ids)->update(['subscribed' => 0]);

        // Return a JSON response
        return response()->json([
            'status' => 'success',
            'message' => __('crud.item_has_been_updated', ['item' => __($this->titlePlural)])
        ], 200);
    }

    

    public function assigntogroup($ids)
    {
        $this->authChecker();
        $ids = explode(",", $ids);
        $group = Group::find($_GET['group_id']);

        if (!$group) {
            // Group not found, return an error response
            return response()->json([
                'status' => 'error',
                'message' => __('No group selected')
            ], 404);
        }

        $group->contacts()->syncWithoutDetaching($ids);

        // Return a JSON response
        return response()->json([
            'status' => 'success',
            'message' => __('crud.items_has_been_updated', ['item' => __($this->titlePlural)])
        ], 200);
    }

    public function removefromgroup($ids)
    {
        $this->authChecker();
        $ids = explode(",", $ids);
        $group = Group::find($_GET['group_id']);

        if (!$group) {
            // Group not found, return an error response
            return response()->json([
                'status' => 'error',
                'message' => __('No group selected')
            ], 404);
        }

        $group->contacts()->detach($ids);

        // Return a JSON response
        return response()->json([
            'status' => 'success',
            'message' => __('crud.items_has_been_updated', ['item' => __($this->titlePlural)])
        ], 200);
    }

    public function importindex(){
        $groups=Group::pluck('name','id');
        return view("contacts::".$this->webroute_path.'import',['groups'=>$groups]);
    }

    public function import(Request $request){
       
       $lastContact=$this->provider::orderBy('id', 'desc')->first();
       Excel::import(new ContactsImport,$request->csv);

       //Assign to group
       if($request->group){
         //Get the contacts, that are newer than the previous id
         $contactToApply=null;

         //Find the contacts based on the phone in the attached csv
         $csvData = Excel::toArray(new ContactsImport, $request->csv);
         $phoneNumbers = array_column($csvData[0], 'phone');
         //In each row of the csv, we have the phone number, add + at start
            $phoneNumbers = array_map(function($phone){
                return strpos($phone,"+")!=false?$phone:"+".$phone;
            }, $phoneNumbers);
         $contactToApply = $this->provider::whereIn('phone', $phoneNumbers)->pluck('id');

        
            
         if($contactToApply){
            $group = Group::find($request->group);
            $group->contacts()->detach($contactToApply);
            $group->contacts()->attach($contactToApply);
         }
       }
       return redirect()->route($this->webroute_path.'index')->withStatus(__('Contacts imported'));
    }
    
}
