<?php
namespace App\Entity;

use OwenIt\Auditing\Auditable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class FormGroup extends Model implements AuditableContract
{
    use SoftDeletes, SearchableTrait, Sortable, Auditable;

    protected $table = 'form_groups';

    protected $fillable = [
        'name',
        'client_id'
    ];

    protected $searchable = [
        /**
         * Columns and their priority in search results.
         * Columns with higher values are more important.
         * Columns with equal values have equal importance.
         *
         * @var array
         */
        'columns' => [
            'name' => 1
        ]
    
    ];

    public function versions()
    {
        return $this->hasMany(FormVersion::class);
    }

    public function latestVersion()
    {
        return $this->versions()
            ->latest()
            ->where('status', FormVersion::STATUS_ACTIVE);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'form_group_project', 'form_group_id', 'project_id');
    }

    public function form()
    {
        return $this->belongsToMany(GroupForm::class, 'group_form_link', 'form_group_id', 'group_form_id');
    }


    public function normalForm()
    {
        return $this->belongsToMany(LocationPoint::class, 'location_normal_form', 'form_id', 'location_id');

    }

    public function mainForm()
    {
        return $this->belongsToMany(LocationPoint::class, 'location_main_form', 'form_id', 'location_id');

    }

    public function formStatus()
    {
        return $this->hasMany(FormGroupStatus::class, 'form_group_id', 'id');
    }


    public function formStatusOpen()
    {
        return $this->hasOne(FormGroupStatus::class, 'form_group_id', 'id')->where('fix_label', 'open');
    }

    public function formStatusCreate()
    {
        return $this->hasMany(FormGroupStatus::class, 'form_group_id', 'id')->where('fix_label', '!=', 'open');
    }


    // public function normalGroupForm()
    // {
    //     return $this->belongsToMany(GroupForm::class, 'location_normal_group_form', 'location_id', 'form_id');

    // }

    // public function mainGroupForm()
    // {
    //     return $this->belongsToMany(GroupForm::class, 'location_main_group_form', 'location_id', 'form_id');

    // }
}
