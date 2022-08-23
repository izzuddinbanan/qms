<?php
namespace App\Entity;

use Illuminate\Database\Eloquent\Model;

class HandoverFormSurvey extends Model
{

    protected $table = 'handover_form_survey';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'question',
        'sequence',
        'type',
        'project_id',
        'status',
        'handover_form_survey_id',
    ];
}
