<?php

namespace App\Models;

use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class Product extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'merchant_id',
        'code',
        'name',
        'image',
        'price',
    ];

    /**
     * Append attributes.
     *
     * @var array
     */
    protected $appends = [
        'hashed_id',
        'merchant_name',
        'categories',
    ];

    /**
     * Hidden attribute.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'merchant_id', 'created_at', 'updated_at', 'user',
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

    /**
     * Get merchant name attribute.
     *
     * @return string
     */
    public function getMerchantNameAttribute()
    {
        return $this->user->name;
    }

    /**
     * Get image attribute.
     *
     * @return string
     */
    public function getImageAttribute()
    {
        if (is_null($this->attributes['image'])) {
            return null;
        }

        return asset($this->attributes['image']);
    }

    /**
     * Get categories attribute.
     *
     * @return array
     */
    public function getCategoriesAttribute()
    {
        $pc = ProductCategory::where('product_id', $this->attributes['id'])->get();
        $res = [];
        foreach ($pc as $c) {
            array_push($res, [
                'hashed_id' => $c->category->hashed_id,
                'name' => $c->category->name,
            ]);
        }

        return $res;
    }

    /**
     * Has Many to categories.
     *
     */
    public function categories()
    {
        return $this->hasMany(\App\Models\ProductCategory::class, 'product_id', 'id');
    }

    /**
     * Relation to User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\App\User::class, 'merchant_id');
    }
}
