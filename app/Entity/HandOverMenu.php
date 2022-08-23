<?php
namespace App\Entity;

use Illuminate\Database\Eloquent\Model;

class HandOverMenu extends Model
{

    protected $table = 'handover_menu';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_id',
        'original_name',
        'display_name',
        'show',
    ];
}
