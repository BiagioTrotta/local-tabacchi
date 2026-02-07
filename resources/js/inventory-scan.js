document.addEventListener("DOMContentLoaded", () => {
    const eanInput = document.getElementById("ean");
    const codiceInput = document.getElementById("codice");
    const denominazioneInput = document.getElementById("denominazione");
    const tipoConfezioneInput = document.getElementById("tipo_confezione");
    const prezzoConfezioneInput = document.getElementById("prezzo_confezione");
    const form = document.getElementById("inventory-form");
    const itemsInput = document.getElementById("items");

    const scannedItems = {};
    eanInput.focus();

    function addItemToList(item) {
        const key = item.ean || item.codice;

        if (!key) {
            alert("Prodotto mancante di codice o EAN");
            return;
        }

        const qty = parseInt(document.getElementById("quantity").value) || 1;

        if (!scannedItems[key]) {
            scannedItems[key] = {
                ean: item.ean || null,
                codice: item.codice,
                denominazione: item.denominazione,
                tipo: item.tipo,
                prezzo: parseFloat(item.prezzo) || 0,
                quantity: qty,
            };
        } else {
            scannedItems[key].quantity += qty;
        }

        renderTable();

        // reset
        eanInput.value = "";
        codiceInput.value = "";
        denominazioneInput.value = "";
        tipoConfezioneInput.value = "";
        prezzoConfezioneInput.value = "";
        document.getElementById("quantity").value = "";
        eanInput.focus();
    }

    // Scan barcode
    eanInput.addEventListener("keydown", (e) => {
        if (e.key !== "Enter") return;
        e.preventDefault();

        const ean = eanInput.value.trim();
        if (!ean) return;

        fetch(`/products/by-barcode/${ean}`)
            .then((res) => (res.ok ? res.json() : Promise.reject()))
            .then((data) => {
                addItemToList({
                    ean: ean,
                    codice: data.codice,
                    denominazione: data.denominazione,
                    tipo: data.tipo_confezione,
                    prezzo: data.prezzo_confezione_euro,
                });
            })
            .catch(() => {
                // barcode non trovato → aggiunta manuale
                const denom = denominazioneInput.value.trim();
                const cod = codiceInput.value.trim();

                if (!denom && !cod) {
                    alert(
                        "Prodotto non trovato: inserire codice o denominazione manualmente",
                    );
                    return;
                }

                addItemToList({
                    ean: ean || null,
                    codice: cod || null,
                    denominazione: denom || "Prodotto senza nome",
                    tipo: tipoConfezioneInput.value || "N/D",
                    prezzo: prezzoConfezioneInput.value || 0,
                });
            });
    });

    // Selezione manuale da denominazione
    denominazioneInput.addEventListener("change", () => {
        const opt = document.querySelector(
            `#denominazioni option[value="${denominazioneInput.value}"]`,
        );
        if (!opt) return;

        addItemToList({
            ean: eanInput.value || opt.dataset.ean || null,
            codice: opt.dataset.codice,
            denominazione: opt.dataset.denominazione,
            tipo: opt.dataset.tipo,
            prezzo: opt.dataset.prezzo,
        });
    });

    // Selezione manuale da codice
    codiceInput.addEventListener("change", () => {
        const opt = document.querySelector(
            `#codici option[value="${codiceInput.value}"]`,
        );
        if (!opt) return;

        addItemToList({
            ean: eanInput.value || opt.dataset.ean || null,
            codice: codiceInput.value,
            denominazione: opt.dataset.denominazione,
            tipo: opt.dataset.tipo,
            prezzo: opt.dataset.prezzo,
        });
    });

    function renderTable() {
        const tbody = document.querySelector("#scan-table tbody");
        tbody.innerHTML = "";

        let totalQty = 0;
        let totalPrice = 0;

        Object.values(scannedItems).forEach((item) => {
            totalQty += item.quantity;
            totalPrice += item.quantity * item.prezzo;

            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td>${item.ean || ""}</td>
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

        document.getElementById("total-qty").textContent = totalQty;
        document.getElementById("total-price").textContent =
            totalPrice.toFixed(2) + " €";
    }

    // Remove row
    document.addEventListener("click", (e) => {
        const btn = e.target.closest("button[data-key]");
        if (!btn) return;

        delete scannedItems[btn.dataset.key];
        renderTable();
    });

    // Submit form
    form.addEventListener("submit", (e) => {
        if (Object.keys(scannedItems).length === 0) {
            e.preventDefault();
            alert("Nessun prodotto inserito");
            return;
        }

        itemsInput.value = JSON.stringify(Object.values(scannedItems));
    });
});
