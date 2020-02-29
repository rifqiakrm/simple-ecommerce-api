<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'product_categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'category_id',
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
        'id', 'product_id', 'category_id', 'created_at', 'updated_at',
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
     * Has One to Category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function product()
    {
        return $this->hasOne(\App\Models\Product::class, 'id', 'product_id');
    }
}
