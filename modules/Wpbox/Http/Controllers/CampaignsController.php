<?php

namespace Modules\Wpbox\Http\Controllers;


use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Contacts\Models\Group;
use Modules\Wpbox\Models\Contact;
use Modules\Contacts\Models\Field;
use Modules\Wpbox\Jobs\SendMessage;
use Modules\Wpbox\Models\Campaign;
use Modules\Wpbox\Models\Message;
use Modules\Wpbox\Models\Template;
use Modules\Wpbox\Traits\Whatsapp;
use Illuminate\Support\Facades\Log;

class CampaignsController extends Controller
{
    use Whatsapp;

    /**
     * Provide class.
     */
    private $provider = Campaign::class;

    /**
     * Web RoutePath for the name of the routes.
     */
    private $webroute_path = 'campaigns.';

    /**
     * View path.
     */
    private $view_path = 'wpbox::campaigns.';

    /**
     * Parameter name.
     */
    private $parameter_name = 'campaigns';

    /**
     * Title of this crud.
     */
    private $title = 'campaign';

    /**
     * Title of this crud in plural.
     */
    private $titlePlural = 'campaigns';


    public function index()
    {

        $this->authChecker();

        if($this->getCompany()->getConfig('whatsapp_webhook_verified','no')!='yes' || $this->getCompany()->getConfig('whatsapp_settings_done','no')!='yes'){
            return redirect(route('whatsapp.setup'));
         }

        $items = $this->provider::orderBy('id', 'desc')->whereNull('contact_id')->where('is_bot', false)->where('is_bot', false)->where('is_api', false)->where('is_reminder', false);
        if(isset($_GET['name'])&&strlen($_GET['name'])>1){
            $items=$items->where('name',  'like', '%'.$_GET['name'].'%');
        }
        $items=$items->paginate(100);
        

        return view($this->view_path.'index', [ 'total_contacts'=>Contact::count(),
        'setup' => [
           
            'title'=>__('crud.item_managment', ['item'=>__($this->titlePlural)]),
            'iscontent'=>true,
            'action_link'=>route($this->webroute_path.'create'),
            'action_name'=>__('Send new campaign')." 📢",
            'action_link2'=>route('wpbox.api.index', ['type' => 'api']),
            'action_name2'=>__('Manage API campaigns'),
            'items'=>$items,
            'item_names'=>$this->titlePlural,
            'webroute_path'=>$this->webroute_path,
            'fields'=>[],
            'custom_table'=>true,
            'parameter_name'=>$this->parameter_name,
            'parameters'=>count($_GET) != 0
        ]]);
    }

    public function show(Campaign $campaign){

        //Get countries we have send to
        $contact_ids=$campaign->messages()->select(['contact_id'])->pluck('contact_id')->toArray();
        $countriesCount = DB::table('contacts')
        ->join('countries', 'contacts.country_id', '=', 'countries.id')
        ->selectRaw('count(contacts.id) as number_of_messages, country_id, countries.name, countries.lat, countries.lng')
        ->whereIn('contacts.id',$contact_ids)
        ->groupBy('contacts.country_id')
        ->get()->toArray();
 
        $dataToSend=[ 
            'total_contacts'=>Contact::count(),
            'item'=>$campaign,
        'setup' => [
            'countriesCount'=>$countriesCount,
            'title'=>__('Campaign')." ".$campaign->name,
            'action_link'=>route($this->webroute_path.'index'),
            'action_name'=>"📢 ".__('Back'),
            'items'=>$campaign->messages()->paginate(config('settings.paginate')),
            'item_names'=>$this->titlePlural,
            'webroute_path'=>$this->webroute_path,
            'fields'=>[],
            'custom_table'=>true,
            'parameter_name'=>$this->parameter_name,
            'parameters'=>count($_GET) != 0
        ]];

        if($campaign->is_bot){
            $dataToSend['setup']['title']=__('Bot')." ".$campaign->name;
            $dataToSend['setup']['action_name']=__('Back to bots')." 🤖";
            $dataToSend['setup']['action_link']=route('replies.index',['type'=>'bot']);
        }else if($campaign->is_api){
            $dataToSend['setup']['title']=__('API')." ".$campaign->name;
            $dataToSend['setup']['action_name']=__('Back to Api');
            $dataToSend['setup']['action_link']=route('wpbox.api.index',['type'=>'api']);
        }else{
            //Regular campaign
            //If there is at lease 1 pending message, show action to pause campaign
            $pendingMessages=$campaign->messages()->where('status',0)->count();
            if($pendingMessages>0 && $campaign->is_active){
                 $dataToSend['setup']['action_link2']=route($this->webroute_path.'pause',$campaign->id);
                $dataToSend['setup']['action_name2']="⏸️ ".__('Pause campaign');
            }else if($pendingMessages>0){
                $dataToSend['setup']['action_link2']=route($this->webroute_path.'resume',$campaign->id);
                $dataToSend['setup']['action_name2']="▶️ ".__('Resume campaign');
            }

            $dataToSend['setup']['action_link3']=route($this->webroute_path.'report',$campaign->id);
            $dataToSend['setup']['action_name3']="📊 ".__('Download report');
        }

        
        return view($this->view_path.'show',$dataToSend );
    }

