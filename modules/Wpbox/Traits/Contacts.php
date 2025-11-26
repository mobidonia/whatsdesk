<?php

namespace Modules\Wpbox\Traits;

use Modules\Wpbox\Models\Contact;

trait Contacts {
    
    public function getOrMakeContact($phone,$company,$name){
        //Find the contact
        $contact = Contact::where('company_id', $company->id)
                          ->where(function ($query) use ($phone) {
                              $query->where('phone', $phone)
                                    ->orWhere('phone', "+" . $phone);
                          })->first();

        if(!$contact){
            //Create new contact
            $contact=Contact::create([
                'name' => $name,
                'phone' =>  $phone,
                'avatar'=> '',
                'company_id'=>$company->id,
                'has_chat'=>true,
                'created_at' => now(),
                'updated_at' => now(),
                'last_support_reply_at'=>null,
                'last_reply_at'=>now(),
                "last_message"=>"",
                "is_last_message_by_contact"=>true,    
            ]);
        }

        return $contact;
    }

   
}

?>
