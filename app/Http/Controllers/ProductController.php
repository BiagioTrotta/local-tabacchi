<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductBarcode;
use App\Models\Inventory;
use App\Models\InventoryMovement;

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

    // Salva movimento inventario
    public function inventoryMove(Request $request)
    {
        $request->validate([
            'ean' => 'nullable|string|max:13',
            'codice' => 'nullable|integer',
            'denominazione' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'type' => 'required|in:carico,scarico',
        ]);

        $product = null;

        // 1️⃣ Ricerca per EAN
        if ($request->ean) {
            $barcode = ProductBarcode::where('ean', $request->ean)->first();
            if ($barcode) {
                $product = $barcode->product;
            }
        }

        // 2️⃣ Ricerca manuale tramite codice o denominazione
        if (!$product) {
            $query = Product::query();
            if ($request->codice) $query->where('codice', $request->codice);
            if ($request->denominazione) $query->where('denominazione_commerciale', 'like', "%{$request->denominazione}%");
            $product = $query->first();
        }

        if (!$product) {
            return redirect()->back()->withErrors(['msg' => 'Prodotto non trovato']);
        }

        // 3️⃣ Se EAN fornito e non presente, lo associamo
        if ($request->ean && !$product->barcodes()->where('ean', $request->ean)->exists()) {
            $product->barcodes()->create([
                'ean' => $request->ean,
                'tipo' => 'stecca', // default
            ]);
        }

        // 4️⃣ Aggiorna inventario
        $inventory = $product->inventory()->firstOrCreate([
            'product_id' => $product->id,
        ]);

        if ($request->type === 'carico') {
            $inventory->quantity += $request->quantity;
        } else {
            $inventory->quantity -= $request->quantity;
            if ($inventory->quantity < 0) $inventory->quantity = 0;
        }

        $inventory->save();

        // 5️⃣ Registra movimento
        $product->movements()->create([
            'product_barcode_id' => $request->ean
                ? $product->barcodes()->where('ean', $request->ean)->first()->id
                : null,
            'type' => $request->type,
            'quantity' => $request->quantity,
            'note' => $request->note ?? null,
        ]);

        return redirect()->back()->with('success', 'Movimento registrato correttamente.');
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
        ]);
    }
}
