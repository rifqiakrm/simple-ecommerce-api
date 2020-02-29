<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'transactions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'trx_id',
    ];

    /**
     * Append attributes.
     *
     * @var array
     */
    protected $appends = [
        'products',
    ];

    /**
     * Hidden attribute.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'user_id', 'product_id', 'created_at', 'updated_at',
    ];

    /**
     * Get products attribute.
     *
     * @return string
     */
    public function getProductsAttribute()
    {
        $res = [];
        $details = TransactionDetail::where('trx_id', $this->attributes['trx_id'])->get();
        foreach ($details as $detail) {
            array_push($res, [
                'name' => $detail->product->name,
                'pcs' => $detail->pcs,
                'price' => (int) $detail->product->price,
                'total' => (int) $detail->price,
                'description' => $detail->balance->description,
            ]);
        }

        return $res;
    }
}
