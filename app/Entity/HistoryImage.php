<?php

namespace App\Entity;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class HistoryImage extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;
    
    protected $table = 'history_images';


    protected $fillable = [
    	'image', 'type', 'seq', 'history_id',
    ];
}
