<?php

namespace Modules\Wpbox\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Wpbox\Models\Contact;
use Modules\Wpbox\Models\Message;
use Illuminate\Support\Facades\Storage;
use Modules\Wpbox\Models\Reply;
use Modules\Wpbox\Models\Template;
use Modules\Wpbox\Traits\Whatsapp;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Locale;
use Modules\Wpbox\Events\Chatlistchange;
use ResourceBundle;
use Akaunting\Module\Facade as Module;
use Illuminate\Support\Facades\Log;


class ChatController extends Controller
{
    use Whatsapp;

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if($this->getCompany()->getConfig('whatsapp_webhook_verified','no')!='yes' || $this->getCompany()->getConfig('whatsapp_settings_done','no')!='yes'){
            return redirect(route('whatsapp.setup'));
         }
        $templates=Template::where('status','APPROVED')->select('name','id','language')->get();
        $replies = Reply::where('type', 1)->where('flow_id', null)->get();

        $languages = explode(",",__('No translation').",".config('wpbox.available_languages','English,Spanish,German,Italian,Portuguese,Dutch,French,Japanese,Chinese'));
        
       
        
        //Find the users of the company
        $users=$this->getCompany()->users()->pluck('name','id');

        //Get all the modules where type is "link_fetcher"
        $fetcherModules = [];
        $sidebarModules = [];
        foreach (Module::all() as $key => $module) {
            if ($module->get('isLinkFetcher')) {
                try{
                    $fetcherModules[$module->get('alias')] = [
                        'name' => $this->getCompany()->getConfig($module->get('alias') . '_button_name', __('No name')),
                        'data' => app($module->get('namespace').'\Main')->getData(),
                    ];
                }catch(\Exception $e){
                    //Do nothing
                    //dd($e);
                }
            }
            if ($module->get('hasSidebar')) {
                try{
                    foreach ($module->get('sidebarData') as $sidebarApp) {
                        $sidebarModules[] = [
                            'alias' => $sidebarApp['app'],
                            'name' => $sidebarApp['name'],
                            'brandColor' => $sidebarApp['brandColor'] ?? '#96588A',
                            'icon' => $sidebarApp['icon'], 
                            'view' => $sidebarApp['view'],
                            'script' => $sidebarApp['script']
                        ];
                    }
                }catch(\Exception $e){
                    //Do nothing
                    //dd($e);
                }
            }
        }
       

        //Sort the sidebar modules, so that "Contact" is first ,"AI Message Style" is second, and the rest in alphabetical order
        usort($sidebarModules, function($a, $b) {
            if ($a['name'] === 'Contact') return -1;
            if ($b['name'] === 'Contact') return 1;
            return strcmp($a['name'], $b['name']);
        });

