<?php

namespace App\Entity;

use DB;
use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;



class Client extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;


	/**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
    protected $fillable = [
        'name', 'abbreviation_name', 'logo', 'app_logo',
    ];


    /**
     * @var array
     */
    protected $appends = [
        'logo_url',
        'app_logo_url',
    ];

    public function getLogoUrlAttribute()
    {
        if ($this->logo) {

            return url('uploads/client_logo/') . '/' . $this->logo;
        }

        return url('assets/images/placeholder.jpg');
    }

    public function getAppLogoUrlAttribute()
    {
        if ($this->app_logo) {

            return url('uploads/client_logo/') . '/' . $this->app_logo;
        }

        return url('assets/images/placeholder.jpg');
    }


    public function users()
    {
        return $this->belongToMany(User::class, 'role_user', 'client_id' , 'user_id');
    }
	
    public function scopePowerUser($query)
    {
        return $query->leftjoin('role_user', 'clients.id', '=', 'role_user.client_id')
                ->leftjoin('users', 'role_user.user_id', '=', 'users.id')
                ->where('role_user.role_id' , 2)
                ->groupBy('role_user.client_id')
                ->select('clients.*' ,'users.id as user_id', 'users.name as user_name', 'users.contact as user_contact', 'users.email as user_email');
    }

    public function project(){
        return $this->hasMany(Project::class, 'client_id', 'id');
    }

    public function delete()    
    {
        DB::transaction(function() 
        {
            $this->project()->delete();
            parent::delete();
        });
    }
}