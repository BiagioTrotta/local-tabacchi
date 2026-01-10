<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class ProductBarcode extends Model
{
    protected $fillable = [
        'product_id',
        'ean',
        'tipo',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
