<?php

namespace App\Entity;

use OwenIt\Auditing\Auditable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class SettingPriority extends Model implements AuditableContract
{
    use SoftDeletes, Sortable, SearchableTrait, Auditable;


    protected $table = 'setting_priority';

    /**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
    protected $fillable = [
        'client_id', 'name', 'no_of_days', 'type', 'priority_type_id', 'no_of_days_notify', 'data_lang',
    ];

    public $sortable = ['name', 'no_of_days', 'priority_type_id'];


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
            'type'          => 1,
            'name' 			=> 1,
            'no_of_days' 	=> 1,
        ],
      
    ];


    public function type()
    {
        return $this->belongsTo(PriorityType::class, 'priority_type_id', 'id');
    }

    public function priority()
    {
        return $this->belongsToMany(Project::class, 'priority_project', 'priority_id', 'project_id');
    }

}
