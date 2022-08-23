<?php

namespace App\Entity;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use Nicolaslopezj\Searchable\SearchableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class GroupContractor extends Model implements AuditableContract
{
    use SoftDeletes, Sortable, SearchableTrait, Auditable;

    protected $table = 'groups';

    /**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
    protected $fillable = [
        'client_id', 'abbreviation_name', 'display_name', 'description', 
    ];

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
            'groups.abbreviation_name' 	=> 1,
            'groups.display_name' 		=> 1,
            'groups.description' 		=> 1,
        ],

    ];


    public function contractors()
    {
        return $this->hasMany('App\Entity\User', '' , 'id');
    }
}
