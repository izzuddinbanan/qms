<?php

namespace App\Entity;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class History extends Model implements AuditableContract
{

	protected $table = 'history';

	use SoftDeletes, Auditable;

    /**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
    protected $fillable = [
        'user_id', 'issue_id', 'image', 'remarks', 'status_id', 'temp_reference', 'customer_view'
    ];


    public function user(){

        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function status(){
        return $this->belongsTo(Status::class, 'status_id', 'id');  
    }

    public function scopeIdDescending($query)
    {
        return $query->orderBy('id','DESC');
    }

    public function issue(){
        return $this->belongsTo(Issue::class, 'issue_id', 'id');  
    }

    public function images(){
        return $this->hasMany(HistoryImage::class, 'history_id', 'id');
    }



}
