<?php

namespace App\Models;

use App\Models\Product;
use App\Models\ProductBarcode;
use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    protected $fillable = [
        'product_id',
        'product_barcode_id',
        'type',
        'quantity',
        'note',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function barcode()
    {
        return $this->belongsTo(ProductBarcode::class, 'product_barcode_id');
    }
}
