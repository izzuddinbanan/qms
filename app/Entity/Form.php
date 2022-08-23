<?php
namespace App\Entity;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Form extends Model implements AuditableContract
{
    use Auditable;

    const FILE_PATH = 'uploads/forms';

    protected $table = 'forms';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'file',
        'width',
        'height',
        'form_version_id'
    ];

    public function getFileUrlAttribute()
    {
        return asset(self::FILE_PATH) . '/' . $this->attributes['file'];
    }

    public function versions()
    {
        return $this->belongsTo(FormVersion::class);
    }

    public function formAttributes() 
    {
        return $this->hasMany(FormAttribute::class);
    }    
}
