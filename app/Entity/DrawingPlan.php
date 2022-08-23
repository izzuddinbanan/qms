<?php

namespace App\Entity;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;


class DrawingPlan extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;

    protected $table = 'drawing_plans';

    /**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
    protected $fillable = [
        'drawing_set_id', 'name', 'file', 'seq', 'width', 'height', 'types', 'level', 'block', 'phase', 'unit', 'unit_id', 'access_card', 'key_fob', 'car_park', 'handover_status', 'ready_to_handover', 'all_location_ready', 'default',
    ];

    /**
     * @var array
     */
    protected $appends = [
        'file_url',
        'file_thumbnail_url',
    ];

    public function getFileUrlAttribute()
    {
        if ($this->file) {

            return url('uploads/drawings') . '/' .  $this->file;
        }
    }

    public function getFileThumbnailUrlAttribute()
    {
        if ($this->file) {

            return url('uploads/drawings/thumbnail') . '/' .  $this->file;
        }
    }

    /**
     * @return mixed
     */
    public function drawingSet()
    {
        return $this->belongsTo(DrawingSet::class, 'drawing_set_id', 'id');
    }

    /**
     * @return mixed
     */
    public function drill()
    {
        return $this->hasMany(DrillDown::class, 'drawing_plan_id', 'id');
    }


    /**
     * @return mixed
     */
    public function location()
    {
        return $this->hasMany(LocationPoint::class, 'drawing_plan_id', 'id');
    }

    /**
     * @return mixed
     */
    public function locationNoGeneral()
    {
        return $this->hasMany(LocationPoint::class, 'drawing_plan_id', 'id')->where('name', '!=', 'Other');
    }


    /**
     * @return mixed
     */
    public function scopeDefault($query)
    {
        return $query->where('default', 1);
    }

    public function scopeUnit($query){

        return $query->where('types', 'unit');
    }   

    /**
     * @return mixed
     */
    public function locationOrder()
    {
        return $this->hasMany(LocationPoint::class, 'drawing_plan_id', 'id')->latest();
    }


    /**
     * @return mixed
     */
    public function unitOwner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function itemSubmitted()
    {
        return $this->hasMany(ItemSubmitted::class, 'drawing_plan_id');
    }

    public function itemManagementSubmit()
    {
        return $this->itemSubmitted()->where('possessor', 'management');
    }

    public function itemHandlerSubmit()
    {
        return $this->itemSubmitted()->where('possessor', 'handler');
    }

    public function itemSubmittedTransaction()
    {
        return $this->hasMany(ItemSubmittedTransaction::class, 'drawing_plan_id');
    }

    public function itemSubmittedTransactionLatest()
    {
        return $this->itemSubmittedTransaction()->latest();
    }

    public function jointOwner()
    {
        return $this->hasMany(JointUnitOwner::class, 'drawing_plan_id');
    }
      

}
