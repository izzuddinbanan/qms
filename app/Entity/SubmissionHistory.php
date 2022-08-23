<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubmissionHistory extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;

    protected $table = 'submission_history';

    protected $fillable = [
        'submission_id',
        'remarks',
        'status_id',
    ];

    public function status()
    {
        return $this->belongsTo(FormGroupStatus::class, 'status_id');
    }

    public function submission()
    {
        return $this->belongsTo(Submission::class, 'submission_id');
    }
    
}
