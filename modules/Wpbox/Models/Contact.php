<?php

namespace Modules\Wpbox\Models;

use App\Models\Company;
use Illuminate\Support\Facades\Log;
use Modules\Contacts\Models\Contact as ModelsContact;
use Modules\Contacts\Models\Group;
use Modules\Wpbox\Events\AgentReplies;
use Modules\Wpbox\Events\ContactReplies;
use Modules\Wpbox\Notifications\ContactReplies as NotificationContactReplies;
use Modules\Wpbox\Events\Chatlistchange;
use Modules\Wpbox\Traits\Whatsapp;

class Contact extends ModelsContact
{
    use Whatsapp;

    public function messages()
    {
        return $this->hasMany(
                Message::class
            )->orderBy('created_at','DESC');
    }

    public function notes()
    {
        return $this->hasMany(
                Message::class
            )->where('is_note', true)->orderBy('created_at','DESC');
    }

    public function trimString($str, $maxLength) {
        if (mb_strlen($str) <= $maxLength) {
            return $str;
        } else {
            $trimmed = mb_substr($str, 0, $maxLength);
            $lastSpaceIndex = mb_strrpos($trimmed, ' ');
    
            if ($lastSpaceIndex !== false) {
                return mb_substr($trimmed, 0, $lastSpaceIndex) . '...';
            } else {
                return $trimmed . '...';
            }
        }
    }

    

    
    public function sendDemoMessage($content,$is_message_by_contact=true,$is_campaign_messages=false,$messageType="TEXT",$fb_message_id=null){
        //Check that all is set ok

        //Create the messages
         $messageToBeSend=Message::create([
            "contact_id"=>$this->id,
            "company_id"=>$this->company_id,
            "value"=>$messageType=="TEXT"?$content:"",
            "header_image"=>$messageType=="IMAGE"?$content:"",
            "header_document"=>$messageType=="DOCUMENT"?$content:"",
            "header_video"=>$messageType=="VIDEO"?$content:"",
            "header_location"=>$messageType=="LOCATION"?$content:"",
            "is_message_by_contact"=>$is_message_by_contact,
            "is_campign_messages"=>$is_campaign_messages,
            "status"=>1,
            "buttons"=>"[]",
            "components"=>"",
            "fb_message_id"=>$fb_message_id
         ]);
         $messageToBeSend->save();

         
    }

    public function addNote($content){
        $messageToBeSend=Message::create([
            "contact_id"=>$this->id,
            "company_id"=>$this->company_id,
            "value"=>$content,
            "header_text"=>__('Note'),
            "header_image"=>"",
            "header_document"=>"",
            "header_video"=>"",
            "header_location"=>"",
            "is_message_by_contact"=>false,
            "is_campign_messages"=>false,
            "status"=>4,
            "buttons"=>"[]",
            "components"=>"",
            "is_note"=>true,
            "fb_message_id"=>null
         ]);
         if(auth()->check()){
            $messageToBeSend->sender_name=auth()->user()->name;
        } 
         $messageToBeSend->save();

         $companyUser=Company::findOrFail($this->company_id)->user;
         event(new AgentReplies($companyUser,$messageToBeSend,$this));

         return $messageToBeSend;
    }



    /**
     * $reply - Reply - The reply to be send
     */
    public function sendReply(Reply $reply){
        //Create the message
        $buttons=[];

        for ($i=1; $i < 4; $i++) { 
            if ($reply->{"button".$i} != "") {
                $buttons[sizeof($buttons)] = [
                    "type" => "reply",
                    "reply" => [
                        "id" => $reply->{"button".$i."_id"},
                        "title" => $reply->{"button".$i}
                    ]
                ];
            }
        }


        //If buttons is empty array
        $is_cta=false;
        if(sizeof($buttons)==0){
            //Check if we have set and not empty button_name and button_url
            if($reply->button_name &&  $reply->button_name!="" && $reply->button_url && $reply->button_url!=""){
                $is_cta=true;
                $buttons[0] = [
                    "name" => "cta_url",
                    "parameters" => [
                        "display_text" => $reply->button_name,
                        "url" => $reply->button_url
                    ]
                ];
            }

        }
        
 
       
        $createData=[
            "contact_id"=>$this->id,
            "company_id"=>$this->company_id,
            "value"=>$reply->text,
            "header_text"=>$reply->header,
            "footer_text"=>$reply->footer,
            "buttons"=>json_encode($buttons),
            "is_message_by_contact"=>false,
            "is_campign_messages"=>false,
            "status"=>1,
            "fb_message_id"=>null
        ];
        
        $messageToBeSend=Message::create($createData);
        $messageToBeSend->save();
        if($is_cta){
            $messageToBeSend->is_cta=true;
        }   
       
   

        $this->last_support_reply_at=now();
        $this->is_last_message_by_contact=false;
        $this->sendMessageToWhatsApp($messageToBeSend,$this);
        //Find the user of the company
        $companyUser=Company::findOrFail($this->company_id)->user;
        event(new AgentReplies($companyUser,$messageToBeSend,$this));

        $this->last_message=$this->trimString($reply->text,40);
        $this->update();

        return $messageToBeSend;

    }


