<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ProductsSeeder extends Seeder
{
    public function run(): void
    {
        $path = storage_path('app/data/products.json');

        $products = json_decode(File::get($path), true);

        foreach ($products as $index => $item) {

            // normalizzazione chiavi (ADM / custom)
            $codice = $item['Codice'] ?? $item['codice'] ?? null;

            if (!$codice) {
                $this->command->warn("Record {$index} senza codice, saltato");
                continue;
            }

            Product::updateOrCreate(
                ['codice' => $codice],
                [
                    'categoria' => $item['Categoria']
                        ?? $item['categoria']
                        ?? null,

                    'denominazione_commerciale' => $item['Denominazione_commerciale']
                        ?? $item['denominazione']
                        ?? null,

                    'prezzo_kg_euro' => $item['Prezzo_kg_euro']
                        ?? $item['prezzo_kg']
                        ?? 0,

                    'prezzo_confezione_euro' => $item['Prezzo_confezione_euro']
                        ?? $item['prezzo_confezione']
                        ?? 0,

                    'tipo_confezione' => $item['Tipo_confezione']
                        ?? $item['tipo_confezione']
                        ?? null,
                ]
            );
        }
    }
}
