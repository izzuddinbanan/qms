<?php

namespace App\Entity;


use OwenIt\Auditing\Auditable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;


class FormGroupStatus extends Model implements AuditableContract
{
    use SoftDeletes, SearchableTrait, Sortable, Auditable;

    protected $table = 'form_group_status';

    protected $fillable = [
        'form_group_id',
        'name',
        'fix_label',
        'color_code',
    ];
    
}
