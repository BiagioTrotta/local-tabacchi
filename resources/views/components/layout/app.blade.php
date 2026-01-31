<!DOCTYPE html>
<html lang="it">

<head>
    <title>{{ $title ?? 'LocalTabacchi' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>

    <main class="d-flex flex-column min-vh-100">
        <div class="sticky-top">
            <x-navigation.navbar />
        </div>

        <div>
            {{ $slot }}
        </div>

        <div class="container-fluid mt-auto">
            <div class="row">
                <div class="col-12 mt-5 px-0">
                    <x-footer.footer />
                </div>
            </div>
        </div>

    </main>

</body>

</html>