<x-layouts.admin heading="Registros">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-espuma-navy bg-white p-5">
        <div>
            <h2 class="text-lg font-extrabold">Registros de pedidos entregados</h2>
            <p class="text-sm text-espuma-muted">Aquí se archivan los pedidos que ya fueron entregados y sacados del tablero.</p>
        </div>
        <div class="rounded-xl border-2 border-espuma-navy bg-espuma-yellow/40 px-4 py-2 text-sm">
            <p class="text-xs font-medium text-espuma-muted">Total archivados</p>
            <p class="font-black">{{ $registros->total() }}</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.registros.index') }}"
          class="mb-6 flex flex-wrap items-end gap-3 rounded-2xl border border-espuma-navy/20 bg-white p-5">
        <div>
            <label class="text-xs font-bold text-espuma-muted">Buscar</label>
            <input name="q" value="{{ $filtros['q'] }}" placeholder="Código o cliente"
                   class="mt-1 block w-52 rounded-lg border-2 border-espuma-navy px-3 py-2 text-sm">
        </div>
        <div>
            <label class="text-xs font-bold text-espuma-muted">Desde</label>
            <input type="date" name="desde" value="{{ $filtros['desde'] }}"
                   class="mt-1 block rounded-lg border-2 border-espuma-navy px-3 py-2 text-sm">
        </div>
        <div>
            <label class="text-xs font-bold text-espuma-muted">Hasta</label>
            <input type="date" name="hasta" value="{{ $filtros['hasta'] }}"
                   class="mt-1 block rounded-lg border-2 border-espuma-navy px-3 py-2 text-sm">
        </div>
        <button class="rounded-lg bg-espuma-navy px-4 py-2.5 text-sm font-bold text-espuma-yellow">Filtrar</button>
        <a href="{{ route('admin.registros.index') }}" class="rounded-lg border-2 border-espuma-navy px-4 py-2.5 text-sm font-bold">Limpiar</a>
    </form>

    <div class="overflow-x-auto rounded-2xl border border-espuma-navy/20 bg-white">
        <table class="w-full min-w-[900px] border-collapse text-sm">
            <thead>
                <tr class="border-b border-espuma-navy/15 bg-espuma-navy text-left text-xs uppercase tracking-wide text-espuma-yellow">
                    <th class="px-4 py-3">Fecha</th>
                    <th class="px-4 py-3">Hora</th>
                    <th class="px-4 py-3">ID Cliente</th>
                    <th class="px-4 py-3">Cliente</th>
                    <th class="px-4 py-3">Pedido</th>
                    <th class="px-4 py-3">Servicio</th>
                    <th class="px-4 py-3">Método de pago</th>
                    <th class="px-4 py-3">Total</th>
                    <th class="px-4 py-3">Factura</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-espuma-navy/10">
                @forelse ($registros as $r)
                    <tr class="align-top hover:bg-espuma-cream">
                        <td class="px-4 py-3 whitespace-nowrap">{{ $r->archivado_en?->translatedFormat('d/m/Y') ?? '—' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap font-mono text-xs">{{ $r->archivado_en?->format('H:i:s') ?? '—' }}</td>
                        <td class="px-4 py-3 font-bold">{{ $r->user_id }}</td>
                        <td class="px-4 py-3">
                            <p class="font-bold">{{ $r->user?->name ?? '—' }}</p>
                            <p class="text-xs text-espuma-muted">{{ $r->user?->email ?? '—' }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-black">#{{ $r->codigo }}</span>
                            <p class="text-xs text-espuma-muted">{{ $r->estado_texto }}</p>
                        </td>
                        <td class="px-4 py-3 text-xs text-espuma-muted">{{ $r->servicio }}</td>
                        <td class="px-4 py-3">
                            @if ($r->pago)
                                <p class="font-bold">{{ $r->pago->metodo_texto }}</p>
                                <p class="text-xs text-espuma-muted">{{ $r->pago->estado_texto }}</p>
                            @else
                                <span class="text-xs text-espuma-muted">Sin pago</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-bold">${{ number_format($r->total, 2) }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.facturas.show', $r) }}" target="_blank"
                               class="rounded-lg border-2 border-espuma-navy px-3 py-1.5 text-xs font-bold hover:bg-espuma-navy hover:text-white">
                                🖨️ Imprimir
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-10 text-center text-espuma-muted">
                            No hay pedidos archivados todavía.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $registros->links() }}
    </div>
</x-layouts.admin>
