<?php

namespace App\Entity;

use OwenIt\Auditing\Auditable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use App\Supports\Shared\HasUserRelation;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class UserDevice extends Model implements AuditableContract
{
    use HasUserRelation, SoftDeletes, Auditable, Sortable, SearchableTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'user_id', 'push_token', 'OS', 'app_version', 'IMEI', 'os_version', 'width', 'height', 'created_at', 'updated_at',
    ];

    /**
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
            'user_devices.push_token'  => 1,
            'user_devices.OS'          => 1,
            'user_devices.app_version' => 1,
            'user_devices.IMEI'        => 1,
            'user_devices.os_version'  => 1,
            'user_devices.width'       => 1,
            'user_devices.height'      => 1,
            'user_devices.created_at'  => 1,
            'users.name'               => 1,
        ],
        'joins'   => [
            'users' => ['user_devices.user_id', 'users.id'],
        ],
    ];

    public static $operating_systems = ['ANDROID', 'IOS'];
}
