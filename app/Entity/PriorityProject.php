<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class PriorityProject extends Model
{
    
    use SoftDeletes;

    protected $table = 'priority_project';


    protected $fillable = [
        'project_id', 'priority_id',
    ];

    /**
     * @return mixed
     */
    function priority()
    {
        return $this->belongsTo(SettingPriority::class, 'priority_id', 'id');
    }

}
