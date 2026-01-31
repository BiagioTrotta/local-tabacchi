<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\ProductBarcode;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\InventoryMovement;
use App\Models\CashMovement;

class SaleController extends Controller
{
    public function create()
    {
        $products = Product::orderBy('denominazione_commerciale')->get();
        $title = 'Nuova Vendita';
        return view('sales.create', compact('products', 'title'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.manual_ean' => 'nullable|string|max:13',
        ]);

        DB::transaction(function () use ($request) {

            $sale = Sale::create([
                'total_amount' => 0,
                'payment_method' => 'contanti',
                'status' => 'completed',
            ]);

            $total = 0;

            foreach ($request->items as $item) {
                $product = Product::with('inventory', 'barcodes')->findOrFail($item['product_id']);

                // 🔹 Se l’utente ha inserito manualmente un barcode non registrato
                if (!empty($item['manual_ean'])) {
                    ProductBarcode::updateOrCreate(
                        ['ean' => $item['manual_ean']],
                        ['product_id' => $product->id, 'tipo' => 'stecca']
                    );
                }

                // Controllo disponibilità
                if (!$product->inventory || $product->inventory->quantity < $item['quantity']) {
                    throw new \Exception("Stock insufficiente per {$product->denominazione_commerciale}");
                }

                $subtotal = $item['quantity'] * $item['unit_price'];

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $subtotal,
                ]);

                $product->inventory->decrement('quantity', $item['quantity']);

                $total += $subtotal;
            }

            $sale->update(['total_amount' => $total]);

            CashMovement::create([
                'type' => 'in',
                'amount' => $total,
                'description' => "Vendita #{$sale->id}",
            ]);
        });

        return redirect()->back()->with('success', 'Vendita registrata correttamente.');
    }


    public function findByBarcode(string $ean)
    {
        $barcode = ProductBarcode::with('product')->where('ean', $ean)->first();

        if (!$barcode) {
            return response()->json(null, 404);
        }

        $product = $barcode->product;

        return response()->json([
            'product_id' => $product->id,
            'codice' => $product->codice,
            'denominazione' => $product->denominazione_commerciale,
            'prezzo_confezione_euro' => $product->prezzo_confezione_euro,
        ]);
    }
}