    /**
     * Auth checker function for the crud.
     */
    private function authChecker()
    {
        $this->ownerAndStaffOnly();
    }

    private function componentToVariablesList($template){
        $jsonData = json_decode($template->components, true);

        $variables = [];
        foreach ($jsonData as $item) {

            if($item['type']=="HEADER"&&$item['format']=="TEXT"){
                preg_match_all('/{{(\d+)}}/', $item['text'], $matches);  
                if (!empty($matches[1])) {
                    foreach ($matches[1] as $id) {
                        $exampleValue ="";
                        try {
                            $exampleValue = $item['example']['header_text'][$id - 1];
                        } catch (\Throwable $th) {
                        }
                        $variables['header'][] = ['id' => $id, 'exampleValue' => $exampleValue];
                    }
                }
            }else if($item['type']=="HEADER"&&$item['format']=="DOCUMENT"){
                $variables['document']=true;
            }else if($item['type']=="HEADER"&&$item['format']=="IMAGE"){
                $variables['image']=true;
            }else if($item['type']=="HEADER"&&$item['format']=="VIDEO"){
                $variables['video']=true;
            }else if($item['type']=="BODY"){
                preg_match_all('/{{(\d+)}}/', $item['text'], $matches);  
                if (!empty($matches[1])) {
                    foreach ($matches[1] as $id) {
                        $exampleValue ="";
                        try {
                            $exampleValue = $item['example']['body_text'][0][$id - 1];
                        } catch (\Throwable $th) {
                        }
                        $variables['body'][] = ['id' => $id, 'exampleValue' => $exampleValue];
                    }
                }
            }else if($item['type']=="BUTTONS"){
                foreach ($item['buttons'] as $keyBtn => $button) {
                    if($button['type']=="URL"){
                        preg_match_all('/{{(\d+)}}/', $button['url'], $matches);  
                   
                        if (!empty($matches[1])) {
                        
                            foreach ($matches[1] as $id) {
                                $exampleValue ="";
                                try {
                                    $exampleValue = $button['url'];
                                    $exampleValue = str_replace("{{1}}", "", $exampleValue );
                                } catch (\Throwable $th) {
                                }
                                $variables['buttons'][$id - 1][] = ['id' => $id, 'exampleValue' => $exampleValue,'type'=>$button['type'],'text'=>$button['text']];
                            }
                        }
                    }
                    if($button['type']=="COPY_CODE"){
                        $exampleValue = $button['example'][0];
                        $variables['buttons'][$keyBtn][] = ['id' => $keyBtn, 'exampleValue' => $exampleValue,'type'=>$button['type'],'text'=>$button['text']];
                    }
                    
                }
               
            }
        }
        return $variables;
    }

