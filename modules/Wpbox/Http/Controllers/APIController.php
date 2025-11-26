<?php

namespace Modules\Wpbox\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;
use Modules\Wpbox\Models\Contact;
use Modules\Wpbox\Models\Campaign;
use Modules\Wpbox\Models\Template;
use Modules\Wpbox\Traits\Whatsapp;
use Modules\Wpbox\Traits\Contacts;
use Carbon\Carbon;
use Closure;
use Modules\Contacts\Models\Group;
use Modules\Wpbox\Models\Message;
use Modules\Wpbox\Models\Reply;
use Illuminate\Support\Facades\Storage;
use Modules\Contacts\Models\Field;
use Illuminate\Support\Facades\Cache;
use Modules\Wpbox\Events\AgentReplies;
use Modules\Wpbox\Jobs\SendMessage;

class APIController extends Controller
{
    use Contacts;
    use Whatsapp;

    public function sendListMessageToPhoneNumber(Request $request)
    {
        return $this->authenticate($request,function($request){
            //Company
            $company=$this->getCompany();

            //Make or get the contact
            $contact=$this->getOrMakeContact($request->phone,$company,$request->phone);

            Log::info($contact);

            $createData=[
                "contact_id"=>$contact->id,
                "company_id"=>$contact->company_id,
                "value"=>$request->message,
                "header_text"=>$request->header,
                "footer_text"=>$request->footer,
                "buttons"=>json_encode($request->action),
                "is_message_by_contact"=>false,
                "is_campign_messages"=>false,
                "status"=>1,
                "fb_message_id"=>null
            ];

           

            $messageToBeSend=Message::create($createData);

            $messageToBeSend->save();

            Log::info($messageToBeSend);

       
   

            $contact->last_support_reply_at=now();
            $contact->is_last_message_by_contact=false;
            $contact->sendMessageToWhatsApp($messageToBeSend,$contact);
       
            //Find the user of the company
            $companyUser=$company->user;
            event(new AgentReplies($companyUser,$messageToBeSend,$contact));

            $contact->last_message=$contact->trimString($request->message,40);
            $contact->update();

            return $messageToBeSend;
        },[
            'token' => 'required',
            'phone' => 'required',
            'message' => 'required',
            'action' => 'required',
        ]);
    }

    
   


