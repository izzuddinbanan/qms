<?php
namespace App\Entity;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;
use Kyslik\ColumnSortable\Sortable;

class Submission extends Model implements AuditableContract
{
    use SoftDeletes, SearchableTrait, Sortable, Auditable;

    protected $table = 'submissions';
    
    const FILE_PATH = 'uploads/submissions';
    
    protected $searchable = [
        /**
         * Columns and their priority in search results.
         * Columns with higher values are more important.
         * Columns with equal values have equal importance.
         *
         * @var array
         */
        'columns' => [
            'reference_no' => 1
        ]        
    ];

    protected $fillable = [
        'reference_no',
        'location_id',
        'user_id',
        'status_id',
        'form_group_id',
        'remarks',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->select([
            'id',
            'name'
        ]);
    }

    public function location()
    {
        return $this->belongsTo(LocationPoint::class);
    }

    public function formGroup()
    {
        return $this->belongsToMany(FormGroup::class, 'submission_form_group')
            ->withPivot([
            'form_attribute_location_id',
            'value'
        ])
            ->using(SubmissionFormGroup::class);
    }

    public function status()
    {
        return $this->belongsTo(FormGroupStatus::class);
    }

    public function formGroupDetail()
    {

        return $this->belongsTo(FormGroup::class, 'form_group_id');

    }

    public function linkIssue()
    {
        return $this->belongsToMany(Issue::class, 'submission_issue', 'submission_id', 'issue_id');
    }

}
