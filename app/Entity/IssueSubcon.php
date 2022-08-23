<?php

namespace App\Entity;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class IssueSubcon extends Model implements AuditableContract
{

	use SoftDeletes, Auditable;

    protected $table = 'issue_subcontractor';

    /**
     * @var array
     */
    protected $fillable = [
        'issue_id', 'subcontractor_id',
    ];
}
