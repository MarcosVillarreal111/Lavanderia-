@php
    $pedido = $this->pedido;
    $construyendo = $creando || $editando || ! $pedido;
@endphp

<div @if (! $construyendo) wire:poll.30s="refrescarEstado" @endif class="grid gap-6 xl:grid-cols-[1fr_340px]">
    @if ($creando || $editando || ! $pedido)
        <div class="space-y-6 xl:col-span-2">
            @if (! $creando && ! $editando)
                <div class="rounded-2xl border-2 border-espuma-navy bg-espuma-yellow px-8 py-10">
                    <span class="inline-block rounded-full bg-espuma-navy px-3 py-1 text-xs font-bold text-espuma-yellow">Pide 100% en línea</span>
                    <h2 class="mt-4 text-4xl font-black tracking-tight md:text-5xl">No tienes pedidos en curso. ¡Agenda el primero!</h2>
                    <p class="mt-3 max-w-2xl text-lg">
                        Elige tu servicio, la cantidad, agenda la recogida y sigue cada paso desde la web. Sin llamadas y sin depender de WhatsApp.
                    </p>

                    <div class="mt-8 grid gap-3 sm:grid-cols-3">
                        @foreach ([
                            ['M8 7V3m8 4V3M3 11h18M5 5h14v16H5z', 'Agenda en 1 minuto', 'Elige día, franja horaria y dirección de recogida.'],
                            ['M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z', 'Sigue cada paso', 'Confirmación, pago, recogida, proceso y entrega, en tiempo real.'],
                            ['M3 3v18h18M7 15l4-4 3 3 5-6', 'Tu historial', 'Consulta tus pedidos y repítelos con un solo clic.'],
                        ] as [$icono, $titulo, $texto])
                            <div class="rounded-xl border-2 border-espuma-navy bg-white/70 p-4">
                                <p class="mt-1 font-extrabold">{{ $titulo }}</p>
                                <p class="text-sm text-espuma-navy/80">{{ $texto }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="flex items-center justify-between rounded-2xl border-2 border-espuma-navy bg-white px-6 py-4">
                    <h2 class="text-xl font-extrabold">{{ $editando ? 'Editar pedido' : 'Nuevo pedido' }}</h2>
                    <button type="button" wire:click="cancelar" class="text-sm font-bold underline">Cancelar</button>
                </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-[1fr_380px]">
                <x-catalogo />

                <aside class="h-fit rounded-2xl border border-espuma-navy bg-white">
                    <div class="border-b border-espuma-navy/15 p-6">
                        <h3 class="text-lg font-extrabold">Armar pedido</h3>
                        <p class="text-sm text-espuma-muted">Elige servicios y cantidades.</p>
                    </div>

                    <form wire:submit="guardarPedido" class="space-y-5 p-6">
                        <div>
                            <label class="text-sm font-bold">Servicio</label>
                            <div class="mt-1 flex gap-2">
                                <select wire:model="servicioSel"
                                        class="block w-full rounded-lg border-2 border-espuma-navy text-sm">
                                    <option value="">Elige un servicio</option>
                                    @foreach ($catalogo as $categoria)
                                        <optgroup label="{{ $categoria['titulo'] }}">
                                            @foreach ($categoria['items'] as $item)
                                                <option value="{{ $item['nombre'] }}">
                                                    {{ $item['nombre'] }} — ${{ number_format($item['precio']) }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                <input type="number" min="1" max="50" wire:model="cantidadSel"
                                       class="w-20 rounded-lg border-2 border-espuma-navy text-center text-sm">
                            </div>
                            @error('servicioSel') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            <button type="button" wire:click="agregarAlCarrito"
                                    class="mt-2 w-full rounded-lg border-2 border-espuma-navy py-2 text-sm font-bold hover:bg-espuma-navy hover:text-white">
                                + Agregar al pedido
                            </button>
                            @error('carrito') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <div class="rounded-xl border border-espuma-navy/15">
                            @forelse ($this->lineas as $linea)
                                <div class="flex items-center justify-between gap-2 border-b border-espuma-navy/10 px-3 py-2.5 last:border-0"
                                     wire:key="linea-{{ $linea['key'] }}">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-bold">{{ $linea['nombre'] }}</p>
                                        <p class="text-xs text-espuma-muted">${{ number_format($linea['precio_unitario']) }} c/u</p>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <button type="button" wire:click="restar('{{ $linea['key'] }}')"
                                                class="h-6 w-6 rounded border border-espuma-navy font-bold">−</button>
                                        <span class="w-5 text-center text-sm font-bold">{{ $linea['cantidad'] }}</span>
                                        <button type="button" wire:click="sumar('{{ $linea['key'] }}')"
                                                class="h-6 w-6 rounded border border-espuma-navy font-bold">+</button>
                                        <span class="w-16 text-right text-sm font-bold">${{ number_format($linea['subtotal']) }}</span>
                                        <button type="button" wire:click="quitar('{{ $linea['key'] }}')"
                                                class="ml-1 text-xs text-espuma-muted hover:text-red-600" title="Quitar">✕</button>
                                    </div>
                                </div>
                            @empty
                                <p class="px-3 py-4 text-center text-xs text-espuma-muted">Aún no agregaste servicios.</p>
                            @endforelse

                            <div class="flex items-center justify-between border-t-2 border-espuma-navy px-3 py-3">
                                <span class="font-black">Total</span>
                                <span class="text-lg font-black">${{ number_format($this->total) }}</span>
                            </div>
                        </div>

                        <div>
                            <p class="mb-1 text-sm font-bold">Día de recogida</p>
                            <div class="grid grid-cols-5 gap-2 text-center text-xs">
                                @foreach ($dias as $d)
                                    <button type="button" wire:key="dia-{{ $d->format('Y-m-d') }}"
                                            wire:click="$set('dia', '{{ $d->format('Y-m-d') }}')"
                                            class="rounded-lg border py-2 transition
                                                {{ $dia === $d->format('Y-m-d') ? 'border-espuma-navy bg-espuma-yellow font-bold' : 'border-espuma-navy/20' }}">
                                        <span class="block">{{ strtoupper($d->isoFormat('dd')) }}</span>
                                        <span class="block font-black">{{ $d->format('d') }}</span>
                                    </button>
                                @endforeach
                            </div>
                            @error('dia') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <p class="mb-1 text-sm font-bold">Franja horaria</p>
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                @foreach ($horarios as $h)
                                    <button type="button" wire:key="h-{{ $h }}"
                                            wire:click="$set('horario', '{{ $h }}')"
                                            class="rounded-lg border py-2 transition
                                                {{ $horario === $h ? 'border-espuma-navy bg-espuma-yellow font-bold' : 'border-espuma-navy/20' }}">
                                        {{ $h }}
                                    </button>
                                @endforeach
                            </div>
                            @error('horario') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="text-sm font-bold">Dirección</label>
                            @if ($direcciones->isEmpty())
                                <a href="{{ route('direcciones.create') }}"
                                   class="mt-1 block rounded-lg border-2 border-dashed border-espuma-navy px-4 py-3 text-sm font-bold">
                                    + Agrega tu dirección de recogida
                                </a>
                            @else
                                <select wire:model="direccionId"
                                        class="mt-1 block w-full rounded-lg border-2 border-espuma-navy text-sm">
                                    <option value="">Elige una dirección</option>
                                    @foreach ($direcciones as $dir)
                                        <option value="{{ $dir->id }}">{{ $dir->etiqueta }} · {{ $dir->linea }}</option>
                                    @endforeach
                                </select>
                            @endif
                            @error('direccionId') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <label class="flex items-center gap-2 text-sm font-bold">
                            <input type="checkbox" wire:model="express" class="rounded border-2 border-espuma-navy text-espuma-navy">
                            Servicio exprés (+50%)
                        </label>

                        <button type="submit"
                                class="w-full rounded-lg bg-espuma-navy py-3.5 font-bold text-espuma-yellow shadow-[3px_3px_0_#F5D25F]">
                            {{ $editando ? 'Guardar cambios' : 'Confirmar pedido' }}
                        </button>

                        <p class="text-center text-xs text-espuma-muted">
                            ¿Prefieres WhatsApp?
                            <a href="https://wa.me/{{ config('services.whatsapp.number') }}" class="font-bold underline">Escríbenos</a>,
                            pero desde la web gestionas todo tú solo.
                        </p>
                    </form>
                </aside>
            </div>
        </div>
    @else
        <div class="rounded-2xl border border-espuma-navy bg-white">
            <div class="bg-espuma-yellow px-8 py-7">
                <p class="text-sm font-bold">Pedido #{{ $pedido->codigo }} · {{ $pedido->servicio }}</p>
                <h2 class="mt-2 text-3xl font-black tracking-tight md:text-4xl">{{ $pedido->estado_texto }}</h2>
                <p class="mt-3 max-w-2xl text-lg font-bold">{{ $pedido->paso_mensaje }}</p>
                <p class="mt-3">Entrega estimada: <strong>{{ optional($pedido->entrega_estimada)->translatedFormat('j M Y') ?? 'Por confirmar' }}</strong></p>

                <div class="mt-5 flex flex-wrap gap-3">
                    <button type="button" wire:click="iniciarNuevoPedido"
                            class="rounded-lg bg-espuma-navy px-4 py-2 text-sm font-bold text-espuma-yellow">
                        + Hacer otro pedido
                    </button>
                    @if ($pedido->estado !== 'entregado')
                        <button type="button" wire:click="editarPedido"
                                class="rounded-lg border-2 border-espuma-navy bg-white px-4 py-2 text-sm font-bold">
                            Editar este pedido
                        </button>
                    @endif
                </div>
            </div>

            <div class="border-b border-espuma-navy/15 px-8 pt-6 pb-2">
                <h3 class="text-sm font-extrabold uppercase tracking-wide text-espuma-muted">Estado de tu pedido</h3>
            </div>
            <ol class="grid gap-3 px-8 py-6 sm:grid-cols-2 xl:grid-cols-4">
                @foreach (\App\Models\Pedido::PASO_TEXTO as $paso => $texto)
                    @php
                        $actualPaso = $pedido->paso_actual;
                        $completado = $paso < $actualPaso;
                        $esActual = $paso === $actualPaso;
                    @endphp
                    <li wire:key="estado-{{ $paso }}"
                        class="rounded-xl border-2 p-4 transition
                            {{ $esActual ? 'border-espuma-navy bg-espuma-yellow shadow-[3px_3px_0_#1B2540]' : ($completado ? 'border-espuma-navy/30 bg-white' : 'border-espuma-navy/10 bg-white/60') }}">
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-bold
                                {{ $completado ? 'bg-espuma-navy text-espuma-yellow' : ($esActual ? 'bg-white text-espuma-navy ring-2 ring-espuma-navy' : 'bg-espuma-navy/10 text-espuma-muted') }}">
                                {{ $completado ? '✓' : $paso }}
                            </span>
                            <p class="text-sm font-bold {{ $paso <= $actualPaso ? 'text-espuma-navy' : 'text-espuma-muted' }}">{{ $texto }}</p>
                        </div>
                        @if ($esActual)
                            <p class="mt-2 text-xs font-medium text-espuma-navy/80">{{ \App\Models\Pedido::PASO_MENSAJE[$paso] }}</p>
                        @endif
                    </li>
                @endforeach
            </ol>

            <div class="grid gap-8 border-t border-espuma-navy/15 p-8 md:grid-cols-2">
                <div>
                    <h3 class="font-bold">Detalle</h3>
                    <ul class="divide-y divide-espuma-navy/15">
                        @foreach ($pedido->lineas as $linea)
                            <li class="flex items-center justify-between py-4">
                                <div>
                                    <p class="font-bold">{{ $linea->nombre }}</p>
                                    <p class="text-sm text-espuma-muted">{{ $linea->cantidad }} × ${{ number_format($linea->precio_unitario, 2) }}</p>
                                </div>
                                <span class="font-bold">${{ number_format($linea->subtotal, 2) }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-4 flex items-center justify-between border-t border-espuma-navy/15 pt-4">
                        <span class="font-bold">Total</span>
                        <span class="text-xl font-black">${{ number_format($pedido->total, 2) }}</span>
                    </div>
                </div>

                <div class="space-y-4 text-sm">
                    <div class="flex gap-3">
                        <span>📍</span>
                        <div>
                            <p class="text-xs text-espuma-muted">Entregamos en</p>
                            <p class="font-bold">{{ $pedido->direccion->linea ?? 'Dirección por confirmar' }}</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <span>🕒</span>
                        <div>
                            <p class="text-xs text-espuma-muted">Recogido</p>
                            <p class="font-bold">{{ $pedido->recogido_en?->translatedFormat('j M Y, H:i') ?? 'Aún no recogido' }}</p>
                        </div>
                    </div>

                    @if ($pedido->pendiente_pago && $pedido->paso_actual >= 2)
                        <button wire:click="$dispatch('abrir-pago')"
                                class="w-full rounded-lg bg-espuma-navy py-3.5 font-bold text-espuma-yellow shadow-[3px_3px_0_#F5D25F]">
                            Pagar ${{ number_format($pedido->total, 2) }}
                        </button>
                    @endif

                    <a href="https://wa.me/{{ config('services.whatsapp.number') }}"
                       class="block w-full rounded-lg bg-espuma-green py-3.5 text-center font-bold">
                        Escríbenos por WhatsApp
                    </a>
                </div>
            </div>
        </div>

        <aside class="rounded-2xl border border-espuma-navy bg-white h-fit">
            <div class="border-b border-espuma-navy/15 p-6">
                <h3 class="text-lg font-extrabold">Agendar recogida</h3>
                <p class="text-sm text-espuma-muted">Elige día y franja horaria.</p>
            </div>

            @if ($agendada)
                <div class="p-6">
                    <div class="rounded-lg bg-espuma-yellow/30 px-4 py-3 text-sm">
                        @if ($dia)
                            Recogida agendada para el {{ $dia }} · {{ $horario }}.
                        @else
                            Recogida agendada. Te avisaremos por WhatsApp.
                        @endif
                    </div>
                    <button wire:click="$set('agendada', false)" class="mt-4 text-sm font-bold underline">
                        Cambiar fecha
                    </button>
                </div>
            @else
                <form wire:submit="agendar" class="space-y-5 p-6">
                    <div class="grid grid-cols-5 gap-2 text-center text-xs">
                        @foreach ($dias as $d)
                            <button type="button" wire:key="dia-{{ $d->format('Y-m-d') }}"
                                    wire:click="$set('dia', '{{ $d->format('Y-m-d') }}')"
                                    class="rounded-lg border py-2 transition
                                        {{ $dia === $d->format('Y-m-d') ? 'border-espuma-navy bg-espuma-yellow font-bold' : 'border-espuma-navy/20' }}">
                                <span class="block">{{ strtoupper($d->isoFormat('dd')) }}</span>
                                <span class="block text-sm font-black">{{ $d->format('d') }}</span>
                            </button>
                        @endforeach
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs">
                        @foreach ($horarios as $h)
                            <button type="button" wire:key="h-{{ $h }}"
                                    wire:click="$set('horario', '{{ $h }}')"
                                    class="rounded-lg border py-2 transition
                                        {{ $horario === $h ? 'border-espuma-navy bg-espuma-yellow font-bold' : 'border-espuma-navy/20' }}">
                                {{ $h }}
                            </button>
                        @endforeach
                    </div>

                    <select wire:model="direccionId"
                            class="block w-full rounded-lg border-2 border-espuma-navy text-sm">
                        <option value="">Elige una dirección</option>
                        @foreach ($direcciones as $dir)
                            <option value="{{ $dir->id }}">{{ $dir->etiqueta }} · {{ $dir->linea }}</option>
                        @endforeach
                    </select>
                    @error('direccionId') <span class="text-xs text-red-600">{{ $message }}</span> @enderror

                    <label class="flex items-center gap-2 text-sm font-bold">
                        <input type="checkbox" wire:model="express" class="rounded border-2 border-espuma-navy text-espuma-navy">
                        Servicio exprés (+50%)
                    </label>

                    <button type="submit"
                            class="w-full rounded-lg bg-espuma-navy py-3.5 font-bold text-espuma-yellow shadow-[3px_3px_0_#F5D25F]">
                        Agendar recogida
                    </button>
                </form>
            @endif
        </aside>
    @endif

    <section class="xl:col-span-2">
        <div class="flex items-end justify-between">
            <h3 class="text-lg font-extrabold">Pedidos anteriores</h3>
            <a href="{{ route('pedidos.index') }}" class="text-sm font-bold underline">Ver todos</a>
        </div>
        <ul class="mt-4 divide-y divide-espuma-navy/15 border-y border-espuma-navy">
            @forelse ($anteriores as $ant)
                <li class="flex items-center gap-6 py-4 text-sm">
                    <span class="w-24 font-bold">#{{ $ant->codigo }}</span>
                    <span class="flex-1 text-espuma-muted">{{ $ant->servicio }} · {{ $ant->fecha->translatedFormat('j M Y') }}</span>
                    <span class="font-bold">${{ number_format($ant->total, 2) }}</span>
                    <form method="POST" action="{{ route('pedidos.repetir', $ant) }}">
                        @csrf
                        <button class="rounded-lg border-2 border-espuma-navy px-3 py-1.5 text-xs font-bold hover:bg-espuma-navy hover:text-white">
                            ↻ Repetir
                        </button>
                    </form>
                </li>
            @empty
                <li class="py-4 text-sm text-espuma-muted">Aún no tienes pedidos anteriores.</li>
            @endforelse
        </ul>
    </section>
</div>
