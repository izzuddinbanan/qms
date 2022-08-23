<?php

namespace App\Entity;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use Nicolaslopezj\Searchable\SearchableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class LocationPoint extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;

    protected $table = 'locations';

    /**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
    protected $fillable = [
    	'name', 'reference', 'drawing_plan_id', 'position_x', 'position_y', 'status_id', 'points', 'color', 'normal_form', 'normal_group_form', 'main_form', 'main_group_form',
    ];

    public function issues(){
        return $this->hasMany(Issue::class, 'location_id', 'id');
    }

    public function drawingPlan(){
        return $this->belongsTo(DrawingPlan::class, 'drawing_plan_id', 'id');
    }

    public function status(){
        return $this->belongsTo(GeneralStatus::class, 'status_id', 'id');
    }

    public function normalForm()
    {
        return $this->belongsToMany(FormGroup::class, 'location_normal_form', 'location_id', 'form_id');

    }

    public function normalGroupForm()
    {
        return $this->belongsToMany(GroupForm::class, 'location_normal_group_form', 'location_id', 'form_id');

    }

    public function mainForm()
    {
        return $this->belongsToMany(FormGroup::class, 'location_main_form', 'location_id', 'form_id');

    }

    public function mainGroupForm()
    {
        return $this->belongsToMany(GroupForm::class, 'location_main_group_form', 'location_id', 'form_id');

    }

}
