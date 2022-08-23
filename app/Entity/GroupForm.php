<?php

namespace App\Entity;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class GroupForm extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;

    protected $table = 'group_form';

    /**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
    protected $fillable = [
        'name', 'created_by', 'updated_by', 'client_id', 'total'
    ];

    public function form()
    {
        return $this->belongsToMany(FormGroup::class, 'group_form_link', 'group_form_id', 'form_group_id');
    }


    public function projectForm()
    {
        return $this->belongsToMany(Project::class, 'project_group_form', 'group_form_id', 'project_id');
    }
}
