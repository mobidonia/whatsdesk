<?php

namespace Modules\Wpbox\Jobs;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Wpbox\Models\Message;
use Modules\Wpbox\Models\Campaign;

class ReceiveUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(protected $value)
    {
            
    }

    public function handle()
    {   
            $value=$this->value;

            //Status change -- Message update
            $newStatus=$value['statuses'][0]['status'];
            $messageFBID=$value['statuses'][0]['id'];
            $message=Message::where('fb_message_id',$messageFBID)->first();
            if($message){
                $message_previous_status=$message->status;
                if($newStatus=="sent"&&$message->status!=3){
                    $message->status=2;
                }else if($newStatus=="delivered"&&$message->status!=4){
                    $message->status=3;
                }else if($newStatus=="read"){
                    $message->status=4;
                }else if($newStatus=="failed"){
                    $message->status=5;
                    $message->error=$value['statuses'][0]['errors'][0]['message'];
                }
                $message->update();

                if($message->campaign_id!=null &&  $message_previous_status!=$message->status){
                    $campaign=Campaign::where('id',$message->campaign_id)->first();
                    if($campaign){
                        if($newStatus=="sent"){
                            $campaign->increment('sended_to', 1);
                        }else if($newStatus=="delivered"){
                            $campaign->increment('delivered_to', 1);
                        }else if($newStatus=="read"){
                            $campaign->increment('read_by', 1);
                        }
                        $campaign->update();
                    }
                    
                }
            }

    }
}