        return view('wpbox::chat.master',[
            'company'=>$this->getCompany(),
            'templates'=>$templates->toArray(),
            'replies'=>$replies->toArray(),
            'users'=>$users->toArray(),
            'languages'=>$languages,
            'fetcherModules'=>$fetcherModules,
            'sidebarModules'=>$sidebarModules,
        ]);
    }






    /**
     * API
     */
    public function chatlist($lastmessagetime,$page=1,$search_query=""){
        //Number of chats to return per page
        $pageSize=config('wpbox.chat_page_size', 6);
        
        $shouldWeReturnChats=true;

        if($shouldWeReturnChats){
            //Return list of contacts that have chat actives
            //check if current user in agent
            $numberOfPages=1;
            if(Auth::user()->hasRole('staff') && $this->getCompany()->getConfig('agent_assigned_only','false')!='false' ){
                $chatList=Contact::where('has_chat',1)->where('user_id',Auth::user()->id)->with(['messages','country'])->orderBy('last_reply_at','DESC');
            }else{
                $chatList=Contact::where('has_chat',1)->with(['messages','country'])->orderBy('last_reply_at','DESC');
            }

            
            //Total number of chats
            $numberOfChats=$chatList->count();

            //Mine chats
            $myChatsCount=Contact::where('has_chat',1)->where('user_id',Auth::user()->id)->count();

            //We also need to know the total number of pages, from the total number of chats
            $numberOfPages=ceil($chatList->count()/$pageSize);

            //Unread chats
            if(Auth::user()->hasRole('staff') && $this->getCompany()->getConfig('agent_assigned_only','false')!='false' ){
                $unreadChatsCount=Contact::where('has_chat',1)->where('user_id',Auth::user()->id)->where('is_last_message_by_contact',1)->count();
            }else{
                $unreadChatsCount=Contact::where('has_chat',1)->where('is_last_message_by_contact',1)->count();
            }

            //Resolved chats count
            if(Auth::user()->hasRole('staff') && $this->getCompany()->getConfig('agent_assigned_only','false')!='false' ){
                $resolvedChatsCount=Contact::where('has_chat',1)->where('user_id',Auth::user()->id)->where('resolved_chat',1)->count();
            }else{
                $resolvedChatsCount=Contact::where('has_chat',1)->where('resolved_chat',1)->count();
            }

            //Query, also by last_message
            if($search_query!=""&&strlen($search_query)>3){
                $chatList=$chatList->where(function($query) use ($search_query) {
                    $query->where('name','like','%'.$search_query.'%')
                          ->orWhere('phone','like','%'.$search_query.'%')
                          ->orWhereHas('messages',function($q) use ($search_query){
                              $q->where('value','like','%'.$search_query.'%');
                          });
                });
            }

            //Filter by resolved status if requested
            if(request()->has('filter') && request()->filter == 'resolved') {
                $chatList = $chatList->where('resolved_chat', 1);
            }

            //Now get the chats for the current page
            $chatList=$chatList->skip(($page-1)*$pageSize)->limit($pageSize)->get();

           
            return response()->json([
                'data' => $chatList,
                'numberOfPages' => $numberOfPages,
                'page' => $page,
                'totalChats' => $numberOfChats,
                'myChatsCount' => $myChatsCount,
                'unreadChatsCount' => $unreadChatsCount,
                'newMessagesCount' => $unreadChatsCount,
                'resolvedChatsCount' => $resolvedChatsCount,
                'status' => true,
                'errMsg' => '',
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errMsg' => 'No changes',
            ]);
        }
        
    }

    public function setLanguage(Request $request, Contact $contact)
    {
        // Validate the request...
        $validatedData = $request->validate([
            'language' => 'required|string',
        ]);

        // Assign the contact to the user
        $contact->language = $validatedData['language'];

        if(__('No translation')==$validatedData['language']){
            $contact->language = 'none';
        }
        $contact->save();

        return response()->json([
            'status' => true,
            'message' => 'Language set successfully',
        ]);
    }

    public function assignContact(Request $request, Contact $contact)
    {
        // Validate the request...
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Assign the contact to the user
        $contact->user_id = $validatedData['user_id'];
        $contact->save();

        event(new Chatlistchange($contact->id,$contact->company_id)); 

        return response()->json([
            'status' => true,
            'message' => 'Contact assigned successfully',
        ]);
    }

    public function chatmessages($contact){
        //Find the contact
        try{
            $contactUser=Contact::where('id',$contact)->first();
            if($contactUser){
                $contactUser->is_last_message_by_contact=0;
                $contactUser->update();
            }
        }catch(\Exception $e){
            //Do nothing
        }
        
        $messages=Message::where('contact_id',$contact)->where('status','>',0)->orderBy('id','desc')->limit(50)->get();
        return response()->json([
            'data' =>  $messages,
            'status' => true,
            'errMsg' => '',
        ]);
    }

    public function sendNoteToContact(Request $request, Contact $contact){
        /**
         * Contact id
         * Message
         */
        $validator = Validator::make($request->all(), [
            'note' => 'required|string'
        ]);

        if ($validator->fails()) {
            $errorsText = $validator->errors()->all();
            // Convert the array of error messages to a single string
            $errorsString = implode("\n", $errorsText);
            return response()->json([
                'status' => false,
                'errMsg' => $errorsString,
            ]);
        } else {
            // OK, we can send the note
            $note = $request->input('note');
            $contact->addNote($note);

            return response()->json([
                'status' => true,
                'message' => 'Note added successfully',
            ]);
        }
    }

    public function sendMessageToContact(Request $request, Contact $contact){
        /**
         * Contact id
         * Message
         */

        // Create a validator instance
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:500'
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            $errorsText = $validator->errors()->all();
            // Convert the array of error messages to a single string
            $errorsString = implode("\n", $errorsText);
            return response()->json([
                'status' => false,
                'errMsg' => $errorsString,
            ]);
        }else if(strip_tags($request->message)!=$request->message){
            return response()->json([
                'status' => false,
                'errMsg' => __('Only text is allowed!'),
            ]);
        }else{
            //OK, we can send the message
            $messageSend=$contact->sendMessage(strip_tags($request->message),false);
            return response()->json([
                'message'=> $messageSend,
                'messagetime'=>$messageSend->created_at->format('Y-m-d H:i:s'),
                'status' => true,
                'errMsg' => '',
            ]);
        }



        
    }

    public function sendImageMessageToContact(Request $request, Contact $contact){
        /**
         * Contact id
         * Message
         */
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
       
        $messageSend=$contact->sendMessage($imageUrl,false,false,$messageType);
        return response()->json([
            'message'=> $messageSend,
            'messagetime'=>$messageSend->created_at->format('Y-m-d H:i:s'),
            'status' => true,
            'errMsg' => '',
        ]);
    }

    public function sendDocumentMessageToContact(Request $request, Contact $contact){
        /**
         * Contact id
         * Message
         */
        $fileURL="";
        if(config('settings.use_s3_as_storage',false)){
            //S3 - store per company
            $path = $request->file->storePublicly('uploads/media/send/'.$contact->company_id,'s3',);
            $fileURL = Storage::disk('s3')->url($path);
        }else{
            //Regular
            $path = $request->file->store(null,'public_media_upload',);
            $fileURL = Storage::disk('public_media_upload')->url($path);
        }

        $messageSend=$contact->sendMessage($fileURL,false,false,"DOCUMENT");
        return response()->json([
            'message'=> $messageSend,
            'messagetime'=>$messageSend->created_at->format('Y-m-d H:i:s'),
            'status' => true,
            'errMsg' => '',
        ]);
    }

    public function updateChatStatus(Request $request, Contact $contact) {
        // Update the resolved_chat status
        $contact->resolved_chat = 1;
        $contact->save();

        event(new Chatlistchange($contact->id, $contact->company_id));

        return response()->json([
            'status' => true,
            'message' => 'Chat status updated successfully'
        ]);
    }

    public function reopenChat(Request $request, Contact $contact) {
        // Update the resolved_chat status to reopen
        $contact->resolved_chat = 0;
        $contact->save();

        event(new Chatlistchange($contact->id, $contact->company_id));

        return response()->json([
            'status' => true,
            'message' => 'Chat reopened successfully'
        ]);
    }
}
