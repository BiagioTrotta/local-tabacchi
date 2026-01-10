<x-layout.app :title="$title">
    <div class="container mt-4">
        <h1 class="mb-4">Elenco Prodotti</h1>

        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Codice</th>
                    <th>Denominazione</th>
                    <th>Categoria</th>
                    <th>Prezzo Kg (€)</th>
                    <th>Prezzo Confezione (€)</th>
                    <th>Tipo Confezione</th>
                    <th>Quantità (stecche)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td>{{ $product->codice }}</td>
                    <td>{{ $product->denominazione_commerciale }}</td>
                    <td>{{ $product->categoria }}</td>
                    <td>{{ number_format($product->prezzo_kg_euro, 2) }}</td>
                    <td>{{ number_format($product->prezzo_confezione_euro, 2) }}</td>
                    <td>{{ $product->tipo_confezione }}</td>
                    <td>{{ $product->inventory->quantity ?? 0 }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layout.app>