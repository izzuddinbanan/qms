<?php

namespace App\Entity;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use Nicolaslopezj\Searchable\SearchableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class GroupProject extends Model implements AuditableContract
{
    use SoftDeletes, Sortable, SearchableTrait, Auditable;

    protected $table = 'group_project';

    /**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
    protected $fillable = [
        'group_id', 'project_id',
    ];


    public function project(){
        return $this->hasone('App\Entity\Project', 'id', 'project_id');
    }

    public function groupDetails(){
        return $this->belongsTo(GroupContractor::class, 'group_id', 'id');
    }


    
}