    //Send message to phone number
    public function sendMessageToPhoneNumber(Request $request)
    {
        return $this->authenticate($request,function($request){
            //Company
            $company=$this->getCompany();
             
            //Make or get the contact
            $contact=$this->getOrMakeContact($request->phone,$company,$request->phone);

            //If request has buttons
            if($request->has('buttons') || $request->has('header') || $request->has('footer')){
                
                $header_text="";
                if($request->has('header')){
                    $header_text=$request->header;
                }
                $footer_text="";
                if($request->has('footer')){
                    $footer_text=$request->footer;
                }

                // Make Replay object
                $replay = new Reply([
                    'trigger' => 'none',
                    'type' => 1,
                    'text' => $request->message,
                    'company_id' => $company->id,
                    'header' => $header_text,
                    'footer' => $footer_text,
                ]);

                //In the $reply we have button1, button1_id. Assign them from $buttons
                if($request->has('buttons')){
                    foreach ($request->buttons as $key => $button) {
                        if($key<3){
                            $replay['button'.($key+1)]=$button['title'];
                            $replay['button'.($key+1).'_id']=$button['id'];
                        }
                    }
                }
               

                $message=$contact->sendReply($replay);

            }else if($request->has('image')){
                //Image message
                $imageUrl="";
                if(config('settings.use_s3_as_storage',false)){
                    //S3 - store per company
                    $path = $request->image->storePublicly('uploads/media/send/'.$contact->company_id,'s3');
                    $imageUrl = Storage::disk('s3')->url($path);
                }else{
                    //Regular
                    $path = $request->image->store(null,'public_media_upload',);
                    $imageUrl = Storage::disk('public_media_upload')->url($path);
                }
        
                $fileType = $request->file('image')->getMimeType();
                if (str_contains($fileType, 'image')) {
                    // It's an image
                    $messageType = "IMAGE";
                } elseif (str_contains($fileType, 'video')) {
                    // It's a video
                    $messageType = "VIDEO";
                } elseif (str_contains($fileType, 'audio')) {
                    // It's audio
                    $messageType = "VIDEO";
                } else {
                    // Handle other types or show an error message
                    $messageType = "IMAGE";
                }
               
                $message=$contact->sendMessage($imageUrl,false,false,$messageType);

            }else if($request->has('media_url')){
                //Media URL message - fetch and store the media
                $imageUrl="";
                
                try {
                    // Fetch the media from the URL
                    $mediaContent = file_get_contents($request->media_url);
                    
                    if($mediaContent === false) {
                        return response()->json(['status'=>'error','message'=>'Failed to fetch media from URL']);
                    }
                    
                    // Get file extension from URL or use default
                    $urlInfo = pathinfo(parse_url($request->media_url, PHP_URL_PATH));
                    $extension = isset($urlInfo['extension']) ? $urlInfo['extension'] : 'jpg';
                    
                    // Generate unique filename
                    $filename = 'media_' . time() . '_' . uniqid() . '.' . $extension;
                    
                    if(config('settings.use_s3_as_storage',false)){
                        //S3 - store per company
                        $path = 'uploads/media/send/'.$contact->company_id.'/'.$filename;
                        Storage::disk('s3')->put($path, $mediaContent);
                        $imageUrl = Storage::disk('s3')->url($path);
                    }else{
                        //Regular storage
                        $path = $filename;
                        Storage::disk('public_media_upload')->put($path, $mediaContent);
                        $imageUrl = Storage::disk('public_media_upload')->url($path);
                    }
                    
                    // Get file info to determine type
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $fileType = finfo_buffer($finfo, $mediaContent);
                    finfo_close($finfo);
                    
                    if (str_contains($fileType, 'image')) {
                        // It's an image
                        $messageType = "IMAGE";
                    } elseif (str_contains($fileType, 'video')) {
                        // It's a video
                        $messageType = "VIDEO";
                    } elseif (str_contains($fileType, 'audio')) {
                        // It's audio
                        $messageType = "AUDIO";
                    } else {
                        // Handle other types as document
                        $messageType = "DOCUMENT";
                    }
                    
                    $message=$contact->sendMessage($imageUrl,false,false,$messageType);
                    
                } catch (\Exception $e) {
                    return response()->json(['status'=>'error','message'=>'Failed to process media URL: ' . $e->getMessage()]);
                }

            }else{
                //Just message
                $message=$contact->sendMessage($request->message,false);
            }
            return response()->json(['status'=>'success','message_id'=>$message->id,'message_wamid'=>$message->fb_message_id]);     




            
           
        },[
            'token' => 'required',
            'phone' => 'required'
            //'message' => 'required',
        ]); 
    }

