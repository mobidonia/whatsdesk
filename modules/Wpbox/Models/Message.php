<?php

namespace Modules\Wpbox\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\CompanyScope;
use Illuminate\Support\Facades\Http;

class Message extends Model
{
    
    protected $table = 'messages';
    public $guarded = [];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
    public function campaign()
    {
        return $this->belongsTo(Campaign::class,'campaign_id','id','wa_campaings');
    }

    public function doTranslation($is_message_by_contact){

        $company=Company::where('id',$this->company_id)->first();
        if($is_message_by_contact){
            //Translate the message to the company language
            $language= $company->getConfig('translate_incoming_messages','Original');
        }else{
            //Translate the message to the contact language
            $language=$this->contact->language;
        }

        if($language=='none'||$language=='Original'){
            //Do nothing
        }else if($company->getConfig('translation_enabled', false)){
            //Translate the message
            

            $dataTosend=[
                'model' => config('wpbox.openai_model','gpt-4'),
                'messages' => 
                [
                    ['role'=>'user','content'=>"Translate the following message to ".$language.": ".$this->value],

                ],  
                'temperature' => 0.8,
                'stream'  => false,
                'max_tokens' => intval(config('wpbox.openai_max_tokens')),
            ];

          

            $open_ai_key=config('wpbox.openai_api_key');
            if(config('settings.is_demo',false)){
                $open_ai_key=config('wpbox.openai_api_key_demo');
            }

           
           

            if(strlen($open_ai_key)<5){
               //No API key
            }else{
                $openAIResponse = Http::timeout(400)->withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' .$open_ai_key
                ])->post('https://api.openai.com/v1/chat/completions',$dataTosend );
    
                if(!$openAIResponse->ok()){
                    //If admin, show the error
                    $this->original_message="Error -> ".$openAIResponse->getBody()->getContents();
                }else{
                    $this->original_message=$this->value;
                    $this->value=$openAIResponse->json()['choices'][0]['message']['content'];
                    $this->save();
                }
            }
            
            
        }
       


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
