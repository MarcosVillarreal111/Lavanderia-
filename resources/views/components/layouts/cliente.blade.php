@props(['heading' => null, 'acciones' => null, 'title' => null])

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Espuma' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-espuma-cream text-espuma-navy antialiased">
<div class="flex min-h-screen">

    {{-- Sidebar --}}
    <aside class="hidden md:flex w-60 flex-col border-r border-espuma-navy/15 bg-white">
        <div class="h-16 flex items-center px-6 border-b border-espuma-navy/15">
            <span class="text-lg font-extrabold tracking-tight">Espuma</span>
        </div>

        <p class="px-6 pt-6 pb-2 text-xs font-medium text-espuma-muted">Mi cuenta</p>

        <nav class="px-3 space-y-1 text-sm font-semibold">
            @php
                $pagosPendientes = auth()->user()->pedidos()->where('pendiente_pago', true)->count();
                $links = [
                    ['route' => 'dashboard', 'label' => 'Inicio', 'icon' => 'M3 12l9-9 9 9M5 10v10h14V10'],
                    ['route' => 'pedidos.index', 'label' => 'Mis pedidos', 'icon' => 'M21 16V8l-9-5-9 5v8l9 5 9-5z'],
                    ['route' => 'pagos.index', 'label' => 'Pagos', 'icon' => 'M2 7h20v10H2z', 'badge' => $pagosPendientes],
                    ['route' => 'direcciones.index', 'label' => 'Direcciones', 'icon' => 'M12 21s-7-6.1-7-11a7 7 0 0114 0c0 4.9-7 11-7 11z'],
                    ['route' => 'profile.show', 'label' => 'Perfil', 'icon' => 'M12 12a4 4 0 100-8 4 4 0 000 8zM4 21a8 8 0 0116 0'],
                ];
            @endphp

            @foreach ($links as $link)
                @php $activo = request()->routeIs($link['route']) @endphp
                <a href="{{ route($link['route']) }}"
                   class="flex items-center justify-between rounded-lg px-3 py-2.5 transition
                          {{ $activo ? 'bg-espuma-navy text-espuma-yellow' : 'hover:bg-espuma-navy/5' }}">
                    <span class="flex items-center gap-3">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="{{ $link['icon'] }}"/>
                        </svg>
                        {{ $link['label'] }}
                    </span>
                    @if (!empty($link['badge']))
                        <span class="flex h-5 min-w-5 items-center justify-center rounded-full bg-espuma-yellow px-1.5 text-xs font-bold text-espuma-navy">
                            {{ $link['badge'] }}
                        </span>
                    @endif
                </a>
            @endforeach

            @if (auth()->user()->isAdmin())
                <a href="{{ route('admin.pedidos.index') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-espuma-navy transition hover:bg-espuma-navy/5">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 15a3 3 0 100-6 3 3 0 000 6z"/>
                        <path d="M19.4 15a1.7 1.7 0 00.3 1.9l.1.1a2 2 0 11-2.8 2.8l-.1-.1a1.7 1.7 0 00-2.9 1.2V21a2 2 0 11-4 0v-.1A1.7 1.7 0 007 19.4a1.7 1.7 0 00-1.9.3l-.1.1a2 2 0 11-2.8-2.8l.1-.1A1.7 1.7 0 003 15a1.7 1.7 0 00-1.7-1H1a2 2 0 110-4h.1A1.7 1.7 0 003 9a1.7 1.7 0 00-.3-1.9l-.1-.1a2 2 0 112.8-2.8l.1.1A1.7 1.7 0 009 3.7V3a2 2 0 114 0v.1A1.7 1.7 0 0017 4.6a1.7 1.7 0 001.9-.3l.1-.1a2 2 0 112.8 2.8l-.1.1A1.7 1.7 0 0021 9h.1a2 2 0 110 4H21a1.7 1.7 0 00-1.6 1z"/>
                    </svg>
                    Panel admin
                </a>
            @endif
        </nav>

        <div class="mt-auto flex items-center gap-3 border-t border-espuma-navy/15 p-4">
            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-espuma-yellow text-xs font-bold">
                {{ auth()->user()->initials }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="truncate text-sm font-bold">{{ auth()->user()->name }}</p>
                <p class="text-xs text-espuma-muted">Cliente desde {{ auth()->user()->created_at->year }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-espuma-muted hover:text-espuma-navy" title="Salir">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M15 3h4v18h-4M10 17l5-5-5-5M15 12H3"/>
                    </svg>
                </button>
            </form>
        </div>
    </aside>

    {{-- Contenido --}}
    <div class="flex-1 min-w-0 flex flex-col">
        <header class="flex h-16 items-center justify-between border-b border-espuma-navy/15 bg-white px-6 md:px-8">
            <h1 class="text-xl font-extrabold tracking-tight">{{ $heading ?? '' }}</h1>
            {{ $acciones ?? '' }}
        </header>

        <main class="p-6 md:p-8">
            {{ $slot }}
        </main>
    </div>
</div>
</body>
</html>
