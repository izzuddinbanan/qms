<?php

namespace App\Entity;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Nicolaslopezj\Searchable\SearchableTrait;
use Kyslik\ColumnSortable\Sortable;

class RoleUser extends Model implements AuditableContract
{

	use Auditable, SearchableTrait, Sortable,SoftDeletes;

    protected $table = 'role_user';

    protected $fillable = [
        'user_id', 'role_id', 'project_id', 'group_id', 'client_id',
    ];


    public function project(){
    	return $this->hasOne('App\Entity\Project', 'id', 'project_id');
    }


    // GET role user
    public function roles()
	{
	    return $this->belongsTo('App\Entity\Role', 'role_id' , 'id');
	}

	// GET Client user
	public function clients()
	{
	    return $this->belongsTo('App\Entity\Client', 'client_id' , 'id');
	}

	public function users()
	{
	    return $this->belongsTo('App\Entity\User', 'user_id' , 'id');
	}

	/**
     * Searchable rules.
     *
     * @var array
     */
    protected $searchable = [
        /**
         * Columns and their priority in search results.
         * Columns with higher values are more important.
         * Columns with equal values have equal importance.
         *
         * @var array
         */
        'columns' => [
            'users.name'     => 1,
            'users.email'    => 1,
            'users.contact'  => 1,
            'roles.name'     => 1,
            'roles.display_name'     => 1,
        ],
        'joins'   => [
            'users' => ['role_user.user_id', 'users.id'],
            'roles' => ['role_user.role_id', 'roles.id'],
        ],
    ];

    /**
     * Scope a query to only include customer.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCustomer($query)
    {
        //change to 7(owner)
        return $query->where('role_id', '7');
    }

    /**
     * Scope a query to only include customer.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeProject($query, $id)
    {
        return $query->where('project_id', $id);
    }

}
