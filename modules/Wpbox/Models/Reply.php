<?php

namespace Modules\Wpbox\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\CompanyScope;
use Illuminate\Support\Facades\Log;

class Reply extends Model
{
    
    protected $table = 'replies';
    public $guarded = [];

    public function shouldWeUseIt($receivedMessage, Contact $contact) //Brij Mohan Negi Update
    {
        $receivedMessage = " " . strtolower($receivedMessage);
        $shouldWeUseIt = false;

        //Check if this tipe is a welcome bot, and if this is contact first message
        if ($this->type == 4 && $contact->messages->count() == 1) {
            $shouldWeUseIt = true;
        } else {
            //Check based on the trigger
            // Store the value of $this->trigger in a new variable
            $triggerValues = $this->trigger;

            // Convert $triggerValues into an array if it contains commas
            if (strpos($triggerValues, ',') !== false) {
                $triggerValues = explode(',', $triggerValues);
            }

            //Check if we can use this reply
            if (is_array($triggerValues)) {
                foreach ($triggerValues as $trigger) {
                    if ($this->type == 2) {

                        $trigger = " " . strtolower($trigger); //Brij Mohan Negi Update
                        // Exact match
                        if ($receivedMessage == $trigger) {
                            $shouldWeUseIt = true;
                            break; // exit the loop once a match is found
                        }
                    } else if ($this->type == 3) {
                        // Contains
                        if (stripos($receivedMessage, $trigger) !== false) {
                            $shouldWeUseIt = true;
                            break; // exit the loop once a match is found
                        }
                    }
                }
            } else {
                //Doesn't contain commas
                $triggerValues = " " . strtolower($triggerValues); //Brij Mohan Negi Update
                if ($this->type == 2) {
                    // Exact match
                    if ($receivedMessage == $triggerValues) {
                        $shouldWeUseIt = true;
                    }
                } else if ($this->type == 3) {
                    // Contains
                    if (stripos($receivedMessage, $triggerValues) !== false) {
                        $shouldWeUseIt = true;
                    }
                }
            }
        }

        //Change message
        if ($shouldWeUseIt == true) {
            $this->increment('used', 1);
            $this->update();

            //Change the values in the  $this->text
            $pattern = '/{{\s*([^}]+)\s*}}/';
            preg_match_all($pattern, $this->text, $matches);
            $variables = $matches[1];
            foreach ($variables as $key => $variable) {
                if ($variable == "name") {
                    $this->text = str_replace("{{" . $variable . "}}", $contact->name, $this->text);
                } else if ($variable == "phone") {
                    $this->text = str_replace("{{" . $variable . "}}", $contact->phone, $this->text);
                } else {
                    //Field
                    $val = $contact->fields->where('name', $variable)->first()->pivot->value;
                    $this->text = str_replace("{{" . $variable . "}}", $val, $this->text);
                }
            }

            //Change the values in the  $this->header
            $pattern = '/{{\s*([^}]+)\s*}}/';
            preg_match_all($pattern, $this->header, $matches);
            $variables = $matches[1];
            foreach ($variables as $key => $variable) {
                if ($variable == "name") {
                    $this->header = str_replace("{{" . $variable . "}}", $contact->name, $this->header);
                } else if ($variable == "phone") {
                    $this->header = str_replace("{{" . $variable . "}}", $contact->phone, $this->header);
                } else {
                    //Field
                    $val = $contact->fields->where('name', $variable)->first()->pivot->value;
                    $this->header = str_replace("{{" . $variable . "}}", $val, $this->header);
                }
            }
            Log::info("Let's send the reply");
            Log::info($this);

            $contact->sendReply($this);

            Log::info("Let's check if this reply has a next reply");
            try {
               //Check if this reply has a next reply
                if($this->next_reply_id){
                    Log::info("next_reply_id: ".$this->next_reply_id);
                    $nextReply = Reply::find($this->next_reply_id);
                    $nextReply->sendTheReply($receivedMessage,$contact);
                }
            } catch (\Throwable $th) {
                //throw $th;
                Log::info($th);
            }

            return true;
        } else {
            return false;
        }
    }

    public function sendTheReply($receivedMessage,Contact $contact){
        Log::info("Let's send the reply");
        $this->increment('used', 1);
            $this->update();


            //Change the values in the  $this->text
            $pattern = '/{{\s*([^}]+)\s*}}/';
            preg_match_all($pattern, $this->text, $matches);
            $variables = $matches[1];
            foreach ($variables as $key => $variable) {
                if($variable=="name"){
                    $this->text=str_replace("{{".$variable."}}",$contact->name,$this->text);
                }else if($variable=="phone"){
                    $this->text=str_replace("{{".$variable."}}",$contact->phone,$this->text);
                }else{
                    //Field
                    $val=$contact->fields->where('name',$variable)->first()->pivot->value;
                    $this->text=str_replace("{{".$variable."}}",$val,$this->text);
                }
            }

            //Change the values in the  $this->header
            $pattern = '/{{\s*([^}]+)\s*}}/';
            preg_match_all($pattern, $this->header, $matches);
            $variables = $matches[1];
            foreach ($variables as $key => $variable) {
                if($variable=="name"){
                    $this->header=str_replace("{{".$variable."}}",$contact->name,$this->header);
                }else if($variable=="phone"){
                    $this->header=str_replace("{{".$variable."}}",$contact->phone,$this->header);
                }else{
                    //Field
                    $val=$contact->fields->where('name',$variable)->first()->pivot->value;
                    $this->header=str_replace("{{".$variable."}}",$val,$this->header);
                }
            }
            Log::info("Let's check if this reply has a next reply  before sending it");
            
            $contact->sendReply($this);

            Log::info("Let's check if this reply has a next reply");
            try {
               //Check if this reply has a next reply
                if($this->next_reply_id){
                    Log::info("next_reply_id: ".$this->next_reply_id);
                    $nextReply = Reply::find($this->next_reply_id);
                    $nextReply->sendTheReply($receivedMessage,$contact);
                }
            } catch (\Throwable $th) {
                //throw $th;
                Log::info($th);
            }

            return true;
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
}