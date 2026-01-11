<x-layout.app :title="$title">
    <form action="{{ route('products.inventory.move') }}" method="POST" class="card shadow-sm border-0">
    @csrf

    <div class="card-body">

        {{-- 🔍 Scansione --}}
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold">
                    EAN (codice a barre)
                </label>
                <input
                    type="text"
                    name="ean"
                    id="ean"
                    class="form-control form-control-lg"
                    placeholder="Scansiona barcode"
                    autofocus
                >
            </div>

            <div class="col-md-3">
                <label class="form-label">
                    Codice ADM
                </label>
                <input
                    type="number"
                    name="codice"
                    id="codice"
                    list="codici"
                    class="form-control"
                    placeholder="Codice"
                >
                <datalist id="codici">
                    @foreach($products as $product)
                        <option value="{{ $product->codice }}">
                    @endforeach
                </datalist>
            </div>

            <div class="col-md-5">
                <label class="form-label">
                    Denominazione
                </label>
                <input
                    type="text"
                    name="denominazione"
                    id="denominazione"
                    list="denominazioni"
                    class="form-control"
                    placeholder="Nome prodotto"
                >
                <datalist id="denominazioni">
                    @foreach($products as $product)
                        <option
                            value="{{ $product->denominazione_commerciale }}"
                            data-codice="{{ $product->codice }}"
                            data-tipo="{{ $product->tipo_confezione }}"
                        >
                    @endforeach
                </datalist>
            </div>
        </div>

        <hr class="my-4">

        {{-- 📦 Movimento --}}
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">
                    Quantità (stecche)
                </label>
                <input
                    type="number"
                    name="quantity"
                    class="form-control"
                    min="1"
                    required
                >
            </div>

            <div class="col-md-2">
                <label class="form-label">
                    Tipo movimento
                </label>
                <select name="type" class="form-select" required>
                    <option value="carico">Carico</option>
                    <option value="scarico">Scarico</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">
                    Tipo confezione
                </label>
                <input
                    type="text"
                    id="tipo_confezione"
                    class="form-control bg-light"
                    readonly
                >
            </div>
        </div>

        {{-- 📝 Note --}}
        <div class="mt-4">
            <label class="form-label">
                Note
            </label>
            <input
                type="text"
                name="note"
                class="form-control"
                placeholder="Eventuali note"
            >
        </div>

    </div>

    {{-- ✅ Azione --}}
    <div class="card-footer bg-white border-0 text-end">
        <button type="submit" class="btn btn-primary btn-lg px-4">
            Registra movimento
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const eanInput = document.getElementById('ean');
    const codiceInput = document.getElementById('codice');
    const denominazioneInput = document.getElementById('denominazione');
    const tipoConfezioneInput = document.getElementById('tipo_confezione');

    eanInput.focus();

    // 🔁 Denominazione → codice + tipo confezione
    denominazioneInput.addEventListener('input', function () {
        const val = this.value;
        const options = document.querySelectorAll('#denominazioni option');
        let found = false;

        options.forEach(option => {
            if (option.value === val) {
                codiceInput.value = option.dataset.codice;
                tipoConfezioneInput.value = option.dataset.tipo;
                found = true;
            }
        });

        if (!found) {
            codiceInput.value = '';
            tipoConfezioneInput.value = '';
        }
    });

    // 📦 BARCODE SCAN (modo robusto)
    eanInput.addEventListener('keydown', function (e) {
        if (e.key !== 'Enter') return;

        e.preventDefault(); // ⛔ blocca submit automatico

        const ean = eanInput.value.trim();
        if (!ean) return;

        fetch(`/products/by-barcode/${ean}`)
            .then(res => {
                if (!res.ok) throw new Error();
                return res.json();
            })
            .then(data => {
                codiceInput.value = data.codice;
                denominazioneInput.value = data.denominazione;
                tipoConfezioneInput.value = data.tipo_confezione;
            })
            .catch(() => {
                codiceInput.value = '';
                denominazioneInput.value = '';
                tipoConfezioneInput.value = '';
            })
            .finally(() => {
                eanInput.select(); // pronto per il prossimo scan
            });
    });
});
</script>





</x-layout.app>