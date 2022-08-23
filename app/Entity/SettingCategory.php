<?php

namespace App\Entity;

use OwenIt\Auditing\Auditable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class SettingCategory extends Model implements AuditableContract
{
	use SoftDeletes, Sortable, SearchableTrait, Auditable;

    protected $table = 'setting_category';

    /**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
    protected $fillable = [
        'client_id', 'name', 'data_lang',
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
            'name' => 1,
        ],

    ];


    public function hasTypes(){
        return $this->hasMany(SettingType::class, 'category_id', 'id');
    }
}
