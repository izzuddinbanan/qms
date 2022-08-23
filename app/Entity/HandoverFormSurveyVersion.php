<?php
namespace App\Entity;

use Illuminate\Database\Eloquent\Model;

class HandoverFormSurveyVersion extends Model
{

    protected $table = 'handover_form_survey_version';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'version',
        'project_id',
        'status',
    ];

    public function handoverSurveyQuestion()
    {
        return $this->hasMany(HandoverFormSurvey::class, 'handover_form_survey_id');
    }
}
