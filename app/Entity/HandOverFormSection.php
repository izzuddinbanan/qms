<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Model;

class HandOverFormSection extends Model
{
    protected $table = 'handover_form_section';
	
	protected $fillable = [
        'handover_form_list_id',
        'name',
        'name',
        'seq',
        'config',
    ];    

    protected $casts = [
    	'config' => 'array',
    ]; 

    public function item()
    {
        return $this->hasMany(HandoverFormSectionItem::class, 'handover_form_section_id');
    }
}
