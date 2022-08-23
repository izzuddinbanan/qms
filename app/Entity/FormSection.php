<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class FormSection extends Model implements AuditableContract
{
    use Auditable;
    
    protected $table = 'form_sections';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'sequence',
        'form_version_id'
    ];

    public function version() 
    {
        return $this->belongsTo(FormVersion::class);
    }
}
