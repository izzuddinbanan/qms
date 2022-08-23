<?php

namespace App\Entity;
use Illuminate\Database\Eloquent\Model;

class PriorityType extends Model
{

    protected $table = 'setting_priority_type';


    // public function priority()
    // {
    //     return $this->belongsToMany(Project::class, 'priority_project', 'priority_id', 'project_id');
    // }

}
