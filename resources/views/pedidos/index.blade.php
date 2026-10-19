<x-cliente-layout heading="Mis pedidos">
    @if ($enCurso)
        <h2 class="text-lg font-extrabold">En curso</h2>
        <div class="mt-3 flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-espuma-navy bg-white px-6 py-5">
            <div>
                <div class="flex items-center gap-3">
                    <span class="font-extrabold">#{{ $enCurso->codigo }}</span>
                    <span class="rounded-full bg-espuma-yellow px-3 py-0.5 text-xs font-bold">{{ $enCurso->estado_texto }}</span>
                </div>
                <p class="mt-1 text-sm text-espuma-muted">{{ $enCurso->servicio }} · {{ $enCurso->kg }} kg · Entrega {{ optional($enCurso->entrega_estimada)->translatedFormat('j M Y') ?? 'por confirmar' }}</p>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-2xl font-black">${{ number_format($enCurso->total, 2) }}</span>
                @if ($enCurso->pendiente_pago)
                    <span class="text-sm font-bold text-red-700">Por pagar</span>
                @endif
            </div>
        </div>
    @endif

    <div class="mt-10 flex items-end justify-between">
        <h2 class="text-lg font-extrabold">Entregados</h2>
        <span class="text-sm text-espuma-muted">{{ $entregados->count() }} pedidos</span>
    </div>

    <div class="mt-3 overflow-x-auto rounded-2xl border border-espuma-navy bg-white">
        <table class="w-full text-sm">
            <thead class="bg-espuma-cream text-left text-xs uppercase text-espuma-muted">
                <tr>
                    <th class="px-6 py-4 font-bold">Código</th>
                    <th class="px-6 py-4 font-bold">Servicio</th>
                    <th class="px-6 py-4 font-bold">Fecha</th>
                    <th class="px-6 py-4 font-bold text-right">Total</th>
                    <th class="px-6 py-4 font-bold"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-espuma-navy/15">
                @forelse ($entregados as $p)
                    <tr>
                        <td class="px-6 py-4 font-bold">#{{ $p->codigo }}</td>
                        <td class="px-6 py-4">{{ $p->servicio }}</td>
                        <td class="px-6 py-4">{{ $p->fecha->translatedFormat('j M Y') }}</td>
                        <td class="px-6 py-4 text-right font-bold">${{ number_format($p->total, 2) }}</td>
                        <td class="px-6 py-4 text-right">
                            <form method="POST" action="{{ route('pedidos.repetir', $p) }}">
                                @csrf
                                <button class="rounded-lg border-2 border-espuma-navy px-3 py-1.5 text-xs font-bold hover:bg-espuma-navy hover:text-white">
                                    ↻ Repetir
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-6 text-center text-espuma-muted">Aún no tienes pedidos entregados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-cliente-layout>
