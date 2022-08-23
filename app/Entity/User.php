<?php

namespace App\Entity;

use App\Entity\UserDevice;
use App\Entity\DrawingPlan;
use OwenIt\Auditing\Auditable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Notifications\Notifiable;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Authenticatable implements AuditableContract, CanResetPasswordContract
{
    use EntrustUserTrait, Notifiable, Auditable, Sortable, CanResetPassword;
    use SoftDeletes {SoftDeletes::restore insteadof EntrustUserTrait;}
    use SearchableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'contact', 'avatar', 'contact', 'language_id', 'buyer_id', 'salutation', 'ic_no', 'passport_no', 'comp_reg_no', 'phone_no', 'house_no', 'office_no', 'mailing_address', 'staff_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'verification_token',
    ];

    function avatarStoragePath()
    {
        return str_replace(asset('uploads/avatars'), "uploads/avatars", $this->avatar);
    }

    /**
     * @return mixed
     */
    function devices()
    {
        return $this->hasMany(UserDevice::class);
    }

    /**
     * @return mixed
     */
    function role()
    {
        return $this->hasMany(RoleUser::class, 'user_id', 'id');
    }

    /**
     * @return mixed
     */
    function unit()
    {
        return $this->hasMany(DrawingPlan::class, 'user_id', 'id');
    }

    /**
     * @return mixed
     */
    function notification()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * @return mixed
     */
    function newNotification()
    {
        return $this->hasMany(Notification::class)->where('read_status_id', 0);

    }

    /**
     * Route notifications for the FCM channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    function routeNotificationForFcm($notification)
    {
        return UserDevice::where('user_id', $this->id)->pluck('push_token')->toArray();
    }

    /**
     * @return mixed
     */
    function language()
    {
        return $this->belongsTo(Language::class, 'language_id', 'id');
    }

    /**
     * @return mixed
     */
    function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    /**
     * @return mixed
     */
    function projectByRole()
    {
        return $this->belongsToMany(Project::class, 'role_user');
    }

    /**
     * @param $query
     * @return mixed
     */
    function scopeIsThirdParty($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('name', 'third_party');
        })
            ->exists();
    }

    function jointUnitOwner()
    {
        return $this->hasMany(JointUnitOwner::class, 'user_id');
    }
}
