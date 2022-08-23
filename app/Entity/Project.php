<?php

namespace App\Entity;

use OwenIt\Auditing\Auditable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Project extends Model implements AuditableContract
{
    use SoftDeletes, Sortable, SearchableTrait, Auditable;

    /**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
    protected $fillable = [
        'client_id', 'name', 'abbreviation_name', 'logo', 'app_logo', 'email_notification', 'email_notification_at', 'language_id', 'description', 'contract_no', 'data_lang', 'project_id', 'default_project_team_id', 'acceptance_form', 'project_id', 'header', 'footer'
    ];

    /**
     * @var array
     */
    protected $appends = [
        'logo_url',
        'app_logo_url',
        'header_url',
        'footer_url',
    ];

    protected $casts = [
        'data_lang' => 'array'
    ];
    /**
     * Searchable rules.
     *
     * @var array
     */
    protected $searchable = [
        /**
         * Columns and their priority in search results.
         * Columns with higher values are more important.
         * Columns with equal values have equal importance.
         *
         * @var array
         */
        'columns' => [
            'projects.name' => 10,
        ],
    ];  

    public function getLogoUrlAttribute()
    {
        if ($this->logo) {

            return url('uploads/project_logo') . '/' .  $this->logo;
        }

        return asset('assets/images/no_image.png');
    }

    public function getAppLogoUrlAttribute()
    {
        if ($this->app_logo) {

            return url('uploads/project_logo') . '/' .  $this->app_logo;
        }

        return asset('assets/images/no_image.png');
    }

    public function getHeaderUrlAttribute()
    {
        if ($this->header) {

            return url('uploads/template-pdf') . '/' .  $this->header;
        }

        return asset('assets/images/no_image.png');
    }

    public function getFooterUrlAttribute()
    {
        if ($this->footer) {

            return url('uploads/template-pdf') . '/' .  $this->footer;
        }

        return asset('assets/images/no_image.png');
    }


    public function projectLanguage(){
    	return $this->belongsTo('App\Entity\Language', 'language_id', 'id');
    }

    public function drawingSet(){
        return $this->hasMany(DrawingSet::class, 'project_id', 'id');
    }

    public function categoryProject(){
        return $this->hasMany(CategoryProject::class, 'project_id', 'id');

    }

    // public function priority()
    // {
    //     return $this->belongsToMany(SettingPriority::class, 'priority_project', 'project_id', 'priority_id');
    // }


    public function document()
    {
        return $this->belongsToMany(Document::class, 'project_document', 'project_id', 'document_id');
    }

    public function digitalform()
    {
        return $this->belongsToMany(FormGroup::class, 'form_group_project', 'project_id', 'form_group_id');

    }
   

    public function groupDigitalform()
    {
        return $this->belongsToMany(GroupForm::class, 'project_group_form', 'project_id', 'group_form_id');

    }

    public function survey()
    {
        return $this->hasMany(HandoverFormSurvey::class, 'project_id', 'id')->where('status', 'Active');
    }

    public function handOVerMenu()
    {
        return $this->hasMany(HandOverMenu::class, 'project_id');
    }

    public function handOVerMenuActive()
    {
        return $this->HandOverMenu()->where('show', 'yes');
    }

    public function HandoverFormAcceptance()
    {
        return $this->hasOne(HandOverFormAcceptance::class, 'project_id', 'id')->where('status', 'Active');
    }

    public function HandoverFormWaiver()
    {
        return $this->hasOne(HandoverFormWaiver::class, 'project_id');
    }

    public function client(){
        return $this->belongsTo(Client::class, 'client_id');
    }

    function acceptanceForm()
    {
        return $this->belongsTo(FormGroup::class, 'acceptance_form');
    }
   
}
