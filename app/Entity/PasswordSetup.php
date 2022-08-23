<?php

namespace App\Entity;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class PasswordSetup extends Model implements AuditableContract
{
    
    use Auditable;

	protected $table = 'password_setup';

    /**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
    protected $fillable = [
        'email', 
        'token', 
        'expire_at',
    ];

}


