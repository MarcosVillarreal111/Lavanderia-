<x-layouts.admin heading="Auditoría">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-espuma-navy bg-white p-5">
        <div>
            <h2 class="text-lg font-extrabold">Registro de movimientos</h2>
            <p class="text-sm text-espuma-muted">Cada avance o retroceso de un pedido queda registrado con el administrador que lo realizó.</p>
        </div>
        <div class="rounded-xl border-2 border-espuma-navy bg-espuma-yellow/40 px-4 py-2 text-sm">
            <p class="text-xs font-medium text-espuma-muted">Administrador en sesión</p>
            <p class="font-bold">{{ $admin->name }} · <span class="font-mono">{{ $admin->email }}</span></p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.auditoria.index') }}"
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
        <a href="{{ route('admin.auditoria.index') }}" class="rounded-lg border-2 border-espuma-navy px-4 py-2.5 text-sm font-bold">Limpiar</a>
    </form>

    <div class="overflow-x-auto rounded-2xl border border-espuma-navy/20 bg-white">
        <table class="w-full min-w-[1100px] border-collapse text-sm">
            <thead>
                <tr class="border-b border-espuma-navy/15 bg-espuma-navy text-left text-xs uppercase tracking-wide text-espuma-yellow">
                    <th class="px-4 py-3">Admin que atendió</th>
                    <th class="px-4 py-3">Nº pedido</th>
                    <th class="px-4 py-3">Factura</th>
                    <th class="px-4 py-3">Cliente</th>
                    <th class="px-4 py-3">Datos del cliente</th>
                    <th class="px-4 py-3">Fecha</th>
                    <th class="px-4 py-3">Hora</th>
                    <th class="px-4 py-3">Código QR</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-espuma-navy/10">
                @forelse ($actividades as $activity)
                    @php
                        $props = $activity->properties;
                        $causer = $activity->causer;
                        $qr = $props['qr'] ?? ('PEDIDO:'.($activity->subject?->codigo ?? ''));
                        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=110x110&data='.urlencode($qr);
                    @endphp
                    <tr class="align-top hover:bg-espuma-cream">
                        <td class="px-4 py-3">
                            <p class="font-bold">{{ $causer?->name ?? 'Sistema' }}</p>
                            <p class="font-mono text-xs text-espuma-muted">{{ $causer?->email ?? '—' }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-black">#{{ $props['pedido'] ?? ($activity->subject?->codigo ?? '—') }}</span>
                            <p class="mt-1 text-xs text-espuma-muted">{{ $activity->description }}</p>
                        </td>
                        <td class="px-4 py-3">
                            @if (!empty($props['factura']))
                                <span class="rounded bg-espuma-navy px-2 py-1 text-xs font-bold text-espuma-yellow">{{ $props['factura'] }}</span>
                            @else
                                <span class="text-xs text-espuma-muted">Sin factura</span>
                            @endif
                            @if ($activity->subject?->pago)
                                <p class="mt-1 text-xs text-espuma-muted">Pago: {{ $activity->subject->pago->metodo_texto }}</p>
                            @endif
                            @if ($activity->subject)
                                <a href="{{ route('admin.facturas.show', $activity->subject) }}" target="_blank"
                                   class="mt-1 inline-block text-xs font-bold underline">🖨️ Imprimir</a>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-xs text-espuma-muted">Nº cliente</p>
                            <p class="font-bold">{{ $props['cliente_numero'] ?? ($activity->subject?->user_id ?? '—') }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <p class="font-bold">{{ $props['cliente_nombre'] ?? '—' }}</p>
                            <p class="text-xs text-espuma-muted">{{ $props['cliente_email'] ?? '—' }}</p>
                            <p class="text-xs text-espuma-muted">Tel/WhatsApp: {{ $props['cliente_whatsapp'] ?? '—' }}</p>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $activity->created_at->translatedFormat('d/m/Y') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap font-mono text-xs">{{ $activity->created_at->format('H:i:s') }}</td>
                        <td class="px-4 py-3">
                            <img src="{{ $qrUrl }}" alt="QR {{ $props['pedido'] ?? '' }}" width="90" height="90"
                                 class="rounded-lg border border-espuma-navy/20 bg-white p-1">
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-10 text-center text-espuma-muted">
                            Aún no hay movimientos registrados. Avanza un pedido para generar la primera entrada.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $actividades->links() }}
    </div>
</x-layouts.admin>
