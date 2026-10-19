<x-layouts.admin heading="Pedidos">
    @if (session('status'))
        <div class="mb-6 rounded-lg border-2 border-espuma-navy bg-espuma-yellow/40 px-4 py-3 text-sm font-bold">
            {{ session('status') }}
        </div>
    @endif

    <div class="mb-6 flex items-center justify-between">
        <p class="text-sm text-espuma-muted">{{ $total }} pedido(s) en total. Avanza cada tarjeta conforme terminen los pasos.</p>
    </div>

    @php $maxPaso = count(\App\Models\Pedido::PASO_TEXTO); @endphp

    <div id="tablero" data-poll-url="{{ route('admin.pedidos.index') }}"
         class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7">
        @foreach ($columnas as $paso => $columna)
            <section class="flex flex-col rounded-2xl border border-espuma-navy bg-white">
                <header class="flex items-center justify-between border-b border-espuma-navy/15 px-4 py-3">
                    <h2 class="text-sm font-extrabold">{{ $columna['titulo'] }}</h2>
                    <span class="flex h-6 min-w-6 items-center justify-center rounded-full bg-espuma-navy px-1.5 text-xs font-bold text-espuma-yellow">
                        {{ $columna['pedidos']->count() }}
                    </span>
                </header>

                <div class="flex-1 space-y-3 p-3 max-h-[62vh] overflow-y-auto">
                    @forelse ($columna['pedidos'] as $pedido)
                        <article class="rounded-xl border border-espuma-navy/20 p-3 text-sm">
                            <div class="flex items-center justify-between">
                                <span class="font-black">#{{ $pedido->codigo }}</span>
                                @if ($pedido->express)
                                    <span class="rounded bg-espuma-green/20 px-1.5 py-0.5 text-[10px] font-bold">EXPRÉS</span>
                                @endif
                            </div>

                            <p class="mt-1 font-bold">{{ $pedido->user?->name ?? 'Sin cliente' }}</p>
                            <p class="text-xs text-espuma-muted">{{ $pedido->servicio }} · {{ rtrim(rtrim(number_format($pedido->kg, 2, '.', ''), '0'), '.') }} kg</p>
                            <p class="text-xs text-espuma-muted">{{ $pedido->direccion?->linea ?? 'Sin dirección' }}</p>

                            <div class="mt-2 flex items-center justify-between border-t border-espuma-navy/15 pt-2">
                                <span class="text-xs text-espuma-muted">{{ $pedido->fecha?->translatedFormat('j M Y') }}</span>
                                <span class="font-bold">${{ number_format($pedido->total, 2) }}</span>
                            </div>

                            @if ($pedido->pendiente_pago)
                                <p class="mt-2 rounded bg-espuma-yellow/40 px-2 py-1 text-center text-[11px] font-bold">Pago pendiente</p>
                            @endif

                            <div class="mt-3 flex items-center gap-2">
                                <form method="POST" action="{{ route('admin.pedidos.retroceder', $pedido) }}" class="flex-1"
                                      onsubmit="return confirmarAtras(event, this)">
                                    @csrf
                                    <button @disabled($paso === 1)
                                            class="w-full rounded-lg border-2 border-espuma-navy py-1.5 text-xs font-bold transition
                                                   disabled:cursor-not-allowed disabled:opacity-30 hover:enabled:bg-espuma-navy hover:enabled:text-white">
                                        ← Atrás
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.pedidos.avanzar', $pedido) }}" class="flex-1">
                                    @csrf
                                    <button @disabled($paso >= $maxPaso)
                                            class="w-full rounded-lg bg-espuma-navy py-1.5 text-xs font-bold text-espuma-yellow transition
                                                   disabled:cursor-not-allowed disabled:opacity-30 hover:enabled:bg-espuma-navy/80">
                                        Avanzar →
                                    </button>
                                </form>
                            </div>

                            <button type="button" onclick="verDetalle({{ $pedido->id }})"
                                    class="mt-2 w-full rounded-lg border-2 border-espuma-navy py-1.5 text-xs font-bold transition
                                           hover:bg-espuma-navy hover:text-white">
                                Ver detalles
                            </button>

                            @if ($paso === $maxPaso)
                                <form method="POST" action="{{ route('admin.pedidos.archivar', $pedido) }}" class="mt-2">
                                    @csrf
                                    <button class="w-full rounded-lg bg-espuma-green py-1.5 text-xs font-bold text-white transition hover:bg-espuma-green/80">
                                        Mandar a registros
                                    </button>
                                </form>
                            @endif
                        </article>
                    @empty
                        <p class="py-6 text-center text-xs text-espuma-muted">Sin pedidos.</p>
                    @endforelse
                </div>
            </section>
        @endforeach
    </div>

    <div id="banner-atras" class="fixed inset-x-0 bottom-0 z-50 hidden">
        <div class="mx-auto m-4 flex max-w-3xl flex-wrap items-center gap-4 rounded-2xl border-2 border-espuma-navy bg-espuma-yellow p-4 shadow-[4px_4px_0_#1B2540]">
            <div class="flex-1">
                <p class="font-extrabold">¿Seguro que desea volver atrás?</p>
                <p class="text-xs">El pedido regresará a la etapa anterior y la acción quedará registrada a nombre de
                    <span class="font-mono font-bold">{{ auth()->user()->email }}</span>.</p>
            </div>
            <button type="button" onclick="confirmarAtrasSi()"
                    class="rounded-lg bg-espuma-navy px-4 py-2 text-sm font-bold text-espuma-yellow">
                Sí, volver atrás
            </button>
            <button type="button" onclick="cancelarAtras()"
                    class="rounded-lg border-2 border-espuma-navy px-4 py-2 text-sm font-bold">
                Cancelar
            </button>
        </div>
    </div>

    <script>
        let formAtras = null;

        function confirmarAtras(event, form) {
            if (form.dataset.confirmado === '1') {
                return true;
            }
            event.preventDefault();
            formAtras = form;
            document.getElementById('banner-atras').classList.remove('hidden');
            return false;
        }

        function confirmarAtrasSi() {
            if (formAtras) {
                formAtras.dataset.confirmado = '1';
                formAtras.submit();
            }
        }

        function cancelarAtras() {
            formAtras = null;
            document.getElementById('banner-atras').classList.add('hidden');
        }
    </script>

    <div id="modal-detalle" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-espuma-navy/50" onclick="cerrarDetalle()"></div>
        <div class="relative mx-auto my-10 max-h-[85vh] w-full max-w-2xl overflow-y-auto rounded-2xl border-2 border-espuma-navy bg-white p-6 shadow-[6px_6px_0_#1B2540]">
            <div class="flex items-center justify-between border-b border-espuma-navy/15 pb-3">
                <h3 class="text-lg font-extrabold">Detalle del pedido</h3>
                <button onclick="cerrarDetalle()" class="text-espuma-muted hover:text-espuma-navy" title="Cerrar">✕</button>
            </div>
            <div id="detalle-contenido" class="mt-4 text-sm"></div>
        </div>
    </div>

    @php $todos = collect($columnas)->flatMap(fn ($c) => $c['pedidos']); @endphp
    <div id="detalles" class="hidden">
        @foreach ($todos as $pedido)
            <div id="detalle-{{ $pedido->id }}">
                <p class="text-2xl font-black">#{{ $pedido->codigo }}</p>
                <p class="mt-1 font-bold">{{ $pedido->estado_texto }}</p>
                <p class="text-espuma-muted">{{ $pedido->paso_mensaje }}</p>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-xl border border-espuma-navy/15 p-3">
                        <p class="mb-1 text-xs font-extrabold uppercase text-espuma-muted">Cliente</p>
                        <p class="font-bold">{{ $pedido->user?->name ?? 'Sin cliente' }}</p>
                        <p class="text-espuma-muted">{{ $pedido->user?->email }}</p>
                        <p class="text-espuma-muted">WhatsApp: {{ $pedido->user?->whatsapp ?? '—' }}</p>
                    </div>
                    <div class="rounded-xl border border-espuma-navy/15 p-3">
                        <p class="mb-1 text-xs font-extrabold uppercase text-espuma-muted">Dirección</p>
                        <p class="font-bold">{{ $pedido->direccion?->linea ?? 'Sin dirección' }}</p>
                        <p class="text-espuma-muted">{{ $pedido->direccion?->referencia ?? '—' }}</p>
                    </div>
                </div>

                <div class="mt-4 rounded-xl border border-espuma-navy/15 p-3">
                    <p class="mb-2 text-xs font-extrabold uppercase text-espuma-muted">Servicios</p>
                    <ul class="divide-y divide-espuma-navy/10">
                        @forelse ($pedido->lineas as $linea)
                            <li class="flex items-center justify-between py-2">
                                <div>
                                    <p class="font-bold">{{ $linea->nombre }}</p>
                                    <p class="text-xs text-espuma-muted">{{ $linea->cantidad }} × ${{ number_format($linea->precio_unitario, 2) }}</p>
                                </div>
                                <span class="font-bold">${{ number_format($linea->subtotal, 2) }}</span>
                            </li>
                        @empty
                            <li class="py-2 text-espuma-muted">Sin líneas.</li>
                        @endforelse
                    </ul>
                    <div class="mt-2 flex items-center justify-between border-t-2 border-espuma-navy pt-2">
                        <span class="font-black">Total</span>
                        <span class="font-black">${{ number_format($pedido->total, 2) }}</span>
                    </div>
                </div>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-xl border border-espuma-navy/15 p-3">
                        <p class="mb-1 text-xs font-extrabold uppercase text-espuma-muted">Pago</p>
                        @if ($pedido->pago)
                            <p>{{ $pedido->pago->metodo_texto }} · <strong>{{ $pedido->pago->estado_texto }}</strong></p>
                            <p class="text-espuma-muted">Monto: ${{ number_format($pedido->pago->monto, 2) }}</p>
                            @if ($pedido->pago->comprobante)
                                <a href="{{ asset('storage/'.$pedido->pago->comprobante) }}" target="_blank" class="font-bold underline">Ver comprobante</a>
                            @endif
                        @else
                            <p class="text-espuma-muted">Sin pago registrado.</p>
                        @endif
                        @if ($pedido->pendiente_pago)
                            <p class="mt-1 rounded bg-espuma-yellow/40 px-2 py-1 text-center text-[11px] font-bold">Pago pendiente</p>
                        @endif
                    </div>
                    <div class="rounded-xl border border-espuma-navy/15 p-3">
                        <p class="mb-1 text-xs font-extrabold uppercase text-espuma-muted">Fechas</p>
                        <p>Creado: {{ $pedido->fecha?->translatedFormat('j M Y') ?? '—' }}</p>
                        <p>Recogido: {{ $pedido->recogido_en?->translatedFormat('j M Y, H:i') ?? 'No' }}</p>
                        <p>Entrega: {{ $pedido->entrega_estimada?->translatedFormat('j M Y') ?? 'Por confirmar' }}</p>
                        @if ($pedido->express)
                            <p class="font-bold">Exprés</p>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        function verDetalle(id) {
            document.getElementById('detalle-contenido').innerHTML =
                document.getElementById('detalle-' + id).innerHTML;
            document.getElementById('modal-detalle').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function cerrarDetalle() {
            document.getElementById('modal-detalle').classList.add('hidden');
            document.body.style.overflow = '';
        }

        (function () {
            const tablero = document.getElementById('tablero');
            if (!tablero) return;

            const url = tablero.dataset.pollUrl;
            let enCurso = false;

            const ocupado = () =>
                document.hidden ||
                !document.getElementById('modal-detalle').classList.contains('hidden') ||
                !document.getElementById('banner-atras').classList.contains('hidden');

            async function refrescar() {
                if (enCurso || ocupado()) return;
                enCurso = true;

                try {
                    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    if (!res.ok) return;

                    const doc = new DOMParser().parseFromString(await res.text(), 'text/html');
                    const nuevoTablero = doc.getElementById('tablero');
                    const nuevosDetalles = doc.getElementById('detalles');
                    if (!nuevoTablero || !nuevosDetalles) return;

                    const scrolls = [...tablero.querySelectorAll('.overflow-y-auto')].map((el) => el.scrollTop);
                    tablero.innerHTML = nuevoTablero.innerHTML;
                    tablero.querySelectorAll('.overflow-y-auto').forEach((el, i) => {
                        if (scrolls[i] != null) el.scrollTop = scrolls[i];
                    });

                    document.getElementById('detalles').innerHTML = nuevosDetalles.innerHTML;
                } catch (e) {
                    /* sin conexión: se reintenta en el próximo ciclo */
                } finally {
                    enCurso = false;
                }
            }

            setInterval(refrescar, 30000);
        })();
    </script>
</x-layouts.admin>