    public function create(Request $request){
        $templates=[];
        foreach (Template::where('status','APPROVED')->get() as $key => $template) {
            $templates[$template->id]=$template->name." - ".$template->language;
        }
        if(sizeof($templates)==0){
           //If there are 0 template,re-load them
            try {
                $this->loadTemplatesFromWhatsApp();
                foreach (Template::where('status','APPROVED')->get() as $key => $template) {
                    $templates[$template->id]=$template->name." - ".$template->language;
                }
            } catch (\Throwable $th) {
                //throw $th;
            }
        }
        
         

        if(sizeof($templates)==0){
            //Redirect to templates
            return redirect()->route('templates.index')->withStatus(__('Please add a template first. Or wait some to be approved'));
        }
    
        $groups=Group::pluck('name','id');
        $groups[0]=__("Send to all contacts");

        $selectedTemplate=null;
        $variables=null;
        if(isset($_GET['template_id'])){
            $selectedTemplate=Template::withoutGlobalScope(\App\Scopes\CompanyScope::class)->where('id',$_GET['template_id'])->first();
            $variables=$this->componentToVariablesList($selectedTemplate);
            
        }

        $isApiCampaignMaker=$request->has('type') && $request->type === 'api';
        $isReminderCampaignMaker=$request->has('type') && $request->type === 'reminder';
        
        $contactFields=[];
        if($isApiCampaignMaker){
            $contactFields[-3]=__('Use API defined value');
        }

        if($isReminderCampaignMaker){
           //Add Start date, Start time, Start Date And time, End date, End time and End date and time
            $contactFields[-4] = __('Start date');
            $contactFields[-5] = __('Start time');
            $contactFields[-6] = __('Start date and time');
            $contactFields[-7] = __('End date');
            $contactFields[-8] = __('End time');
            $contactFields[-9] = __('End date and time');
            $contactFields[-10] = __('External ID');
        }
        
        $contactFields[-2]=__('Use manually defined value');
        $contactFields[-1]=__('Contact name');
        $contactFields[0]=__('Contact phone');
        foreach (Field::pluck('name','id') as $key => $value) {
            $contactFields[$key]=$value;
        }

        $selectedContacts = 0;
        if (isset($_GET['group_id'])) {
            if ($_GET['group_id'] == "0") {
                $selectedContacts = Contact::where('subscribed', 1)->count();
            } else {
                $group = Group::findOrFail($_GET['group_id']);
                $selectedContacts = $group->contacts()->where('subscribed', 1)->count();
            }
        }


        $dataToSend=[
            'selectedContacts'=>$selectedContacts,
            'selectedTemplate'=>$selectedTemplate,
            'selectedTemplateComponents'=>$selectedTemplate?json_decode($selectedTemplate->components,true):null,  
            'contactFields'=> $contactFields,
            'variables'=>$variables,
            'groups' => $groups,
            'contacts' => Contact::pluck('name','id'),
            'templates' => $templates,
            'isBot' => $request->has('type') && $request->type === 'bot',
            'isAPI' => $isApiCampaignMaker,
            'isReminder'=>$isReminderCampaignMaker
        ];

        if($isReminderCampaignMaker){
            $dataToSend['sources']=\Modules\Reminders\Models\Source::pluck('name','id');
            //Prepend the all source
            $dataToSend['sources'] = collect([0 => __('All')])->union($dataToSend['sources']);
        }

        return view($this->view_path.'create', $dataToSend);
    }


    public function store(Request $request) {  
        //Create the campaign
        $campaign = $this->provider::create([
            'name'=>$request->has('name') ? $request->name:"template_message_".now(),
            'timestamp_for_delivery'=>$request->has('send_now')?null:$request->send_time,
            'variables'=>$request->has('paramvalues')?json_encode($request->paramvalues):"",
            'variables_match'=>json_encode($request->parammatch),
            'template_id'=>$request->template_id,
            'group_id'=>$request->group_id.""=="0"?null:$request->group_id,
            'contact_id'=>$request->contact_id,
            'total_contacts'=>Contact::count(),
        ]);

        //Check if type is bot
        $isBot=$request->has('type') && $request->type === 'bot';
        if($isBot) {
            $campaign->is_bot = true;
            $campaign->bot_type= $request->reply_type;
            $campaign->trigger= $request->trigger;
            $campaign->save();
        }

        $isAPI=$request->has('type') && $request->type === 'api';
        if($isAPI) {
            $campaign->is_api = true;
            $campaign->save();
        }

        $isReminder=$request->has('type') && $request->type === 'reminder';
        if($isReminder) {
            $campaign->is_reminder = true;
            $campaign->save();

            //Create the reminder
            $reminder = \Modules\Reminders\Models\Remineder::create([
                'campaign_id' => $campaign->id,
                'name' => $request->has('name') ? $request->name:"template_message_".now(),
                'source_id' => $request->source_id == 0 ? null : $request->source_id,
                'type' => $request->reminder_type,
                'time' => $request->reminder_time,
                'time_type' => $request->reminder_unit,
                'status' => 1,
            ]);
        }

        if ($request->hasFile('pdf')) {
            $campaign->media_link = $this->saveDocument(
                "",
                $request->pdf,
            );
            $campaign->update();
        }
        if ($request->hasFile('imageupload')) {
            $campaign->media_link = $this->saveDocument(
                "",
                $request->imageupload,
            );
            $campaign->update();
        }

    

        
         if($isBot) {
            //Bot campaign
            return redirect()->route('replies.index',['type'=>'bot'])->withStatus(__('You have created a new bot.'));
         } else if($isAPI) {
            //API campaign
            return redirect()->route('wpbox.api.index',['type'=>'api'])->withStatus(__('You have created new API Campaigns.'));
         }
         else if($isReminder) {
            //Reminder campaign
            return redirect()->route('reminders.reminders.index')->withStatus(__('You have created a new reminder.'));
         }
         else{
            //Regular campaign
            //Make the actual messages
            $campaign->makeMessages($request);

            if($request->has('contact_id')){
                return redirect()->route('chat.index')->withStatus(__('Message will be send shortly. Please note that if new contact, it will not appear in this list until the contact start interacting with you!'));
            }else{
                return redirect()->route($this->webroute_path.'index')->withStatus(__('Campaign is ready to be send'));
            }
         }
        

       
    
    }

   

