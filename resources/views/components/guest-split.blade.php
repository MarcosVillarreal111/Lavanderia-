@props(['title' => null])

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Espuma' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-espuma-navy antialiased">
<div class="min-h-screen grid md:grid-cols-2 bg-white font-sans text-espuma-navy">
    <section class="relative hidden md:flex flex-col justify-between bg-espuma-yellow p-12 overflow-hidden">
        <img src="{{ asset('img/lavanderia.jpg') }}" alt="" class="absolute inset-0 h-full w-full object-cover mix-blend-multiply opacity-40">
        <div class="relative">
            <a href="/" class="text-xl font-extrabold">Espuma</a>
            <h1 class="mt-24 text-5xl font-black leading-tight">Tu ropa te espera limpia y doblada.</h1>
            <p class="mt-6 text-lg">Entra para ver el estado de tus pedidos y agendar la próxima recogida.</p>
        </div>
        <p class="relative border-t-2 border-espuma-navy pt-4 text-sm font-bold">
            Lavado y secado desde $3.50/kg &nbsp;|&nbsp; Entrega en 48 h
        </p>
    </section>

    <section class="flex flex-col p-6 md:p-16">
        <div class="flex items-center justify-end text-sm font-bold">
            <a href="/" class="inline-flex items-center gap-2 rounded-lg border-2 border-espuma-navy px-4 py-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Volver al inicio
            </a>
        </div>
        <div class="my-auto mx-auto w-full max-w-sm">
            {{ $slot }}
        </div>
    </section>
</div>
@livewireScripts
</body>
</html>
