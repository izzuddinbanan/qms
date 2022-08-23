<?php

namespace App\Entity;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class UserClient extends Model implements AuditableContract
{
    
    use Auditable;

	protected $table = 'user_client';

    /**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
    protected $fillable = [
        'user_id', 'client_id',
    ];

}