    public function sendSchuduledMessages(){
        //Find all unsent Messages that are within the timeline
        $limit=100;

        //campaign_sending_batch
        try {
            $limit = (int) config('wpbox.campaign_sending_batch', 100);

            //Limit must be number
            if(!is_numeric($limit)){
                $limit=100;
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
        $messagesToBeSend = Message::where('status', 0)
            ->where('scchuduled_at', '<', Carbon::now())
            ->whereIn('campaign_id', function ($query) {
                $query->select('id')
                    ->from('wa_campaings')
                    ->where('is_active', true);
            })
            ->limit($limit)
            ->get();
        foreach ( $messagesToBeSend as $key => $message) {
            if(config('wpbox.campaign_sending_type','normal')=="normal"){
                //Old way - send all at once
                $this->sendCampaignMessageToWhatsApp($message);
            }else{
                dispatch(new SendMessage($message));
            }
        }

    }

    //Delete campaign, only if type is BOT
    public function destroy(Campaign $campaign){
        if($campaign->is_bot || $campaign->is_api){
            $campaign->delete();
            //Redirect based on campaign type
            if($campaign->is_api){
                return redirect()->route('wpbox.api.index',['type'=>'api'])->withStatus(__('API Campaign deleted'));
            }
            return redirect()->route('replies.index',['type'=>'bot'])->withStatus(__('Bot deleted'));
        }else{
            return redirect()->route($this->webroute_path.'index')->withStatus(__('You can only delete bot campaigns'));
        }
    }

    //Activate bot
    public function activateBot(Campaign $campaign){
        $campaign->is_bot_active=true;
        $campaign->save();
        return redirect()->route('replies.index',['type'=>'bot'])->withStatus(__('Bot activated'));
    }

    //Deactivate bot
    public function deactivateBot(Campaign $campaign){
        $campaign->is_bot_active=false;
        $campaign->save();
        return redirect()->route('replies.index',['type'=>'bot'])->withStatus(__('Bot deactivated'));
    }

    //Pause campaign
    public function pause(Campaign $campaign){
        $campaign->is_active=false;
        $campaign->save();
        return redirect()->route($this->webroute_path.'show',$campaign)->withStatus(__('Campaign paused'));
    }

    //Resume campaign
    public function resume(Campaign $campaign){
        $campaign->is_active=true;
        $campaign->save();
        return redirect()->route($this->webroute_path.'show',$campaign)->withStatus(__('Campaign resumed'));
    }

    //Download report
    public function report(Campaign $campaign){
        $filename = "report_campaign_".$campaign->id."_".now().".csv";
        $handle = fopen($filename, 'w+');
        fputcsv($handle, array('Name', 'Phone', 'Country', 'Status', 'Sent at', 'Last status update','Extra'));
        foreach ($campaign->messages as $key => $message) {
            //Status
            $status = "";
            $error = $message->error;
            if($message->status==0){
                $status = "PENDING_SENT";
            }else if($message->status==1 || $message->status=2){
                $status = "SENT";
            }else if($message->status==3){
                $status = "DELIVERED";
            }else if($message->status==4){
                $status = "READ";
            }else if($message->status==5){
                $status = "FAILED";
            }
            try {
                fputcsv($handle, array($message->contact->name, $message->contact->phone, $message->contact->country->name, $status, $message->scchuduled_at?$message->scchuduled_at:$message->created_at, $message->updated_at,$error));
            } catch (\Throwable $th) {
                //throw $th;
            }
           
        }
        fclose($handle);
        $headers = array(
            'Content-Type' => 'text/csv',
        );

        return response()->download($filename, $filename, $headers)->deleteFileAfterSend(true);
    }    

}