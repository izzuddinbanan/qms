<?php

namespace App\Exports;

use Session;
use App\Entity\User;
use App\Entity\RoleUser;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class CustomerExport implements FromCollection, WithHeadings
{
	use Exportable;

	public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Contact',
            'Status',
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
    	$session_id = session('project_id');

        if(Session::has('project_id')){
            Session::put('project_id', session('project_id'));
            $session_id = session('project_id');
        }

        $customer = RoleUser::customer()
			        ->project($session_id)
			        ->with('users')
			        ->join('users', 'users.id','=','role_user.user_id')
			        ->select('users.name', 'users.email', 'users.contact', 'users.verified')
			        ->get();
                    
		foreach ($customer as $c){
			$c->verified = $c->verified==1 ? 'Active' : 'Inactive';
		}
		
        return $customer;
    }
}
