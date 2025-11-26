<?php

namespace Modules\Wpbox\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Scopes\CompanyScope;
use Carbon\Carbon;
use Illuminate\Support\Facades\Request;
use Modules\Contacts\Models\Group;
use Modules\Wpbox\Traits\Whatsapp;
use Illuminate\Support\Facades\Log;

class Campaign extends Model
{
    use Whatsapp;
    
    protected $table = 'wa_campaings';
    public $guarded = [];

    public function template()
    {
        return $this->belongsTo(Template::class);
    }
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
    protected static function booted(){
        static::addGlobalScope(new CompanyScope);

        static::creating(function ($model){
           $company_id=session('company_id',null);
            if($company_id){
                $model->company_id=$company_id;
            }
        });
    }

   public function shouldWeUseIt($receivedMessage, Contact $contact) //Brij Mohan Negi Update
    {
        $receivedMessage = " " . strtolower($receivedMessage);
        $message = "";
        $sendThisCampaign = false;

        // Store the value of $this->trigger in a new variable
        $triggerValues = $this->trigger;
	   
        // Convert $triggerValues into an array if it contains commas
        if (strpos($triggerValues, ',') !== false) {
            $triggerValues = explode(',', $triggerValues);
        }
	   

        if (is_array($triggerValues)) {
            foreach ($triggerValues as $trigger) {
                if ($this->bot_type == 2) {
                    // Exact match
					$trigger = " " . strtolower($trigger); //Brij Mohan Negi Update
                    if ($receivedMessage == $trigger) {
                        $sendThisCampaign = true;
                        break; // exit the loop once a match is found
                    }
                } else if ($this->bot_type == 3) {
                    // Contains
                    if (stripos($receivedMessage, $trigger) !== false) {
                        $sendThisCampaign = true;
                        break; // exit the loop once a match is found
                    }
                }
            }
        } else {
            //Doesn't contain commas
            if ($this->bot_type == 2) {
                // Exact match
				
				$triggerValues = " " . strtolower($triggerValues); //Brij Mohan Negi Update
                if ($receivedMessage == $triggerValues) {
                    $sendThisCampaign = true;
                }
            } else if ($this->bot_type == 3) {
                // Contains
                if (stripos($receivedMessage, $triggerValues) !== false) {
                    $sendThisCampaign = true;
                }
            }
        }



        //Change message
        if ($sendThisCampaign) {
            $this->increment('used', 1);
            $this->update();

            $message = $this->makeMessages(null, $contact);
            $contact->sendMessage($contact->getCompany()->getConfig('delay_response', __('Give me a moment, I will have the answer shortly')), false);
            $this->sendCampaignMessageToWhatsApp($message);

            return true;
        } else {
            return false;
        }
    }


