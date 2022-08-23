<?php
namespace App\Entity;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class FormVersion extends Model implements AuditableContract
{
    use Sortable, Auditable;

    const STATUS_ACTIVE = 1;

    const STATUS_PENDING = 2;

    const STATUS_INACTIVE = 3;

    protected $table = 'form_versions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'form_group_id',
        'version',
        'status'
    ];

    public function getStatusNameAttribute()
    {
        switch ($this->attributes['status']) {
            case 1:
                return 'Active';
                break;
            case 2:
                return 'Pending';
                break;
            case 3:
                return 'Inactive';
                break;
        }
    }

    public function formGroup()
    {
        return $this->belongsTo(FormGroup::class);
    }

    public function sections()
    {
        return $this->hasMany(FormSection::class);
    }
    
    public function forms()
    {
        return $this->hasMany(Form::class);
    }
}
