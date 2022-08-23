<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Model;

class HandoverFormSubmission extends Model
{
    protected $table = 'handover_form_submissions';
	
	protected $fillable = [
        'drawing_plan_id',
        'key_submission',
        'es_submission',
        'waiver_submission',
        'photo_submission',
        'acceptance_submission',
        'survey_submission',
        'pdf_name',
        'created_by',
        'survey_form_id',
    ];    

    protected $casts = [
    	'key_submission' => 'array',
    	'es_submission' => 'array',
    	'waiver_submission' => 'array',
    	'photo_submission' => 'array',
    	'acceptance_submission' => 'array',
    	'survey_submission' => 'array',
    ]; 


    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    function drawingPlan()
    {
        return $this->belongsTo(DrawingPlan::class, 'drawing_plan_id', 'id');
    }
}
