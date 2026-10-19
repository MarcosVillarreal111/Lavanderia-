@props(['heading' => null, 'title' => null])

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Panel · Espuma' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-espuma-cream text-espuma-navy antialiased">
<div class="flex min-h-screen">

    <aside class="hidden md:flex w-60 flex-col border-r border-espuma-navy/15 bg-white">
        <div class="h-16 flex items-center gap-2 px-6 border-b border-espuma-navy/15">
            <span class="text-lg font-extrabold tracking-tight">Espuma</span>
            <span class="rounded bg-espuma-navy px-1.5 py-0.5 text-[10px] font-bold text-espuma-yellow">ADMIN</span>
        </div>

        <p class="px-6 pt-6 pb-2 text-xs font-medium text-espuma-muted">Operación</p>

        <nav class="px-3 space-y-1 text-sm font-semibold">
            <a href="{{ route('admin.pedidos.index') }}"
               class="flex items-center gap-3 rounded-lg px-3 py-2.5 transition
                      {{ request()->routeIs('admin.pedidos.*') ? 'bg-espuma-navy text-espuma-yellow' : 'hover:bg-espuma-navy/5' }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M21 16V8l-9-5-9 5v8l9 5 9-5z"/>
                </svg>
                Pedidos
            </a>

            <a href="{{ route('admin.auditoria.index') }}"
               class="flex items-center gap-3 rounded-lg px-3 py-2.5 transition
                      {{ request()->routeIs('admin.auditoria.*') ? 'bg-espuma-navy text-espuma-yellow' : 'hover:bg-espuma-navy/5' }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M9 12h6M9 16h6M9 8h6M5 3h14v18H5z"/>
                </svg>
                Auditoría
            </a>

            <a href="{{ route('admin.notificaciones.index') }}"
               class="flex items-center justify-between rounded-lg px-3 py-2.5 transition
                      {{ request()->routeIs('admin.notificaciones.*') ? 'bg-espuma-navy text-espuma-yellow' : 'hover:bg-espuma-navy/5' }}">
                <span class="flex items-center gap-3">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M18 8a6 6 0 10-12 0c0 7-3 9-3 9h18s-3-2-3-9M13.7 21a2 2 0 01-3.4 0"/>
                    </svg>
                    Notificaciones
                </span>
                @if ($noLeidas = auth()->user()->unreadNotifications()->count())
                    <span class="flex h-5 min-w-5 items-center justify-center rounded-full bg-espuma-navy px-1.5 text-xs font-bold text-espuma-yellow">{{ $noLeidas }}</span>
                @endif
            </a>

            <a href="{{ route('admin.registros.index') }}"
               class="flex items-center gap-3 rounded-lg px-3 py-2.5 transition
                      {{ request()->routeIs('admin.registros.*') ? 'bg-espuma-navy text-espuma-yellow' : 'hover:bg-espuma-navy/5' }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M4 6h16M4 12h16M4 18h10"/>
                </svg>
                Ver registros
            </a>
        </nav>

        <div class="mt-auto flex items-center gap-3 border-t border-espuma-navy/15 p-4">
            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-espuma-yellow text-xs font-bold">
                {{ auth()->user()->initials }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="truncate text-sm font-bold">{{ auth()->user()->name }}</p>
                <p class="truncate font-mono text-xs text-espuma-muted">{{ auth()->user()->email }}</p>
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

    <div class="flex-1 min-w-0 flex flex-col">
        @php $noLeidas = auth()->user()->unreadNotifications()->count(); @endphp
        <header class="flex h-16 items-center justify-between border-b border-espuma-navy/15 bg-white px-6 md:px-8">
            <h1 class="text-xl font-extrabold tracking-tight">{{ $heading ?? '' }}</h1>
            <a href="{{ route('admin.notificaciones.index') }}" class="relative text-espuma-navy" title="Notificaciones">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M18 8a6 6 0 10-12 0c0 7-3 9-3 9h18s-3-2-3-9M13.7 21a2 2 0 01-3.4 0"/>
                </svg>
                @if ($noLeidas > 0)
                    <span class="absolute -right-2 -top-2 flex h-5 min-w-5 items-center justify-center rounded-full bg-espuma-navy px-1 text-[10px] font-bold text-espuma-yellow">
                        {{ $noLeidas }}
                    </span>
                @endif
            </a>
        </header>

        <main class="p-6 md:p-8">
            {{ $slot }}
        </main>
    </div>
</div>
</body>
</html>
