<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VendorsExport implements FromArray, WithHeadings
{
    protected $vendors;

    public function headings(): array
    {
        return [
            'vendor_name',
            'vendor_id',
            'created',
            'owner_name',
            'owner_email',
        ];
    }

    public function __construct(array $vendors)
    {
        $this->vendors = $vendors;
    }

    public function array(): array
    {
        return $this->vendors;
    }
}
