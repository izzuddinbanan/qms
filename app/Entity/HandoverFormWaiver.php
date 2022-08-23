<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Model;

class HandoverFormWaiver extends Model
{
    protected $table = 'handover_form_waiver';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_id',
        'description',
    ];
}
