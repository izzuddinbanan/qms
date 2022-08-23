<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Model;

class GeneralStatus extends Model
{
    

    protected $table = 'general_status';

    /**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
    protected $fillable = [
        'name', 'color', 'type',
    ];
}
