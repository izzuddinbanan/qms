<?php

namespace App\Entity;

use OwenIt\Auditing\Auditable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class SettingIssue extends Model implements AuditableContract
{
    use SoftDeletes, Sortable, SearchableTrait, Auditable;


    protected $table = 'setting_issues';

    /**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
    protected $fillable = [
        'client_id', 'name', 'category_id', 'type_id', 'data_lang', 'unit_owner'
    ];

     protected $searchable = [
        /**
         * Columns and their priority in search results.
         * Columns with higher values are more important.
         * Columns with equal values have equal importance.
         *
         * @var array
         */
        'columns' => [
            'setting_issues.name' => 1,
            'setting_category.name' => 1,
            'setting_types.name' => 1,
        ],
        'joins' => [
            'setting_category' => ['setting_issues.category_id','setting_category.id'],
            'setting_types' => ['setting_issues.type_id','setting_types.id'],
        ],

    ];


    public function type(){

    	return $this->belongsTo('App\Entity\SettingType', 'type_id', 'id');
    }

     public function category(){

    	return $this->belongsTo('App\Entity\SettingCategory', 'category_id', 'id');
    }


}