    //Send Template     message to phone number
    public function sendTemplateMessageToPhoneNumber(Request $request)
    {

        return $this->authenticate($request,function($request){
            //Company
            $company=$this->getCompany();
            
             //Make or get the contact
             $contact=$this->getOrMakeContact($request->phone,$company,$request->phone);

             //Find the template based on the provided id (with 5 day cache)
             $templateCacheKey = "template_{$company->id}_{$request->template_name}_{$request->template_language}";
             $template = Cache::remember($templateCacheKey, 60 * 60 * 24 * 5, function () use ($company, $request) {
                 return Template::where('company_id',$company->id)->where('name',$request->template_name)->where('language',$request->template_language)->first();
             });
 
             if(!$template){
                 return response()->json(['status'=>'error','message'=>'Invalid template']);
             }
 
             $campaign = Campaign::create([
                 'company_id'=>$company->id,
                 'name'=>"api_message_".now(),
                 'timestamp_for_delivery'=>null,
                 'variables'=>"",
                 'variables_match'=>"",
                 'template_id'=>$template->id,
                 'group_id'=>null,
                 'contact_id'=>$contact->id,
                 'total_contacts'=>Contact::count(),
             ]);
 
             $bodyText="API Message";
             $header_text="";
             $header_image="";
             $header_video="";
             $header_audio="";
             $header_document="";
             try {
                 foreach(json_decode($template->components,true) as $component){
                     if($component['type']=='BODY'){
                         $bodyText=$component['text'];
                         foreach ($request->components as $key => $receivedComponent) {
                             if($receivedComponent['type']=='body'){
                                 foreach ($receivedComponent['parameters'] as $keyp => $parameter) {
                                     $bodyText=str_replace("{{".($keyp+1)."}}", $parameter['text'], $bodyText);
                                 }
                             }
                         }
                     }
                     if($component['type']=='HEADER'&&$component['format']=="TEXT"){ 
                         $header_text=$component['text'];
                         foreach ($request->components as $key => $receivedComponent) {
                             if($receivedComponent['type']=='header'){
                                 foreach ($receivedComponent['parameters'] as $keyp => $parameter) {
                                     $bodyText=str_replace("{{".($keyp+1)."}}", $parameter['text'], $bodyText);
                                 }
                             }
                         }
                     }

                    // Handle header media types
                    if($component['type']=='HEADER'){
                        foreach ($request->components as $key => $receivedComponent) {
                            if($receivedComponent['type']=='header'){
                                foreach ($receivedComponent['parameters'] as $keyp => $parameter) {
                                    if(isset($parameter['type'])){
                                        switch($parameter['type']){
                                            case 'image':
                                                $header_image = $parameter['image']['link'];
                                                break;
                                            case 'video':
                                                $header_video = $parameter['video']['link'];
                                                break;
                                            case 'document':
                                                $header_document = $parameter['document']['link'];
                                                break;
                                            case 'audio':
                                                $header_audio = $parameter['audio']['link'];
                                                break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                     //Video header
                     
                 }
             } catch (\Throwable $th) {
                 //throw $th;
             }
             
            
             $dataForMessage=[
                 "contact_id"=>$contact->id,
                 "company_id"=>$contact->company_id,
                 "value"=>$bodyText,
                 "header_image"=>$header_image,
                 "header_video"=>$header_video,
                 "header_audio"=>$header_audio,
                 "header_document"=>$header_document,
                 "footer_text"=>"",
                 "buttons"=>"",
                 "header_text"=>$header_text,
                 "is_message_by_contact"=>false,
                 "is_campign_messages"=>true,
                 "status"=>0,
                 "created_at"=>now(),
                 "scchuduled_at"=>Carbon::now(),
                 "components"=>json_encode($request->components),
                 "campaign_id"=>$campaign->id,
             ];
            
 
             //Create a message on the contact
             $message=Message::create($dataForMessage);

             //Add logging before dispatch
             Log::info('Attempting to dispatch SendMessage job', [
                 'message_id' => $message->id,
                 'contact_id' => $contact->id,
                 'company_id' => $company->id
             ]);

             try {
                 //Instead of sending it here,  queuing it to be sent
                 dispatch(new SendMessage($message));

                 Log::info('Successfully dispatched SendMessage job', [
                     'message_id' => $message->id
                 ]);
             } catch (\Exception $e) {
                 Log::error('Failed to dispatch SendMessage job', [
                     'message_id' => $message->id,
                     'error' => $e->getMessage(),
                     'trace' => $e->getTraceAsString()
                 ]);
                 
                 throw $e;
             }
    
             return response()->json(['status'=>'success','message_id'=>$message->id,'message_wamid'=>$message->fb_message_id]);

        },
        [
            'token' => 'required',
            'phone' => 'required',
            'template_name' => 'required',
            'template_language' => 'required',
            'components' => 'array'
        ]);

        
    }

    //Get ot make contact  //Last Update by Brij 24Jun
    public function makeContact($name,$phone,$company)
    {
        $contact=Contact::where('company_id',$company->id)->where('phone',$phone)->first();
        if(!$contact){
            $contact=Contact::create([
                'name'=>$name ?? $phone,
                'phone'=>$phone,
                'company_id'=>$company->id,
            ]);
        }
        return $contact;
    }

    //Get templates
    public function getTemplates(Request $request)
    {
       
        return $this->authenticate($request,function($request){
            //Company
            $company=$this->getCompany();
             //Find the template based on the provided id
             $templates=Template::where('company_id',$company->id)->get();

             return response()->json(['status'=>'success','templates'=>$templates]);
        });
    }

    //Send Campaign via API
    public function sendCampaignMessageToPhoneNumber(Request $request)
    {

        return $this->authenticate($request,function($request){
   
            //Make or get the contact
            $contact=$this->getOrMakeContact($request->phone,$this->getCompany(),$request->phone);

            //All the passed data in request data, merge with the contact
            $contact['extra_value']=$request->data;
       
             //Get the campaign
            $message=Campaign::findOrFail($request->campaing_id)->makeMessages(null,$contact);
 
            //We are queuing the message to be sent, so we don't need to send it here
            //$this->sendCampaignMessageToWhatsApp($message);

            //Api responses
            return response()->json(['status'=>'success','message_id'=>$message->id,'message_wamid'=>$message->fb_message_id]);
        },
        [
            'token' => 'required',
            'phone' => 'required',
            'campaing_id' => 'required',
        ]);  
    }

    //Get groups
    public function getGroups(Request $request)
    {
        
        return $this->authenticate($request,function($request){
            //Company
            $company=$this->getCompany();
            if ($request->has('showContacts') && $request->showContacts == "yes") {
                $groups = Group::where('company_id', $company->id)->with('contacts')->get();
            } else {
                $groups = Group::where('company_id', $company->id)->get();
            }

            return response()->json(['status'=>'success','groups'=>$groups]);

        });
    }

    public function getCampaigns(Request $request)
    {
        
        return $this->authenticate($request,function($request){
            //Company
            $company=$this->getCompany();


            
            if ($request->has('type')) {
                if($request->type=='bot'){
                    $items = Campaign::where('company_id', $company->id)->where('is_bot',true)->get();
                }else if($request->type=='api'){
                    $items = Campaign::where('company_id', $company->id)->where('is_api',true)->get();
                }else if($request->type=='regular'){
                    $items = Campaign::where('company_id', $company->id)->where('is_api',false)->where('is_bot',false)->get();
                }
            } else {
                $items = Campaign::where('company_id', $company->id)->get();
            }

            return response()->json(['status'=>'success','items'=>$items]);

        });
    }

    

    public function getContacts(Request $request)
    {
        return $this->authenticate($request,function($request){
            //Company
            $company=$this->getCompany();
            return response()->json(['status'=>'success','contacts'=>Contact::where('company_id',$company->id)->get()]);
        });
    }

    public function getConversations(Request $request)
    {
        return $this->authenticate($request,function($request){
            //Company
            $company=$this->getCompany();
            $chatList=Contact::where('has_chat',1)->where('company_id',$company->id)->orderBy('last_reply_at','DESC')->limit(150)->get();
            return response()->json([
                'data' => $chatList,
                'status' => true,
                'errMsg' => '',
            ]);
            
        });
    }

    public function getMessages(Request $request)
    {
        return $this->authenticate($request,function($request){
            //Company
            $messages=Message::where('contact_id',$request->contact_id)->where('status','>',0)->orderBy('id','desc')->limit(100)->get();
            return response()->json([
                'data' => $messages,
                'status' => true,
                'errMsg' => '',
            ]);
            
        },[
            'token' => 'required',
            'contact_id' => 'required'
        ]);
    }

    public function updateContact(Request $request)
    {
        return $this->authenticate($request,function($request){
            //Company
            $company=$this->getCompany();
            $contact=Contact::findOrFail($request->id);
            $contact->update($request->all());
            return response()->json(['status'=>'success','contact'=>$contact]);
        },[
            'id' => 'required',
        ]);
    }


     //Send Template     message to phone number
     public function contactApiMake(Request $request)
     {
        return $this->authenticate($request,function($request){
            //Company
            $company=$this->getCompany();
            
            $contact=$this->makeContact($request->name,$request->phone,$company);

            //Update the contact
            $contact->update(['name' => $request->name]);
            
            //If there is a email
            if($request->has('email')){
                $contact->update(['email' => $request->email]);
            }



            //If request has groups
            if($request->has('groups')){
               // Attaching groups to the contact
               $contact->groups()->sync([]);
                // Groups are passed as string with comma
                $groups = explode(',', $request->groups);
                
                // Remove empty values from the array
                $groups = array_filter($groups);

                //Convert each group name into a group id
                $groupIds = [];
                foreach ($groups as $groupName) {
                    $groupId = Group::where('name', $groupName)->where('company_id', $company->id)->first();
                    if ($groupId) {
                        $groupIds[] = $groupId->id;
                    }else{
                        //Create a new group
                        $groupId = Group::create([
                            'name' => $groupName,
                            'company_id' => $company->id,
                        ]);
                        $groupIds[] = $groupId->id;
                    }
                }

                $contact->groups()->attach($groupIds);
            }

            //If request has custom fields
            if($request->has('custom')){
                $contact->fields()->sync([]);
                foreach ($request->custom as $key => $value) {
                    if($value){
                        //Find the custom field id
                        $fieldId = Field::where('name', $key)->where('company_id', $company->id)->first();
                        if ($fieldId) {
                            $contact->fields()->attach($fieldId->id, ['value' => $value]);
                        }else{
                            //Create a new custom field
                            $field = Field::create([
                                'name' => $key,
                                'company_id' => $company->id,
                            ]);
                            $contact->fields()->attach($field->id, ['value' => $value]);
                        }
                    }
                }
                
            }
            $contact->update();
            $contact->load('groups', 'fields');
            return response()->json([
                'status' => 'success',
                'contact' => $contact
            ]);

        },[
            'token' => 'required',
            'phone' => 'required'
        ]);
    }

    public function getCustomFields(Request $request)
    {
       
    }

    public function getSingleContact(Request $request)
    {
        return $this->authenticate($request,function($request){
            //Company
            $company=$this->getCompany();
            
            if($request->has('contact_id')) {
                $contact = Contact::where('id', $request->contact_id)
                    ->where('company_id', $company->id)
                    ->firstOrFail();
            } else if($request->has('phone')) {
                $contact = Contact::where('phone', $request->phone)
                    ->where('company_id', $company->id)
                    ->firstOrFail();
            }

            return response()->json(['status'=>'success','contact'=>$contact]);
        },[
            'token' => 'required',
            'contact_id' => 'required_without:phone',
            'phone' => 'required_without:contact_id'
        ]);
    }

    private function authenticate(Request $request,Closure $next,$rules=['token' => 'required']){
        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 400);
        }
        
        /*if (config('settings.is_demo')) {
            return response()->json([
                'status' => 'error',
                'errors' => "API is disabled in demo"
            ], 400);
        }*/

        //Authenticate the user, if there is no autnenticatedd user already
        if(!Auth::check()){
            $token = PersonalAccessToken::findToken($request->token);
            if(!$token){
                return response()->json(['status'=>'error','message'=>'Invalid token']);
            }else{
                
                $user=User::findOrFail($token->tokenable_id);
                Auth::login($user);
                return $next($request);
            }
        }else{
            //User is already authenticated, so just return the next
            return $next($request);
        }
    }

    public function info()  
    {
        $token = PersonalAccessToken::where('tokenable_id',auth()->user()->id)->where('tokenable_type','App\Models\User')->first();
        $company= $this->getCompany();

        if(!$token||$company->getConfig('whatsapp_webhook_verified','no')!='yes' ||$company->getConfig('whatsapp_settings_done','no')!='yes'){
            return redirect(route('whatsapp.setup'));
         }

       
        //Get old config
        $planText=$company->getConfig('plain_token','');
        
        return view('wpbox::api.info',['token'=>$planText,'company'=>$company]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      

        $items = Campaign::orderBy('id', 'desc')->whereNull('contact_id')->where('is_api', true)->get();
           
        //Regular, bot ant template based bot
        $setup=[
            'usefilter'=>null,
            'title'=>__('API Campaigns'),
            'action_link'=>route('campaigns.create',['type'=>'api']),
            'action_name'=>__('New API Campaign'),
            'action_link2'=>route('api.info'),
            'action_name2'=>__('API Info'),
            'items'=>$items,
            'item_names'=>__('API Campaigns'),
            'webroute_path'=>'campaigns.',
            'fields'=>[],
            'filterFields'=>[],
            'custom_table'=>true,
            'parameter_name'=>'campaigns',
            'parameters'=>count($_GET) != 0,
            'hidePaging'=>true,
        ];


        return view('wpbox::api.index', ['setup' => $setup]);
    }

    public function updateAIBot(Request $request)
    {
        return $this->authenticate($request, function($request) {
            // Company
            $company = $this->getCompany();
            
            // Validate request
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'enabled_ai_bot' => 'required|in:0,1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors()
                ], 400);
            }

            // Find the contact
            $contact = Contact::where('id', $request->id)
                ->where('company_id', $company->id)
                ->first();

            if (!$contact) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Contact not found'
                ], 404);
            }

            // Update the AI bot status
            $contact->update([
                'enabled_ai_bot' => $request->enabled_ai_bot
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'AI Bot status updated successfully'
            ]);
        }, [
            'token' => 'required'
        ]);
    }

    public function getContactGroupsAndCustomFields(Contact $contact)
    {

        // Get the contact's groups with their details
        $groups = $contact->groups()->get();
        $customFields = $contact->fields->toArray();
        foreach ($customFields as $key => $fieldWithPivot) {
            $customFields[$key]['value'] = $fieldWithPivot['pivot']['value'];
        }
        return response()->json([
            'groups' => $groups,
            'customFields' => $customFields
        ]);
    }

    public function getNotes(Contact $contact)
    {
        // Get all notes for the contact
        $notes = $contact->notes()->orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $notes
        ]);
    }


    public function me(Request $request)
    {
        //return response()->json(['status'=>'success']);
        return $this->authenticate($request, function($request) {
            return response()->json(['status'=>'success','user'=>auth()->user()]);
        });
    }
}
