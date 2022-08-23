<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppVersion extends Model
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'os', 'version', 'type', 'description', 'status',
    ];

}
