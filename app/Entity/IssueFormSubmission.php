<?php
namespace App\Entity;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Kyslik\ColumnSortable\Sortable;

class IssueFormSubmission extends Model implements AuditableContract
{
    use Sortable, Auditable;

    protected $table = 'issue_form_submmission';
    
    const FILE_PATH = 'uploads/submissions';

    // GENERATE PDF FILE LOCATION -> uploads/oasignoff-form-submission
    
    protected $fillable = [
        'reference_no',
        'drawing_plan_id',
        'issue_id',
        'user_id',
        'status_id',
        'form_group_id',
        'remarks',
        'details',
        'submission_type',
        'created_at',
        'updated_at',
        'accept_issue',
        'redo_issue',
    ];

    protected $casts = [
        'details'       => 'array',
        'accept_issue'  => 'array',
        'redo_issue'    => 'array',
    ];

    public function drawingPlan()
    {
        return $this->belongsTo(DrawingPlan::class);
    }
    
}
