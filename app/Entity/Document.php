<?php

namespace App\Entity;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Document extends Model implements AuditableContract
{
	use SoftDeletes, Auditable;

    protected $table = 'documents';

    /**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
    protected $fillable = [
        'name','client_id'
    ];



    public function document()
    {
        return $this->belongsToMany(Project::class, 'project_document', 'document_id', 'project_id');
    }

    public function version(){
        return $this->hasMany(DocumentVersion::class, 'document_id', 'id');
    }

    public function activeVersion(){
        return $this->hasOne(DocumentVersion::class, 'document_id', 'id')->where('publish', true);
    }
}


