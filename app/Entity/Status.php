<?php

namespace App\Entity;

use DB;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    /**
     * @var string
     */
    protected $table = 'status';

    /**
     * @param $query
     * @return mixed
     */
    public function scopeUser($query)
    {
        return $query->where('type', 'user');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeItem($query)
    {
        return $query->where('type', 'item');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeBooking($query)
    {
        return $query->where('type', 'booking');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeComment($query)
    {
        return $query->where('type', 'comment');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeFraud($query)
    {
        return $query->where('type', 'fraud');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeItemAdminPending($query)
    {
        return $query->whereIn('id', [12, 13, 15]);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeItemAdminAvailable($query)
    {
        return $query->whereIn('id', [13, 19]);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeItemAdminTaken($query)
    {
        return $query->whereIn('id', [11, 14, 19])
            ->orderByRaw(DB::raw("FIELD(name, 'Taken', 'Collected', 'Cancelled by owner')"));
    }
}
