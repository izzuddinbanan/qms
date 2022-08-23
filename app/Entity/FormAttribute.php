<?php
namespace App\Entity;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class FormAttribute extends Model implements AuditableContract
{
    use Auditable;

    protected $table = 'form_attributes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'form_id',
        'attribute_id',
        'key',
        'is_required',
        'form_section_id'
    ];

    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id');
    }

    public function section()
    {
        return $this->belongsTo(FormSection::class, 'form_section_id');
    }

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'form_attribute_role', 'form_attribute_id', 'role_id');
    }

    public function locations()
    {
        return $this->hasMany(FormAttributeLocation::class);
    }
}
