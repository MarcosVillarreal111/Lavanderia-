<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Espuma · Lavandería a domicilio</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-espuma-navy antialiased">
<section class="relative min-h-screen bg-espuma-yellow overflow-hidden">
    <img src="{{ asset('img/lavanderia.jpg') }}" alt=""
         class="absolute inset-0 h-full w-full object-cover mix-blend-multiply opacity-40">

    <div class="relative">
        <header class="flex items-center justify-between border-b-2 border-espuma-navy px-6 py-5 md:px-12">
            <span class="text-xl font-extrabold">Espuma</span>
            <nav class="flex items-center gap-4 text-sm font-bold">
                <a href="{{ route('register') }}" class="hidden sm:inline">Registro para clientes</a>
                <span class="hidden sm:inline h-5 border-l-2 border-espuma-navy"></span>
                <a href="{{ route('login') }}"
                   class="rounded-lg border-2 border-espuma-navy px-4 py-2 hover:bg-espuma-navy hover:text-espuma-yellow transition">Login</a>
                <a href="{{ route('register') }}"
                   class="rounded-lg bg-espuma-navy px-4 py-2 text-espuma-yellow hover:opacity-90 transition">Register</a>
            </nav>
        </header>

        <div class="grid gap-12 px-6 py-12 md:grid-cols-2 md:px-12 md:py-20 items-center">
            <div>
                <h2 class="text-5xl md:text-6xl font-black leading-[1.05] tracking-tight">
                    Tu ropa limpia, sin que muevas un dedo.
                </h2>
                <p class="mt-6 max-w-md text-lg">
                    Recogemos, lavamos y entregamos en tu puerta. Crea tu cuenta, agenda tu primer pedido y sigue cada paso desde la web.
                </p>

                <ul class="mt-6 max-w-md space-y-2 text-sm font-bold">
                    <li>✓ Agenda la recogida en 1 minuto</li>
                    <li>✓ Sigue el estado de tu pedido en tiempo real</li>
                    <li>✓ Consulta tu historial y repite con un clic</li>
                </ul>

                <div class="mt-8 flex flex-wrap items-center gap-4">
                    <a href="https://wa.me/{{ config('services.whatsapp.number') }}?text=Quiero%20un%20pedido"
                       class="inline-flex items-center justify-center gap-3 rounded-xl border-2 border-espuma-navy bg-espuma-green px-8 py-5 text-lg font-black text-white shadow-[4px_4px_0_#1B2540] transition hover:-translate-y-0.5">
                        Escríbenos por WhatsApp
                    </a>
                </div>
            </div>

            <x-catalogo titulo="Tabla de precios" />
        </div>
    </div>
</section>
</body>
</html>
