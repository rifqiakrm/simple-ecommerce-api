<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class Reward extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'rewards';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'price',
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
        'id', 'created_at', 'updated_at',
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