    public function makeMessages($request,Contact $contact=null){
        //For each contact, send the message

        //1. Find all the contact that this message should be send to
        if($this->group_id==null&&$this->contact_id==null&&$contact==null){
            //All contacts
            $contacts = Contact::where('subscribed', 1)->get();
        }else if($this->group_id!=null){
            //Specific group
            $contacts = Group::findOrFail($this->group_id)
                ->contacts()
                ->where('subscribed', 1)
                ->get();
        }else if($this->contact_id!=null){
            //Specific contact
            $contacts=Contact::where('id',$this->contact_id)->get();
        }else{
            //No contacts, meaning that contact is passed in run time
            $contacts=collect([$contact]);
        }
       
        //Prepare what we need
        $template=Template::withoutGlobalScope(\App\Scopes\CompanyScope::class)->where('id',$this->template_id)->first();
        $variablesValues=json_decode($this->variables,true);
        $variables_match=json_decode($this->variables_match,true);
        $messages=[];

        $this->send_to=$contacts->count();
        $this->update();
       

        //For each contact prepare the message

        // Parse the date string into a Carbon instance
        $tzBasedDelivery=false;
        $companyRelatedDateTimeOfSend=null;
        if($request!=null&&!$request->has('send_now')&&$request->has('send_time')&&$request->send_time!=null){
           $company=$this->company;

           //Set config based on com
           config(['app.timezone' => $company->getConfig('time_zone',config('app.timezone'))]);


            $companyRelatedDateTimeOfSend = Carbon::parse($request->send_time); //This will be set time in company time
            //Convert to system time
            $systemRelatedDateTimeOfSend = $companyRelatedDateTimeOfSend->copy()->tz(config('app.timezone'));//System time, can be the same
            $tzBasedDelivery=true;
        }

         //Make the components
         $components=json_decode($template->components,true); 
        
        foreach ($contacts as $key => $contact) {

            $content="";
            $header_text="";
            $header_image="";
            $header_document="";
            $header_video="";
            $header_audio="";
            $footer="";
            $buttons=[];
            
            $sendTime=Carbon::now();//Send now
            if($tzBasedDelivery){
                    try {
                        //Calculate time based on the client time zone
                        $sendTime=Carbon::parse($systemRelatedDateTimeOfSend->format('Y-m-d H:i:s'),$contact->country->timezone)->copy()->tz(config('app.timezone'))->format('Y-m-d H:i:s');
                    } catch (\Throwable $th) {
                       
                    }
            }

           
            $APIComponents=[];
            foreach ($components as $keyComponent => $component) {
                $lowKey=strtolower($component['type']);

                if($component['type'] == "HEADER" && isset($component['format']) && $component['format'] == "TEXT"){
                    $header_text=$component['text'];
                    $component['parameters']=[];
                   
                    if(isset($variables_match[$lowKey])){
                        $this->setParameter($variables_match[$lowKey],$variablesValues[$lowKey],$component,$header_text,$contact);
                        unset($component['text']);
                        unset($component['format']);
                        unset($component['example']);
                        array_push($APIComponents,$component);
                    }
                    
                }else if($component['type']=="BODY"){
                    $content=$component['text'];
                    $component['parameters']=[];
                    if(isset($variables_match[$lowKey])){
                        $this->setParameter($variables_match[$lowKey],$variablesValues[$lowKey],$component,$content,$contact);
                        unset($component['text']);
                        unset($component['format']);
                        unset($component['example']);
                        array_push($APIComponents,$component);
                    }
                    
                }else if(($component['type']=="HEADER"&&$component['format']=="DOCUMENT")){
                    $component['parameters']=[[
                        "type"=> "document",
                        "document"=>[
                            'link'=>$this->media_link
                        ]
                    ]];
                    $header_document=$this->media_link;
                    unset($component['format']);
                    unset($component['example']);
                    array_push($APIComponents,$component);
                }else if(($component['type']=="HEADER"&&$component['format']=="IMAGE")){
                    $component['parameters']=[[
                        "type"=> "image",
                        "image"=>[
                            'link'=>$this->media_link
                        ]
                    ]];
                    $header_image=$this->media_link;
                    unset($component['format']);
                    unset($component['example']);
                    array_push($APIComponents,$component);
                }else if(($component['type']=="HEADER"&&$component['format']=="VIDEO")){
                    $component['parameters']=[[
                        "type"=> "video",
                        "video"=>[
                            'link'=>$this->media_link
                        ]
                    ]];
                    $header_video=$this->media_link;
                    unset($component['format']);
                    unset($component['example']);
                    array_push($APIComponents,$component);
                }else if(($component['type']=="HEADER"&&$component['format']=="AUDIO")){
                    $component['parameters']=[[
                        "type"=> "audio",
                        "audio"=>[
                            'link'=>$this->media_link
                        ]
                    ]];
                    $header_audio=$this->media_link;
                    unset($component['format']);
                    unset($component['example']);
                    array_push($APIComponents,$component);
                }else if($component['type']=="FOOTER"){
                    $footer=$component['text'];
                }else if( $component['type']=="BUTTONS"){
                    $keyButton=0;
                    foreach ($component['buttons'] as $keyButtonFromLoop => $valueButton) {
                    
                         if(isset($variables_match[$lowKey][$keyButton]) && (($valueButton['type']=="URL"&&stripos($valueButton['url'], "{{") !== false) || ($valueButton['type']=="COPY_CODE")) ){
                            $buttonName="";
                            $button=[
                                "type"=>"button",
                                "sub_type"=>strtolower($valueButton['type']),
                                "index"=>$keyButtonFromLoop."",
                                "parameters"=>[]
                            ]; 
                            $paramType="text";
                            if($valueButton['type']=="COPY_CODE"){
                                $paramType="coupon_code";
                            }
                            
                           
                            $this->setParameter($variables_match[$lowKey][$keyButton],$variablesValues[$lowKey][$keyButton],$button,$buttonName,$contact,$paramType);
                
                            
                            array_push($APIComponents,$button);
                            array_push($buttons,$valueButton);
                            $keyButton++;
                         }else if($valueButton['type']=="FLOW"){
                            $button=[
                                "type"=>"button",
                                "sub_type"=>strtolower($valueButton['type']),
                                 "index"=>$keyButtonFromLoop."",
                                "parameters"=>[]
                            ];
                            $keyButton++;
                            array_push($APIComponents,$button);
                            array_push($buttons,$valueButton);
                         }else{
                            array_push($buttons,$valueButton);
                         }
                         
                    }
                    
                }

                
            }
            $components=$APIComponents;

            $dataToSend=[
                "contact_id"=>$contact->id,
                "company_id"=>$contact->company_id,
                "value"=>$content,
                "header_image"=>$header_image,
                "header_video"=>$header_video,
                "header_audio"=>$header_audio,
                "header_document"=>$header_document,
                "footer_text"=>$footer,
                "buttons"=>json_encode($buttons),
                "header_text"=>$header_text,
                "is_message_by_contact"=>false,
                "is_campign_messages"=>true,
                "status"=>0,
                "created_at"=>now(),
                "scchuduled_at"=>$sendTime,
                "components"=>json_encode($components),
                "campaign_id"=>$this->id,
            ];

            if(config('settings.is_demo',false)){
                //Demo
                if(count($messages)<5){
                    //Allow, but let it know
                    $dataToSend['value']="[THIS IS DEMO] ".$dataToSend['value'];
                    array_push($messages,$dataToSend);
                }
                
            }else{
                //Production
                array_push($messages,$dataToSend);
            }

            
        }

        $chunkedMessages = array_chunk($messages, 500);
        foreach ($chunkedMessages as $chunk) {
            Message::insert($chunk);
        }
        

        if($contact!=null){
            //This was a single message from bot
            //Get the last message id
            return Message::where('contact_id',$contact->id)->where('campaign_id',$this->id)->orderBy('id','desc')->first();
        }
    }

