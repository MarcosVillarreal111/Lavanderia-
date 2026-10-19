<div>
    @if ($abierto)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-espuma-navy/40 p-4"
             x-data x-on:keydown.escape.window="$wire.cerrar()">
            <div class="w-full max-w-md rounded-2xl border-2 border-espuma-navy bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-espuma-navy/15 px-6 py-5">
                    <h3 class="text-xl font-extrabold">{{ $vista === 'exito' ? 'Pago' : 'Confirmar pago' }}</h3>
                    <button wire:click="cerrar" class="text-espuma-muted hover:text-espuma-navy" title="Cerrar">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M6 6l12 12M18 6L6 18"/>
                        </svg>
                    </button>
                </div>

                @if ($vista === 'exito')
                    <div class="p-8 text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-espuma-green/20">
                            <svg class="h-8 w-8 text-green-700" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="mt-4 text-xl font-black">Confirmado</p>
                        <p class="mt-2 text-sm text-espuma-muted">{{ $mensaje }}</p>
                        <button wire:click="cerrar"
                                class="mt-6 w-full rounded-lg bg-espuma-navy py-3.5 font-bold text-espuma-yellow">
                            Entendido
                        </button>
                    </div>
                @else
                    <div class="p-6">
                        @if ($pedido)
                            <p class="text-sm text-espuma-muted">Pedido #{{ $pedido->codigo }}</p>
                            <p class="mt-1 text-3xl font-black">${{ number_format($pedido->total, 2) }}</p>
                        @endif

                        <div class="mt-6 grid grid-cols-3 gap-2 text-xs font-bold">
                            @foreach (['tarjeta' => 'Tarjeta', 'transferencia' => 'Transferencia', 'efectivo' => 'Efectivo'] as $valor => $label)
                                <button type="button" wire:click="$set('metodo', '{{ $valor }}')"
                                        class="rounded-lg border-2 py-2.5 transition
                                            {{ $metodo === $valor ? 'border-espuma-navy bg-espuma-navy text-espuma-yellow' : 'border-espuma-navy/20 hover:border-espuma-navy' }}">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>

                        @if ($metodo === 'tarjeta')
                            <div class="mt-5 space-y-2">
                                @forelse ($tarjetas as $t)
                                    <label class="flex cursor-pointer items-center justify-between rounded-lg border-2 border-espuma-navy/15 px-4 py-3 hover:border-espuma-navy/40">
                                        <span class="flex items-center gap-3">
                                            <input type="radio" wire:model="tarjeta" value="{{ $t['id'] }}"
                                                   class="border-2 border-espuma-navy text-espuma-navy">
                                            <span class="text-sm font-bold">{{ $t['marca'] }} •••• {{ $t['ult'] }}</span>
                                        </span>
                                        <span class="text-xs text-espuma-muted">{{ $t['vence'] }}</span>
                                    </label>
                                @empty
                                    <p class="rounded-lg bg-espuma-yellow/30 px-4 py-3 text-sm">
                                        No tienes tarjetas guardadas.
                                        <a href="{{ route('pagos.tarjetas.create') }}" class="font-bold underline">Agrega una</a>.
                                    </p>
                                @endforelse
                                @error('tarjeta') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            </div>
                        @elseif ($metodo === 'transferencia')
                            <div class="mt-5 rounded-lg border border-espuma-navy/15 bg-espuma-cream p-4 text-sm">
                                <p class="font-bold">Transferencia bancaria</p>
                                <p class="mt-2 text-espuma-muted">CLABE: <span class="font-bold text-espuma-navy">0121 8000 1234 5678 90</span></p>
                                <p class="text-espuma-muted">Beneficiario: <span class="font-bold text-espuma-navy">Espuma S.A. de C.V.</span></p>
                            </div>
                            <div class="mt-4">
                                <label class="text-sm font-bold">Adjunta tu comprobante</label>
                                <input type="file" wire:model="comprobante" accept=".jpg,.jpeg,.png,.pdf"
                                       class="mt-1 block w-full rounded-lg border-2 border-espuma-navy px-3 py-2 text-sm">
                                <div wire:loading wire:target="comprobante" class="mt-1 text-xs text-espuma-muted">Subiendo…</div>
                                @error('comprobante') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            </div>
                        @else
                            <p class="mt-6 rounded-lg bg-espuma-yellow/30 px-4 py-3 text-sm">
                                Paga en efectivo al repartidor cuando entreguemos tu ropa. Confirmaremos tu pedido de inmediato.
                            </p>
                        @endif

                        @if ($metodo === 'tarjeta')
                            <button type="button" x-data="{ procesando: false }"
                                    x-on:click="if (!procesando) { procesando = true; setTimeout(() => $wire.confirmar(), 1600) }"
                                    x-bind:disabled="procesando"
                                    class="mt-6 w-full rounded-lg bg-espuma-navy py-3.5 font-bold text-espuma-yellow shadow-[3px_3px_0_#F5D25F] disabled:opacity-70">
                                <span x-show="!procesando" x-cloak>Pagar ${{ $pedido ? number_format($pedido->total, 2) : '' }}</span>
                                <span x-show="procesando" x-cloak>Esperando confirmación del banco…</span>
                            </button>
                        @else
                            <button wire:click="confirmar" wire:loading.attr="disabled"
                                    class="mt-6 w-full rounded-lg bg-espuma-navy py-3.5 font-bold text-espuma-yellow shadow-[3px_3px_0_#F5D25F] disabled:opacity-70">
                                @if ($metodo === 'transferencia')
                                    <span wire:loading.remove wire:target="confirmar,comprobante">Enviar comprobante</span>
                                    <span wire:loading wire:target="confirmar">Enviando…</span>
                                @else
                                    Confirmar pedido
                                @endif
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
