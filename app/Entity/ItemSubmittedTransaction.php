<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Model;

class ItemSubmittedTransaction extends Model
{
    

    protected $table = 'item_submitted_transaction';

    /**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
    protected $fillable = [
        'items', 'status', 'drawing_plan_id', 'possessor_from', 'possessor_to', 'signature_receive', 'signature_submit', 'signature_receive_datetime', 'signature_submit_datetime', 'name_receive', 'name_submit', 'internal_remarks', 'external_remarks', 'created_by', 'updated_by', 'code'
    ];

    protected $casts = [
    	'items' => 'array'
    ];

    protected $appends = [
        'sign_submit_url',
        'sign_receive_url'
    ];


    public function getStatus() {

        switch ($this->status) {
            case 'receive':
                return 'Submit';
                break;
            case 'handover_submit':
                return 'Handover (Submit)';
                break;
            case 'handover_return':
                return 'Handover (Return)';
                break;
            case 'return':
                return 'Return';
                break;
        }
    }

    public function keyFrom() {

        switch ($this->status) {
            case 'receive':
                return 'Owner';
                break;
            case 'handover_submit':
                return 'Property Management';
                break;
            case 'handover_return':
                return 'Handler';
                break;
            case 'return':
                return 'Property Management';
                break;
        }
    }

    public function keyTo() {

        switch ($this->status) {
            case 'receive':
                return 'Property Management';
                break;
            case 'handover_submit':
                return 'Handler';
                break;
            case 'handover_return':
                return 'Property Management';
                break;
            case 'return':
                return 'Owner';
                break;
        }
    }

    public function possession() {

        switch ($this->status) {
            case 'receive':
                return 'Owner -> Property Management';
                break;
            case 'handover_submit':
                return 'Property Management -> Handler';
                break;
            case 'handover_return':
                return 'Handler -> Property Management';
                break;
            case 'return':
                return 'Property Management -> Owner';
                break;
        }
    }

    public function getSignSubmitUrlAttribute() {
        
        if($this->signature_submit) {

            return url('uploads/signatures') . '/' . $this->signature_submit;
        }

        return url('assets/images/no_image.png');

    }

    public function getSignReceiveUrlAttribute() {
        
        if($this->signature_receive) {

            return url('uploads/signatures') . '/' . $this->signature_receive;
        }

        return url('assets/images/no_image.png');

    }

    function drawingPlan()
    {
        return $this->belongsTo(DrawingPlan::class, 'drawing_plan_id');
    }
}
