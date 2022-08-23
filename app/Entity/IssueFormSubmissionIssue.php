<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Model;

class IssueFormSubmissionIssue extends Model
{

    protected $table = 'issue_form_submission_issue';

    /**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
    protected $fillable = [
        'issue_form_submission_id', 'issue_id'
    ];

}
