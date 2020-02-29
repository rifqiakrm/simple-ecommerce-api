<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'transaction_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'trx_id',
        'balance_id',
        'product_id',
        'pcs',
        'price',
    ];

    /**
     * Append attributes.
     *
     * @var array
     */
    protected $appends = [
        //
    ];

    /**
     * Hidden attribute.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'product_id', 'balance_id', 'created_at', 'updated_at',
    ];

    /**
     * Has One to Category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function category()
    {
        return $this->hasOne(\App\Models\Category::class, 'id', 'category_id');
    }

    /**
     * Has One to Product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function product()
    {
        return $this->hasOne(\App\Models\Product::class, 'id', 'product_id');
    }

    /**
     * Has One to User Balance.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function balance()
    {
        return $this->hasOne(\App\Models\UserBalance::class, 'id', 'balance_id');
    }
}
