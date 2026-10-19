@props(['titulo' => 'Servicios y precios'])

<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-2xl border-2 border-espuma-navy bg-white']) }}>
    <h3 class="border-b-2 border-espuma-navy px-6 py-5 text-xl font-extrabold">{{ $titulo }}</h3>
    <div class="divide-y divide-espuma-navy/10">
        @foreach (config('servicios') as $categoria)
            <div class="px-6 py-4">
                <p class="text-xs font-bold uppercase tracking-wide text-espuma-muted">{{ $categoria['titulo'] }}</p>
                <ul class="mt-1">
                    @foreach ($categoria['items'] as $item)
                        <li class="flex items-center justify-between gap-4 border-b border-espuma-navy/10 py-3 last:border-0">
                            <div>
                                <p class="font-bold">{{ $item['nombre'] }}</p>
                                @if (!empty($item['detalle']))
                                    <p class="text-sm text-espuma-muted">{{ $item['detalle'] }}</p>
                                @endif
                            </div>
                            <span class="whitespace-nowrap text-lg font-extrabold">${{ number_format($item['precio']) }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>
</div>