    private function setParameter($variables,$values,&$component,&$content,$contact,$type="text"){
        foreach ($variables as $keyVM => $vm) { 
            $data=["type"=>$type];
            if($vm=="-2"){
                //Use static value
                $data[$type]=$values[$keyVM];
                array_push($component['parameters'],$data);
                $content=str_replace("{{".$keyVM."}}",$values[$keyVM],$content);
                
            }else if($vm=="-3"){
                //Contact extra value in runtime
                try {
                    $extraValueNeeded = $values[$keyVM]; // ex "order.id"
                    $extraValues = $contact->extra_value; //ex ["order"=>["id"=>1,"status"=>"pending"]]
                    $valueNeeded = null;

                    if (isset($extraValues)) {
                        $keys = explode('.', $extraValueNeeded);
                        $valueNeeded = $extraValues;
                        

                        foreach ($keys as $key) {
                            if (isset($valueNeeded[$key])) {
                                $valueNeeded = $valueNeeded[$key];
                            } else {
                                $valueNeeded = $values[$keyVM];
                                break;
                            }
                        }
                    }
                 

                    $data[$type] = $valueNeeded;
                    array_push($component['parameters'], $data);
                    $content = str_replace("{{" . $keyVM . "}}", $valueNeeded, $content);

                    
                } catch (\Throwable $th) {
                    //Use static value
                    $data[$type]=$values[$keyVM];
                    array_push($component['parameters'],$data);
                    $content=str_replace("{{".$keyVM."}}",$values[$keyVM]."---",$content);
                }
               
            }else if($vm=="-1"){
                //Contact name
                $data[$type]=$contact->name;
                array_push($component['parameters'],$data);
                $content=str_replace("{{".$keyVM."}}",$contact->name,$content);
            }else if($vm=="0"){
                //Contact phone
                $data[$type]=$contact->phone;
                array_push($component['parameters'],$data);
                $content=str_replace("{{".$keyVM."}}",$contact->phone,$content);
            }else{
                //Use defined contact field
                if($contact->fields->where('id',$vm)->first()){
                    $val=$contact->fields->where('id',$vm)->first()->pivot->value;
                    $data[$type]=$val;
                    array_push($component['parameters'],$data);
                    $content=str_replace("{{".$keyVM."}}",$val,$content);
                }else{
                    $data[$type]="";
                    array_push($component['parameters'],$data);
                    $content=str_replace("{{".$keyVM."}}","",$content);
                }
            }
        }
    }

    
}
