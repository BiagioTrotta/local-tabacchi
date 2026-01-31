<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Http\Request;
use App\Models\ProductBarcode;
use App\Models\InventoryMovement;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    // Lista prodotti
    public function index()
    {
        $products = Product::with('inventory')
            ->orderBy('denominazione_commerciale')
            ->get();

        $title = 'Prodotti';

        return view('products.index', compact('products', 'title'));
    }

    // Form gestione inventario
    public function inventoryForm()
    {
        $title = "Gestione Inventario";

        // Recupera tutti i prodotti per il datalist
        $products = Product::orderBy('denominazione_commerciale')->get();

        return view('products.inventory', compact('title', 'products'));
    }


    public function inventoryMove(Request $request)
    {
        $request->validate([
            'items' => 'required|string',
            'type' => 'required|in:carico,scarico',
        ]);

        $items = json_decode($request->items, true);

        if (!is_array($items) || empty($items)) {
            return back()->withErrors(['msg' => 'Lista prodotti non valida']);
        }

        foreach ($items as $item) {

            // Trova prodotto: per EAN se presente, altrimenti per codice
            $product = null;

            if (!empty($item['ean'])) {
                $barcode = ProductBarcode::where('ean', $item['ean'])->first();
                $product = $barcode ? $barcode->product : null;
            }

            if (!$product && !empty($item['codice'])) {
                $product = Product::where('codice', $item['codice'])->first();
            }

            if (!$product) {
                throw ValidationException::withMessages([
                    'items' => 'Prodotto non valido o non associato correttamente'
                ]);
            }

            // Se EAN fornito e non associato → crealo
            if (!empty($item['ean'])) {
                $product->barcodes()->firstOrCreate(
                    ['ean' => $item['ean']],
                    ['tipo' => 'stecca'] // default
                );
            }

            // Aggiorna inventario
            $inventory = $product->inventory()->firstOrCreate(['product_id' => $product->id]);

            if ($request->type === 'carico') {
                $inventory->quantity += $item['quantity'];
            } else {
                $inventory->quantity -= $item['quantity'];
                if ($inventory->quantity < 0) $inventory->quantity = 0;
            }

            $inventory->save();

            // Registra movimento
            $barcodeId = !empty($item['ean'])
                ? $product->barcodes()->where('ean', $item['ean'])->first()->id
                : null;

            $product->movements()->create([
                'product_barcode_id' => $barcodeId,
                'type' => $request->type,
                'quantity' => $item['quantity'],
                'note' => $request->note ?? null,
            ]);
        }

        return back()->with('success', 'Movimenti registrati correttamente.');
    }







    public function findByBarcode(string $ean)
    {
        $barcode = ProductBarcode::with('product')->where('ean', $ean)->first();

        if (!$barcode) {
            return response()->json(null, 404);
        }

        $product = $barcode->product;

        return response()->json([
            'codice' => $product->codice,
            'denominazione' => $product->denominazione_commerciale,
            'tipo_confezione' => $product->tipo_confezione,
            'prezzo_confezione_euro' => $product->prezzo_confezione_euro,
        ]);
    }
}
