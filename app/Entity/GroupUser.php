<?php

namespace App\Entity;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use Nicolaslopezj\Searchable\SearchableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class GroupUser extends Model implements AuditableContract
{
    use SoftDeletes, Sortable, SearchableTrait, Auditable;

    protected $table = 'group_user';

    /**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
    protected $fillable = [
        'group_id', 'user_id',
    ];


    public function users()
    {
        return $this->belongsTo('App\Entity\User', 'user_id' , 'id');
    }

}
