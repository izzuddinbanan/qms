<?php

namespace App\Entity;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class DocumentVersion extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;

    protected $table = 'document_versions';


    /**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
    protected $fillable = [
        'document_id', 'file', 'version', 'publish', 'created_by', 'updated_by',
    ];

    /**
     * @var array
     */
    protected $appends = [
        'url',
    ];


    public function getUrlAttribute()
    {
        if ($this->file) {

            return url('uploads/documents/' . $this->file);
        }

        return null;

    }
}
