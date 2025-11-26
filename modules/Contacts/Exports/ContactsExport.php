<?php

namespace Modules\Contacts\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Modules\Contacts\Models\Field;

class ContactsExport implements FromArray, WithHeadings
{
    protected $contacts;

    public function headings(): array
    {
        $headings= [
            'id',
            'name',
            'phone',
            'avatar',
            'email'
        ];
        $cs=Field::get();
        foreach ($cs as $key => $value) {
           array_push($headings,$value->name);
        }
        return $headings;   
    }

    public function __construct(array $contacts)
    {
        $this->contacts = $contacts;
    }

    public function array(): array
    {
        return $this->contacts;
    }
}
