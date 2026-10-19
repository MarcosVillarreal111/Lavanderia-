<x-layouts.admin heading="Notificaciones">
    @if (session('status'))
        <div class="mb-6 rounded-lg border-2 border-espuma-navy bg-espuma-yellow/40 px-4 py-3 text-sm font-bold">
            {{ session('status') }}
        </div>
    @endif

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-extrabold">Nuevos pedidos en proceso</h2>
            <p class="text-sm text-espuma-muted">Avisos generados cuando un cliente crea un pedido desde la web.</p>
        </div>
        <form method="POST" action="{{ route('admin.notificaciones.leer') }}">
            @csrf
            <button class="rounded-lg border-2 border-espuma-navy px-4 py-2 text-sm font-bold hover:bg-espuma-navy hover:text-white">
                Marcar todas como leídas
            </button>
        </form>
    </div>

    <div class="space-y-3">
        @forelse ($notificaciones as $n)
            <a href="{{ route('admin.pedidos.index') }}"
               class="flex items-start gap-4 rounded-2xl border bg-white p-5 transition hover:border-espuma-navy
                      {{ $n->read_at ? 'border-espuma-navy/15' : 'border-espuma-navy bg-espuma-yellow/20' }}">
                <span class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-espuma-navy text-espuma-yellow">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 3v18h18M7 15l4-4 3 3 5-6"/>
                    </svg>
                </span>
                <div class="flex-1">
                    <p class="font-bold">Pedido #{{ $n->data['codigo'] ?? '—' }} · {{ $n->data['cliente_nombre'] ?? $n->data['cliente'] ?? 'Cliente' }}</p>
                    <p class="text-sm text-espuma-muted">
                        Total estimado: ${{ number_format($n->data['total'] ?? 0, 2) }} · Creado {{ $n->created_at->diffForHumans() }}
                    </p>
                </div>
                @unless ($n->read_at)
                    <span class="rounded-full bg-espuma-navy px-2 py-1 text-[10px] font-bold uppercase text-espuma-yellow">Nuevo</span>
                @endunless
            </a>
        @empty
            <div class="rounded-2xl border border-espuma-navy/15 bg-white p-10 text-center text-espuma-muted">
                No tienes notificaciones.
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $notificaciones->links() }}
    </div>
</x-layouts.admin>
