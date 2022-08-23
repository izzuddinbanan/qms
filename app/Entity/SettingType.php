<?php

namespace App\Entity;

use OwenIt\Auditing\Auditable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class SettingType extends Model implements AuditableContract
{
    use SoftDeletes, Sortable, SearchableTrait, Auditable;


    protected $table = 'setting_types';

    /**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
    protected $fillable = [
        'client_id', 'name', 'category_id', 'data_lang', 'unit_owner'
    ];

    public $sortable = ['name'];


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
            'setting_types.name' => 1,
            'setting_category.name' => 1,
        ],
        'joins' => [
            'setting_category' => ['setting_types.category_id','setting_category.id'],
        ],

    ];

    function inCategory(){

    	return $this->belongsTo('App\Entity\SettingCategory', 'category_id', 'id');
    }

    public function hasIssues(){
        return $this->hasMany(SettingIssue::class, 'type_id', 'id');
    }


}
