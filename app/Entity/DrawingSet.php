<?php

namespace App\Entity;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class DrawingSet extends Model implements AuditableContract
{
	use SoftDeletes, Auditable;

    protected $table = 'drawing_sets';

    /**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
    protected $fillable = [
        'name', 'project_id', 'seq', 'handover_key_id', 'handover_es_id', 'handover_form'
    ];


    /**
     * @return mixed
     */
    function drawingPlan()
    {
        return $this->hasMany(DrawingPlan::class, 'drawing_set_id', 'id');
    }

    /**
     * @return mixed
     */
    function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    /**
     * @return mixed
     */
    function drawingPlanOrder()
    {
        return $this->hasMany(DrawingPlan::class, 'drawing_set_id', 'id')->orderBy('seq');
    }

    function keyForm()
    {
        return $this->belongsTo(HandOverFormList::class, 'handover_key_id');
    }

    function esForm()
    {
        return $this->belongsTo(HandOverFormList::class, 'handover_es_id');
    }

    function close_and_handover_form()
    {
        return $this->belongsTo(FormGroup::class, 'handover_form');
    }

    /**
     * @return mixed
     */
    function drawingPlanUnit()
    {
        return $this->drawingPlan()->where('types', 'unit');
    }

    /**
     * @return mixed
     */
    function drawingPlanUnitNoOwner()
    {
        return $this->drawingPlanUnit()->whereNull('user_id');
    }
    /**
     * @return mixed
     */
    function drawingPlanUnitHasOwner()
    {
        return $this->drawingPlanUnit()->whereNotNull('user_id');
    }
}
