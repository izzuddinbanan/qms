<?php
namespace App\Entity;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class FormAttributeLocation extends Model implements AuditableContract
{
    use Auditable;
    
    protected $table = 'form_attribute_locations';

    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'form_attribute_id',
        'value',
        'width',
        'height',
        'position_x',
        'position_y',
        'background_color',
        'number_of_row',
    ];

    public function formAttribute()
    {
        return $this->belongsTo(FormAttribute::class, 'form_attribute_id');
    }
}
