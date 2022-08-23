<?php

namespace App\Entity;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class IssueProject extends Model implements AuditableContract
{
    use Auditable;

    /**
     * @var string
     */
    protected $table = 'issue_project';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_id', 'issue_setting_id', 'group_id',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'unit_owner' => 'boolean',
    ];
}