    public function botReply($content,$messageToBeSend){
                //Reply bot
                $textReplies=Reply::where('type','!=',1)->where('company_id',$this->company_id)->get();
                $replySend=false;
                Log::info("textReplies",[$content]);
                foreach ($textReplies as $key => $qr) {
                    if(!$replySend){
                        $replySend=$qr->shouldWeUseIt($content,$this);
                    }
                }

                //If no text reply found, look for campaign reply
                if(!$replySend){
                    $campaignReplies=Campaign::where('is_bot',1)->where('is_bot_active',1)->where('company_id',$this->company_id)->get();
                    foreach ($campaignReplies as $key => $cr) {
                        if(!$replySend){
                            try {
                                $replySend=$cr->shouldWeUseIt($content,$this);
                            } catch (\Exception $e) {
                                Log::error('Error calling shouldWeUseIt:', ['exception' => $e->getMessage()]);
                            }
                        }
                    }
                }

                if($replySend){
                    $messageToBeSend->bot_has_replied=true;
                    $messageToBeSend->update();
                }

    }
    
    /**
     * $content- String - The content to be stored, text or link
     * $is_message_by_contact - Boolean - is this a message send by contact - RECEIVE
     * $is_campaign_messages - Boolean - is this a message generated from campaign
     * $messageType [TEXT | IMAGE | VIDEO | DOCUMENT ]
     * $fb_message_id String - The Facebook message ID
     */
    public function sendMessage($content,$is_message_by_contact=true,$is_campaign_messages=false,$messageType="TEXT",$fb_message_id=null,$extra=null){
        //Check that all is set ok

        //If message is from contact, and fb_message_id is set, check if the message is already in the system
        if($is_message_by_contact && $fb_message_id){
            //Set the resolved_chat to 1
            $this->resolved_chat=1;
            $this->update();

            $message=Message::where('fb_message_id',$fb_message_id)->first();
            if($message){
                return $message;
            }
        }

        //Create the messages
         $messageToBeSend=Message::create([
            "contact_id"=>$this->id,
            "company_id"=>$this->company_id,
            "value"=>$messageType=="TEXT"?$content:"",
            "header_image"=>$messageType=="IMAGE"?$content:"",
            "header_document"=>$messageType=="DOCUMENT"?$content:"",
            "header_video"=>$messageType=="VIDEO"?$content:"",
            "header_audio"=>$messageType=="AUDIO"?$content:"",
            "header_location"=>$messageType=="LOCATION"?$content:"",
            "is_message_by_contact"=>$is_message_by_contact,
            "is_campign_messages"=>$is_campaign_messages,
            "status"=>1,
            "buttons"=>"[]",
            "components"=>"",
            "fb_message_id"=>$fb_message_id
         ]);

        //Set the original message
        if($messageType=="TEXT"){
            $messageToBeSend->doTranslation($is_message_by_contact);
        }

        Log::info("messageToBeSend",[$messageToBeSend]);

        //Check who send the message
        if(!$is_message_by_contact){
            //Get current user
            if(auth()->check()){
                $messageToBeSend->sender_name=auth()->user()->name;
            } 
            

            //Check if the company has enough credits
            if(!$this->getCompany()->hasEnoughCreditsByAction("send_regular_message")){
               //Set the message to be sent as error
               $messageToBeSend->status=2;
               $messageToBeSend->error=__('No credits left');
               $messageToBeSend->save();
               return $messageToBeSend;
            }
            

           
        }
       

        $messageToBeSend->save();
        
       

        //Update the contact last message, time etc
        

        if(!$is_campaign_messages){
            $this->has_chat=true;
            $this->last_reply_at=now();
            if($is_message_by_contact){
                $this->last_client_reply_at=now();
                $this->is_last_message_by_contact=true;

                //Reply bots
                if($this->enabled_ai_bot){
                    $this->botReply($content,$messageToBeSend);
                }
               
                //Notify
                $messageToBeSend->extra=$extra;
                event(new ContactReplies(auth()->user(),$messageToBeSend,$this));


                //Send the notification
                //Get the company user
                try {
                    $companyAdmin=Company::findOrFail($this->company_id)->user;
                    $companyAdmin->notify(new NotificationContactReplies($companyAdmin,$messageToBeSend,$this));
                } catch (\Exception $e) {
                    
                }
               
                $messageToBeSend->extra=null;
                event(new Chatlistchange($this->id,$this->company_id)); 

                //Check if we need to update the contact based on the message
                
                //Check if it is stop promotion
                if($content==$this->getCompany()->getConfig('unsubscribe_trigger',"Stop promotions")){
                    $this->subscribed=0;
                    $this->update();
                }

                //Check it it is agent handover
                if($content==$this->getCompany()->getConfig('agent_handover_trigger',"Talk to a human")){
                    $this->enabled_ai_bot=false;
                    $this->update();

                    //Send the message to the client that soon human will contact him
                    $this->sendMessage($this->getCompany()->getConfig('agent_handover_message',"Soon you will be connected to a human agent. Thanks for your patience."),false,false);
                }
                

            }else{
                $this->last_support_reply_at=now();
                $this->is_last_message_by_contact=false;
                $this->sendMessageToWhatsApp($messageToBeSend,$this);
                event(new AgentReplies(auth()->user(),$messageToBeSend,$this));

                //Use credits
                try {
                    $this->getCompany()->useCreditsByAction("send_regular_message",1);
                } catch (\Exception $e) {
                    //Ignore
                }
            }    
        }
        $this->last_message=$this->trimString($content,40);
        $this->update();

       


        
        return $messageToBeSend;
    }
   
}
