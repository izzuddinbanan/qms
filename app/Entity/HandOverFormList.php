<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Model;

class HandOverFormList extends Model
{
    protected $table = 'handover_form_list';
	
	protected $fillable = [
        'project_id',
        'name',
        'description',
        'meter_reading',
        'status',
    ];    


    public function section()
    {
        return $this->hasMany(HandOverFormSection::class, 'handover_form_list_id');
    }

}
