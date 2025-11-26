<?php

namespace Modules\Contacts\Imports;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\Contacts\Models\Contact;
use Modules\Contacts\Models\Field;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ContactsImport implements ToModel, WithHeadingRow, WithChunkReading
{

    public function chunkSize(): int
    {
        return 150;
    }

  

    /**
     * @param array $row
     *
     * @return User|null
     */
    public function model(array $row)
    {
        $keys = array_keys($row);
        $keysForFields=[];
        foreach ($keys as $key => $value) {
            $keysForFields[$key]=$this->getOrMakeField($value);
        }

        $prevContact=Contact::where('phone', $row['phone'])->first();
        if($prevContact){
            $contact=$prevContact;
            //Detach alll fields from the contact
            $contact->fields()->detach();
        }else{

            $contact=new Contact([
                'name'     => $row['name'],
                'phone'    => strpos($row['phone'],"+")!=false?$row['phone']:"+".$row['phone'],
             ]);
             $contact->save();
        }



        if(isset($row['avatar'])){
            $contact->avatar=$row['avatar'];
        }

        foreach ($keysForFields as $key => $fieldID) {
            if($fieldID!=0&&$row[$keys[$key]]){
                $contact->fields()->attach($fieldID, ['value' =>  $row[$keys[$key]]]);
            }
        }
        $contact->update();

       
        return $contact;
    }

    private function getOrMakeField($field_name){
        if($field_name=="name"||$field_name=="phone"||$field_name=="avatar"){
            return 0;
        }
        $field=Field::where('name',$field_name)->first();
        if(!$field){
            $field=Field::create([
                'name'     => $field_name,
                'type'=>"text",
            ]);
            $field->save();
        }
        return $field->id;
    }

}