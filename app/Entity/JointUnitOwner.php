<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Model;

class JointUnitOwner extends Model
{
    protected $table = 'joint_unit_owner';

    protected $fillable = [
        'drawing_plan_id',
        'user_id',
    ];
    
     public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

     public function unit()
    {
        return $this->belongsTo(DrawingPlan::class, 'drawing_plan_id');
    }

}
