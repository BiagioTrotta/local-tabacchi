<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ route('homepage') }}">
            LocalTabacchi
        </a>

        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('products.index') }}">
                    Prodotti
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('products.inventory.form') }}">
                    Magazzino
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-warning" href="{{ route('resetWarehouse') }}">
                    Reset Magazzino
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-warning" href="{{ route('resetBarcode') }}">
                    Reset Barcode
                </a>
            </li>
        </ul>
    </div>
</nav>