<?php
namespace App\Entity;

use Illuminate\Database\Eloquent\Model;

class HandOverFormAcceptance extends Model
{

    protected $table = 'handover_form_acceptance';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'termsConditions',
        'project_id',
        'status',
        'designation',
    ];
}
