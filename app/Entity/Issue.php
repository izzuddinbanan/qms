<?php

namespace App\Entity;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Issue extends Model implements AuditableContract
{

    use SoftDeletes, Auditable;
    
    /**
     * @var array
     */
    protected $fillable = [
        'location_id','inspector_id', 'owner_id', 'group_id', 'setting_category_id', 'setting_type_id', 'setting_issue_id', 'priority_id', 'due_by', 'position_x', 'position_y', 'image', 'status_id','start_date', 'remarks', 'created_by', 'merge_issue_id', 'conflict_issue_id', 'temp_reference', 'subcon_id', 'handover_status', 'on_behalf_owner', 'submit_source', 'assigned_to', 'assigned_count'
    ];


    public function location(){
    	return $this->belongsTo(LocationPoint::class, 'location_id', 'id')->withTrashed();
    }

    public function category(){
    	return $this->belongsTo(SettingCategory::class, 'setting_category_id', 'id');
    }

    public function type(){
		return $this->belongsTo(SettingType::class, 'setting_type_id', 'id');	
    }

    public function issue(){
		return $this->belongsTo(SettingIssue::class, 'setting_issue_id', 'id');	
    }

    public function priority(){
		return $this->belongsTo(SettingPriority::class, 'priority_id', 'id');	
    }

    public function status(){
		return $this->belongsTo(Status::class, 'status_id', 'id');	
    }

	public function inspector(){
		return $this->belongsTo(User::class, 'inspector_id', 'id');	
    }

    public function owner(){
		return $this->belongsTo(User::class, 'owner_id', 'id');	
    }

    public function createdBy(){
        return $this->belongsTo(User::class, 'created_by', 'id'); 
    }

    public function contractor(){
		return $this->belongsTo(GroupContractor::class, 'group_id', 'id');	
    }

    public function history()
    {
        return $this->hasMany(History::class, 'issue_id', 'id');
    }

    public function images()
    {
        return $this->hasMany(IssueImage::class, 'issue_id', 'id');
    }

    public function startImage(){
        return $this->images()->where('type', 1);
    }

    public function lastImage(){
        return $this->images()->where('type', 2);
    }

    public function historyDesc()
    {
        return $this->history()->orderBy('id', 'DESC');
    }

    public function historyDescCust()
    {
        return $this->historyDesc()->where('customer_view', 1)->orWhere('customer_view', 0);
    }

    public function historyDescContractorInspector()
    {
        return $this->historyDesc()->whereNull('customer_view')->orWhere('customer_view', 0);
    }

    public function joinIssue()
    {
        return $this->hasMany(Issue::class, 'merge_issue_id', 'id');
    }
    
    /**
     * @param $query
     * @return mixed
     */
    public function scopeLodged($query)
    {
        return $query->where('status_id', 1);
    }
    
    /**
     * @param $query
     * @return mixed
     */
    public function scopeNew($query)
    {
        return $query->where('status_id', 2);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopePendingStart($query)
    {
        return $query->where('status_id', 3);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeReject($query)
    {
        return $query->where('status_id', 4);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeWipOnly($query)
    {
        return $query->where('status_id', 5);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeWip($query)
    {
        return $query->whereIn('status_id', [5, 9]);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeNotme($query)
    {
        return $query->where('status_id', 6);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeReassign($query)
    {
        return $query->where('status_id', 7);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeComplete($query)
    {
        return $query->where('status_id', 8);
    }
    
    /**
     * @param $query
     * @return mixed
     */
    public function scopeWipRedo($query)
    {
        return $query->where('status_id', 9);
    }
    
    /**
     * @param $query
     * @return mixed
     */
    public function scopeClosed($query)
    {
        return $query->where('status_id', 10);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopePOA($query)
    {
        return $query->where('status_id', 11);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeRedoVerification($query)
    {
        return $query->where('status_id', 12);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopePendingAccess($query)
    {
        return $query->where('status_id', 13);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeCloseExternal($query)
    {
        return $query->where('status_id', 14);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeDecline($query)
    {
        return $query->where('status_id', 15);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeTest($query)
    {
        return $query->whereIn('status_id', [2,3,4,5,6,7,8,9,10]);
    }

    public function mergeIssue()
    {
        return $this->hasMany(Issue::class, 'conflict_issue_id', 'id')->withTrashed();
    }


}
