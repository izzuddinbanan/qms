<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SubmissionFormGroup extends Pivot
{
    public $timestamps = false;
    
    protected $table = 'submission_form_group';
    
    protected $fillable = [
        'submission_id',
        'form_group_id',
        'form_attribute_location_id',   
        'value'
    ];
    
    public function submission() {
        return $this->belongsTo(Submission::class);
    }
    
    public function formGroup() {
        return $this->belongsTo(FormGroup::class);
    }
    
    public function formAttributeLocation() {
        return $this->belongsTo(FormAttributeLocation::class);
    }
}
