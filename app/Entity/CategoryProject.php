<?php

namespace App\Entity;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class CategoryProject extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;

    protected $table = 'category_project';

    /**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
    protected $fillable = [
        'project_id', 'category_setting_id', 'group_id',
    ];
}
