<?php

namespace App\Entity;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use App\Supports\Shared\HasItemRelation;
use App\Supports\Shared\HasUserRelation;
use App\Supports\Shared\HasModuleRelation;
use App\Supports\Shared\HasStatusRelation;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Supports\Shared\HasUserDeviceRelation;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;


class Notification extends Model implements AuditableContract
{
    use HasUserRelation, HasUserDeviceRelation, HasItemRelation, HasStatusRelation, HasModuleRelation, SoftDeletes, Auditable;


    /**
     * @var array
     */
    protected $fillable = [
        'user_id', 'user_device_id', 'message', 'satus_id', 'issue_id', 'read_status_id', 'module_id', 'payload', 'created_at', 'updated_at', 'push_by', 'issue_status', 'type', 
    ];

    public function issue(){
    	return $this->belongsTo(Issue::class, 'issue_id', 'id');
    }

    function newNotification($query)
    {
        return $query->where('read_status_id', 1);

    }

    function pushBy()
    {
        return $this->belongsTo(User::class, 'push_by', 'id');
    }

    function issueStatus()
    {
        return $this->belongsTo(Status::class, 'issue_status', 'id');
    }

}
