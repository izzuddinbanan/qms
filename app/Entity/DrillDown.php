<?php

namespace App\Entity;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class DrillDown extends Model implements AuditableContract
{
	use SoftDeletes, Auditable;

    protected $table = 'drill_downs';

    /**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
    protected $fillable = [
        'drawing_plan_id', 'to_drawing_plan_id', 'position_x', 'position_y',
    ];

    public function link(){

        return $this->belongsTo('App\Entity\DrawingPlan','to_drawing_plan_id', 'id');
    }

}
