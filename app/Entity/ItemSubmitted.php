<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Model;

class ItemSubmitted extends Model
{
    

    protected $table = 'item_submitted';

    /**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
    protected $fillable = [
        'drawing_plan_id', 'code', 'name', 'primary', 'possessor', 'created_by', 'updated_by'
    ];

    public function possessorDisplay()
    {

    	switch ($this->possessor) {
            case 'handler':
                return 'Project Department';
                break;
            case 'management':
                return 'Project Management';
                break;
        }
    }
}
