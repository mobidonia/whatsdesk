<?php

namespace Modules\Wpbox\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Http;
use Modules\Wpbox\Models\Message;

class SendMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(protected Message $message)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
         //We need data per company
         $company=null;
         try {
             $company=$this->message->campaign->company;
             $this->message->contact->phone;
         } catch (\Throwable $th) {
            $this->message->error="The company or contact is not found";
            $this->message->status=1;
            $this->message->update();
         }
        
         if($company){
             $url = "https://graph.facebook.com/v19.0/".$company->getConfig('whatsapp_phone_number_id','').'/messages';
             $accessToken = $company->getConfig('whatsapp_permanent_access_token','');
             try {
                 $response = Http::withHeaders([
                     'Authorization' => 'Bearer ' . $accessToken,
                     'Content-Type' => 'application/json',
                 ])->post($url, [
                     'messaging_product' => 'whatsapp',
                     'to' => $this->message->contact->phone, // Add recipient information
                     'type' => 'template',
                     'template'=>[
                         "name"=> $this->message->campaign->template->name,
                         "language"=> [
                             "code"=> $this->message->campaign->template->language
                         ],
                         "components"=>json_decode($this->message->components)
                     ]
                 ]);
             
                 
                 $statusCode = $response->status();
                 $content = json_decode($response->body(),true);
                 //dd($content);
                 $this->message->created_at=now();
                 if(isset($content['messages'])){
                    $this->message->fb_message_id=$content['messages'][0]['id'];
                 }else{
                    $this->message->error=isset($content['error'])?$content['error']['message']:"Unknown error";
                 }
                 $this->message->status=1;
                 $this->message->update();
                 // Handle the response as needed based on $statusCode and $content
             } catch (\Exception $e) {
                 // Handle the exception
                 throw $e;
                
             }
         }
    }
}
