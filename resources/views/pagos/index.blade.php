<x-cliente-layout heading="Pagos">
    <div class="grid gap-6 xl:grid-cols-[1fr_340px]">
        <div>
            <div class="flex flex-wrap items-center justify-between gap-6 rounded-2xl bg-espuma-yellow px-8 py-7 border border-espuma-navy">
                <div>
                    <p class="text-sm font-bold">Saldo pendiente</p>
                    <p class="text-4xl font-black">${{ number_format($saldo, 2) }}</p>
                    @if ($pedidoPendiente)
                        <p class="mt-2 text-sm">Pedido #{{ $pedidoPendiente->codigo }} · {{ $pedidoPendiente->servicio }}</p>
                    @else
                        <p class="mt-2 text-sm">No tienes pagos pendientes.</p>
                    @endif
                </div>
                @if ($pedidoPendiente)
                    <button x-data x-on:click="$dispatch('abrir-pago')"
                            class="rounded-lg bg-espuma-navy px-6 py-3 font-bold text-espuma-yellow shadow-[3px_3px_0_#F5D25F]">
                        Pagar ahora
                    </button>
                @endif
            </div>

            <h3 class="mt-10 text-lg font-extrabold">Recibos</h3>
            <div class="mt-3 overflow-x-auto rounded-2xl border border-espuma-navy bg-white">
                <table class="w-full text-sm">
                    <thead class="bg-espuma-cream text-left text-xs uppercase text-espuma-muted">
                        <tr>
                            <th class="px-6 py-4 font-bold">Fecha</th>
                            <th class="px-6 py-4 font-bold">Pedido</th>
                            <th class="px-6 py-4 font-bold">Método</th>
                            <th class="px-6 py-4 font-bold">Estado</th>
                            <th class="px-6 py-4 font-bold text-right">Monto</th>
                            <th class="px-6 py-4 font-bold text-right">Recibo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-espuma-navy/15">
                        @forelse ($recibos as $r)
                            <tr>
                                <td class="px-6 py-4">{{ $r->created_at->translatedFormat('j M Y') }}</td>
                                <td class="px-6 py-4 font-bold">#{{ $r->pedido->codigo }}</td>
                                <td class="px-6 py-4">{{ $r->metodo_texto }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $estilo = match ($r->estado) {
                                            'confirmado', 'completado' => 'bg-espuma-green/20 text-green-800',
                                            'en_revision' => 'bg-espuma-yellow/50 text-espuma-navy',
                                            default => 'bg-espuma-navy/10 text-espuma-navy',
                                        };
                                    @endphp
                                    <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $estilo }}">{{ $r->estado_texto }}</span>
                                    @if ($r->comprobante)
                                        <a href="{{ asset('storage/'.$r->comprobante) }}" target="_blank"
                                           class="ml-2 text-xs font-bold underline">Ver comprobante</a>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right font-bold">${{ number_format($r->monto, 2) }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('recibos.descargar', $r) }}" class="font-bold underline">Descargar</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-6 text-center text-espuma-muted">Aún no tienes recibos.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <aside class="h-fit rounded-2xl border border-espuma-navy bg-white">
            <h3 class="border-b border-espuma-navy/15 px-6 py-5 text-lg font-extrabold">Tarjetas</h3>
            <ul class="divide-y divide-espuma-navy/15">
                @forelse ($tarjetas as $t)
                    <li class="flex items-center justify-between px-6 py-4">
                        <div>
                            <p class="font-bold">{{ $t->marca }} •••• {{ $t->ultimos }}</p>
                            <p class="text-xs text-espuma-muted">Vence {{ $t->vence }}</p>
                        </div>
                        @if ($t->predeterminada)
                            <span class="rounded bg-espuma-yellow px-2 py-0.5 text-[10px] font-bold uppercase">Predeterminada</span>
                        @endif
                    </li>
                @empty
                    <li class="px-6 py-6 text-sm text-espuma-muted">No tienes tarjetas guardadas.</li>
                @endforelse
            </ul>
            <div class="p-4">
                <a href="{{ route('pagos.tarjetas.create') }}"
                   class="block rounded-lg border-2 border-espuma-navy py-3 text-center text-sm font-bold hover:bg-espuma-navy hover:text-white">
                    + Agregar tarjeta
                </a>
            </div>
        </aside>
    </div>

    <livewire:modal-pago :pedido="$pedidoPendiente" />
</x-cliente-layout>
