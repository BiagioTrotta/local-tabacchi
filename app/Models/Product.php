<?php

namespace App\Models;

use App\Models\Inventory;
use App\Models\ProductBarcode;
use App\Models\InventoryMovement;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'categoria',
        'codice',
        'denominazione_commerciale',
        'prezzo_kg_euro',
        'prezzo_confezione_euro',
        'tipo_confezione',
    ];

    public function barcodes()
    {
        return $this->hasMany(ProductBarcode::class);
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    public function movements()
    {
        return $this->hasMany(InventoryMovement::class);
    }
}
