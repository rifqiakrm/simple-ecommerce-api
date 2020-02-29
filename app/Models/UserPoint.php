<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class UserPoint extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'user_points';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'trx_id',
        'type',
        'balance',
        'amount',
        'description',
    ];

    /**
     * Append attributes.
     *
     * @var array
     */
    protected $appends = [
        'hashed_id',
    ];

    /**
     * Hidden attribute.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'user_id', 'created_at', 'updated_at',
    ];

    /**
     * Get hashed ID
     *
     * @return string
     */
    public function hashedId()
    {
        return Hashids::connection('main')->encode($this->attributes['id']);
    }

    /**
     * Get hashed id attribute.
     *
     * @return string
     */
    public function getHashedIdAttribute()
    {
        return $this->hashedId();
    }
}
