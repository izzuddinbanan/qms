<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Model;

class HandoverFormSectionItem extends Model
{
    protected $fillable = [
        'handover_form_section_id',
        'name',
        'quantity',
    ];   
}
