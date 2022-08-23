<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class CustomerSampleExport implements FromCollection, WithHeadings
{
	use Exportable;

	public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Contact',
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //
    	$customer = collect();
        return $customer;
    }
}
