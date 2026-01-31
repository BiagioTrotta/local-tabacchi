<x-layout.app :title="$title">

    <form action="{{ route('sales.store') }}" method="POST" id="sale-form">
        @csrf

        {{-- 🔍 SCANSIONE BARCODE --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-2">
                <h5 class="card-title mb-0 d-flex align-items-center">
                    <i class="bi bi-upc-scan me-2 text-primary"></i>
                    Scansione prodotto
                </h5>
            </div>
            <div class="card-body pt-2">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">EAN</label>
                        <input type="text" id="ean" class="form-control form-control-lg" placeholder="Scansiona barcode" autofocus>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🛒 CARRELLO --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom-0">
                <h5 class="card-title mb-0"><i class="bi bi-cart me-2"></i> Carrello</h5>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0" id="cart-table">
                    <thead class="table-light">
                        <tr>
                            <th>Prodotto</th>
                            <th class="text-center">Q.tà</th>
                            <th class="text-end">Prezzo</th>
                            <th class="text-end">Totale</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                <strong class="fs-5">Totale: € <span id="total">0.00</span></strong>
                <button type="submit" class="btn btn-success btn-lg px-4" id="confirm-sale" disabled>
                    <i class="bi bi-check-circle me-2"></i> Conferma vendita
                </button>
            </div>
        </div>

        {{-- 🔹 AGGIUNTA MANUALE --}}
        <div class="mb-3 text-end">
            <button type="button" class="btn btn-outline-primary" id="manual-add-btn">
                <i class="bi bi-pencil-square me-1"></i> Aggiungi prodotto manualmente
            </button>
        </div>

        <div class="card shadow-sm border-0 mb-4 d-none" id="manual-add-card">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-2">
                <h5 class="card-title mb-0 d-flex align-items-center">
                    <i class="bi bi-pencil-square me-2 text-primary"></i>
                    Inserisci prodotto manualmente
                </h5>
            </div>
            <div class="card-body pt-2">
                <div class="row g-3 align-items-end">
                    {{-- Barcode --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">EAN (opzionale)</label>
                        <input type="text" id="manual-ean" class="form-control" placeholder="Barcode non registrato">
                    </div>

                    {{-- Prodotto --}}
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Prodotto</label>
                        <input list="products-list" id="manual-name" class="form-control">
                        <datalist id="products-list">
                            @foreach($products as $p)
                            <option value="{{ $p->denominazione_commerciale }}" data-price="{{ $p->prezzo_confezione_euro }}" data-id="{{ $p->id }}"></option>
                            @endforeach
                        </datalist>
                    </div>

                    {{-- Prezzo --}}
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Prezzo €</label>
                        <input type="number" id="manual-price" class="form-control" step="0.01">
                    </div>

                    {{-- Quantità --}}
                    <div class="col-md-1">
                        <label class="form-label fw-semibold">Q.tà</label>
                        <input type="number" id="manual-qty" class="form-control" min="1" value="1">
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <button type="button" class="btn btn-success w-100" id="add-manual-product">
                            <i class="bi bi-plus-circle me-1"></i> Aggiungi al carrello
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const eanInput = document.getElementById('ean');
            const cartBody = document.querySelector('#cart-table tbody');
            const totalEl = document.getElementById('total');
            const confirmBtn = document.getElementById('confirm-sale');
            const form = document.getElementById('sale-form');

            const manualBtn = document.getElementById('manual-add-btn');
            const manualCard = document.getElementById('manual-add-card');
            const manualName = document.getElementById('manual-name');
            const manualPrice = document.getElementById('manual-price');
            const manualQty = document.getElementById('manual-qty');
            const manualEan = document.getElementById('manual-ean');
            const manualAddBtn = document.getElementById('add-manual-product');

            let cart = [];

            // 📦 SCANSIONE BARCODE
            eanInput.addEventListener('keydown', e => {
                if (e.key !== 'Enter') return;
                e.preventDefault();
                const ean = eanInput.value.trim();
                if (!ean) return;

                fetch(`/products/by-barcode/${ean}`)
                    .then(res => res.ok ? res.json() : Promise.reject())
                    .then(data => {
                        addToCart({
                            product_id: data.product_id,
                            codice: data.codice,
                            denominazione: data.denominazione,
                            unit_price: data.prezzo_confezione_euro,
                            quantity: 1,
                            manual_ean: null
                        });
                    })
                    .catch(() => alert('Prodotto non trovato. Usa l\'aggiunta manuale.'));

                eanInput.value = '';
            });

            // ➕ AGGIUNTA AL CARRELLO
            function addToCart(product) {
                const qty = product.quantity || 1;

                const existing = cart.find(i => i.product_id === product.product_id && i.manual_ean === product.manual_ean);
                if (existing) {
                    existing.quantity += qty;
                } else {
                    cart.push(product);
                }

                renderCart();
            }

            // 🧾 RENDER CARRELLO
            function renderCart() {
                cartBody.innerHTML = '';
                let total = 0;

                cart.forEach((item, index) => {
                    const subtotal = item.quantity * item.unit_price;
                    total += subtotal;

                    cartBody.innerHTML += `
                <tr>
                    <td>${item.denominazione}</td>
                    <td class="text-center">${item.quantity}</td>
                    <td class="text-end">${item.unit_price.toFixed(2)}</td>
                    <td class="text-end">${subtotal.toFixed(2)}</td>
                    <td class="text-end">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem(${index})">
                            <i class="bi bi-x"></i>
                        </button>
                    </td>
                </tr>
            `;
                });

                totalEl.textContent = total.toFixed(2);
                confirmBtn.disabled = cart.length === 0;
            }

            window.removeItem = index => {
                cart.splice(index, 1);
                renderCart();
            };

            // 🔹 MANUALE
            manualBtn.addEventListener('click', () => manualCard.classList.toggle('d-none'));

            manualName.addEventListener('input', () => {
                const option = [...document.querySelectorAll('#products-list option')]
                    .find(o => o.value === manualName.value);
                if (option) {
                    manualPrice.value = option.dataset.price;
                    manualName.dataset.productId = option.dataset.id;
                }
            });

            manualAddBtn.addEventListener('click', () => {
                const name = manualName.value.trim();
                const price = parseFloat(manualPrice.value);
                const qty = parseInt(manualQty.value);
                const ean = manualEan.value.trim();
                const productId = manualName.dataset.productId;

                if (!name || !productId || isNaN(price) || price <= 0 || isNaN(qty) || qty <= 0) {
                    alert('Inserisci nome, prezzo e quantità validi!');
                    return;
                }

                addToCart({
                    product_id: productId,
                    codice: ean || 'manual-' + Date.now(),
                    denominazione: name,
                    unit_price: price,
                    quantity: qty,
                    manual_ean: ean || null
                });

                manualName.value = '';
                manualPrice.value = '';
                manualQty.value = 1;
                manualEan.value = '';
                delete manualName.dataset.productId;
            });

            // 📤 SUBMIT
            form.addEventListener('submit', () => {
                cart.forEach((item, i) => {
                    Object.entries(item).forEach(([key, value]) => {
                        if (value !== null) {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = `items[${i}][${key}]`;
                            input.value = value;
                            form.appendChild(input);
                        }
                    });
                });
            });
        });
    </script>

</x-layout.app>