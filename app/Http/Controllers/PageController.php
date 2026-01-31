<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PageController extends Controller
{
    public function index()
    {
        $title = 'Homepage';
        return view('homepage', compact('title'));
    }

    // Funzione di reset magazzino
    public function resetWarehouse()
    {
        // Truncate tabelle inventories e inventory_movements
        DB::table('inventories')->truncate();

        return response()->json([
            'success' => true,
            'message' => 'Magazzino resettato correttamente.'
        ]);
    }

    // Funzione di reset barccode associati a prodotti
    public function resetBarcode()
    {
        // Disabilita temporaneamente i vincoli FK
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Tronca le tabelle nell'ordine corretto
        DB::table('inventory_movements')->truncate();
        DB::table('product_barcodes')->truncate();

        // Riabilita FK
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        return response()->json([
            'success' => true,
            'message' => 'Magazzino e barcode resettati correttamente.'
        ]);
    }
}
