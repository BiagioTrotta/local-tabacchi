<x-layout.app :title="$title">
    <div class="container">
        <div class="row mt-5">
            <form action="{{ route('products.inventory.move') }}" method="POST" class="inventory-form-wrapper" id="inventory-form">
                @csrf

                {{-- 🔍 Sezione Scansione --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-2">
                        <h5 class="card-title mb-0 d-flex align-items-center">
                            <i class="bi bi-upc-scan me-2 text-primary"></i>
                            Scansione Prodotto
                        </h5>
                    </div>
                    <div class="card-body pt-2">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    <input type="hidden" name="items" id="items">
                                    <i class="bi bi-upc me-1 text-muted"></i>
                                    EAN (codice a barre)
                                </label>
                                <input
                                    type="text"
                                    name="ean"
                                    id="ean"
                                    class="form-control form-control-lg ean-input"
                                    placeholder="Scansiona barcode"
                                    autofocus>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-hash me-1 text-muted"></i>
                                    Codice ADM
                                </label>
                                <input
                                    type="number"
                                    name="codice"
                                    id="codice"
                                    list="codici"
                                    class="form-control"
                                    placeholder="Codice">
                                <datalist id="denominazioni">
                                    @foreach($products as $product)
                                    <option
                                        value="{{ $product->codice }} – {{ $product->denominazione_commerciale }} ({{ $product->tipo_confezione }})"
                                        data-ean="{{ $product->barcodes->first()?->ean }}"
                                        data-codice="{{ $product->codice }}"
                                        data-denominazione="{{ $product->denominazione_commerciale }}"
                                        data-tipo="{{ $product->tipo_confezione }}"
                                        data-prezzo="{{ $product->prezzo_confezione_euro }}">
                                    </option>
                                    @endforeach
                                </datalist>

                            </div>

                            <div class="col-md-5">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-tag me-1 text-muted"></i>
                                    Denominazione
                                </label>
                                <input
                                    type="text"
                                    name="denominazione"
                                    id="denominazione"
                                    list="denominazioni"
                                    class="form-control"
                                    placeholder="Nome prodotto">
                                <datalist id="denominazioni">
                                    @foreach($products as $product)
                                    <option
                                        value="{{ $product->codice }} – {{ $product->denominazione_commerciale }} ({{ $product->tipo_confezione }})"
                                        data-ean="{{ $product->barcodes->first()?->ean }}"
                                        data-codice="{{ $product->codice }}"
                                        data-denominazione="{{ $product->denominazione_commerciale }}"
                                        data-tipo="{{ $product->tipo_confezione }}"
                                        data-prezzo="{{ $product->prezzo_confezione_euro }}">
                                    </option>
                                    @endforeach
                                </datalist>

                            </div>
                        </div>
                    </div>
                </div>

                {{-- 📦 Sezione Movimento --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-2">
                        <h5 class="card-title mb-0 d-flex align-items-center">
                            <i class="bi bi-box-seam me-2 text-primary"></i>
                            Dettagli Movimento
                        </h5>
                    </div>
                    <div class="card-body pt-2">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-stack me-1 text-muted"></i>
                                    Quantità (1 unità predefinita)
                                </label>
                                <input
                                    type="number"
                                    id="quantity"
                                    class="form-control"
                                    min="1">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-arrow-left-right me-1 text-muted"></i>
                                    Tipo movimento
                                </label>
                                <select name="type" class="form-select" required>
                                    <option value="carico">Carico</option>
                                    <option value="scarico">Scarico</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-muted">
                                    <i class="bi bi-box me-1"></i>
                                    Tipo confezione
                                </label>
                                <input
                                    type="text"
                                    id="tipo_confezione"
                                    class="form-control readonly-field"
                                    readonly>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fw-semibold text-muted">
                                    <i class="bi bi-currency-euro me-1"></i>
                                    Prezzo unitario (€)
                                </label>
                                <input
                                    type="text"
                                    id="prezzo_confezione"
                                    class="form-control readonly-field text-end"
                                    readonly>
                            </div>
                        </div>

                        {{-- 📝 Note --}}
                        <div class="mt-4">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-journal-text me-1 text-muted"></i>
                                Note
                            </label>
                            <input
                                type="text"
                                name="note"
                                class="form-control"
                                placeholder="Eventuali note">
                        </div>
                    </div>

                    {{-- 📋 Lista prodotti scansionati --}}
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">
                                <i class="bi bi-list-check me-2 text-primary"></i>
                                Prodotti scansionati
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm mb-0" id="scan-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>EAN</th>
                                        <th>Prodotto</th>
                                        <th>Tipo</th>
                                        <th class="text-center">Q.tà</th>
                                        <th class="text-end">Prezzo</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="3" class="text-end">Totale</th>
                                        <th class="text-center" id="total-qty">0</th>
                                        <th class="text-end" id="total-price">0,00 €</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>


                    {{-- ✅ Azione --}}
                    <div class="card-footer bg-transparent border-top-0 pt-2 pb-4">
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary btn-lg px-5 submit-btn">
                                <i class="bi bi-check-circle me-2"></i>
                                Registra movimento
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- <script>
        document.addEventListener('DOMContentLoaded', () => {

            const eanInput = document.getElementById('ean');
            const codiceInput = document.getElementById('codice');
            const denominazioneInput = document.getElementById('denominazione');
            const tipoConfezioneInput = document.getElementById('tipo_confezione');
            const prezzoConfezioneInput = document.getElementById('prezzo_confezione');
            const form = document.getElementById('inventory-form');
            const itemsInput = document.getElementById('items');

            const scannedItems = {};
            eanInput.focus();

            function addItemToList(item) {

                const key = item.ean || item.codice;

                if (!key) {
                    alert('Prodotto mancante di codice o EAN');
                    return;
                }

                const qty = parseInt(document.getElementById('quantity').value) || 1;

                if (!scannedItems[key]) {
                    scannedItems[key] = {
                        ean: item.ean || null,
                        codice: item.codice,
                        denominazione: item.denominazione,
                        tipo: item.tipo,
                        prezzo: parseFloat(item.prezzo) || 0,
                        quantity: qty
                    };
                } else {
                    scannedItems[key].quantity += qty;
                }

                renderTable();

                // reset
                eanInput.value = '';
                codiceInput.value = '';
                denominazioneInput.value = '';
                tipoConfezioneInput.value = '';
                prezzoConfezioneInput.value = '';
                document.getElementById('quantity').value = '';
                eanInput.focus();
            }

            // Scan barcode
            eanInput.addEventListener('keydown', e => {
                if (e.key !== 'Enter') return;
                e.preventDefault();

                const ean = eanInput.value.trim();
                if (!ean) return;

                fetch(`/products/by-barcode/${ean}`)
                    .then(res => res.ok ? res.json() : Promise.reject())
                    .then(data => {
                        addItemToList({
                            ean: ean,
                            codice: data.codice,
                            denominazione: data.denominazione,
                            tipo: data.tipo_confezione,
                            prezzo: data.prezzo_confezione_euro
                        });
                    })
                    .catch(() => {
                        // barcode non trovato → aggiunta manuale
                        const denom = denominazioneInput.value.trim();
                        const cod = codiceInput.value.trim();

                        if (!denom && !cod) {
                            alert('Prodotto non trovato: inserire codice o denominazione manualmente');
                            return;
                        }

                        addItemToList({
                            ean: ean || null,
                            codice: cod || null,
                            denominazione: denom || 'Prodotto senza nome',
                            tipo: tipoConfezioneInput.value || 'N/D',
                            prezzo: prezzoConfezioneInput.value || 0
                        });
                    });
            });

            // Selezione manuale da denominazione
            denominazioneInput.addEventListener('change', () => {
                const opt = document.querySelector(
                    `#denominazioni option[value="${denominazioneInput.value}"]`
                );
                if (!opt) return;

                addItemToList({
                    ean: eanInput.value || opt.dataset.ean || null,
                    codice: opt.dataset.codice,
                    denominazione: opt.dataset.denominazione,
                    tipo: opt.dataset.tipo,
                    prezzo: opt.dataset.prezzo
                });
            });

            // Selezione manuale da codice
            codiceInput.addEventListener('change', () => {
                const opt = document.querySelector(
                    `#codici option[value="${codiceInput.value}"]`
                );
                if (!opt) return;

                addItemToList({
                    ean: eanInput.value || opt.dataset.ean || null,
                    codice: codiceInput.value,
                    denominazione: opt.dataset.denominazione,
                    tipo: opt.dataset.tipo,
                    prezzo: opt.dataset.prezzo
                });
            });

            function renderTable() {
                const tbody = document.querySelector('#scan-table tbody');
                tbody.innerHTML = '';

                let totalQty = 0;
                let totalPrice = 0;

                Object.values(scannedItems).forEach(item => {
                    totalQty += item.quantity;
                    totalPrice += item.quantity * item.prezzo;

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                <td>${item.ean || ''}</td>
                <td>${item.denominazione}</td>
                <td>${item.tipo}</td>
                <td class="text-center">${item.quantity}</td>
                <td class="text-end">${(item.quantity * item.prezzo).toFixed(2)} €</td>
                <td class="text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger" data-key="${item.ean || item.codice}">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
                    tbody.appendChild(tr);
                });

                document.getElementById('total-qty').textContent = totalQty;
                document.getElementById('total-price').textContent = totalPrice.toFixed(2) + ' €';
            }

            // Remove row
            document.addEventListener('click', e => {
                const btn = e.target.closest('button[data-key]');
                if (!btn) return;

                delete scannedItems[btn.dataset.key];
                renderTable();
            });

            // Submit form
            form.addEventListener('submit', e => {
                if (Object.keys(scannedItems).length === 0) {
                    e.preventDefault();
                    alert('Nessun prodotto inserito');
                    return;
                }

                itemsInput.value = JSON.stringify(Object.values(scannedItems));
            });

        });
    </script> -->
</x-layout.app>