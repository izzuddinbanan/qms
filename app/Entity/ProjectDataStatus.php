<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectDataStatus extends Model
{
    use SoftDeletes;

    protected $table = 'project_data_status';
	
	/**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
    protected $fillable = [
        'project_id',
        'data_name', 
    ];    
    
}
