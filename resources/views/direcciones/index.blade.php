<x-cliente-layout heading="Direcciones">
    <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
        @forelse ($direcciones as $d)
            <div class="rounded-2xl border border-espuma-navy bg-white p-6">
                <div class="flex items-center gap-2">
                    <span>📍</span>
                    <h3 class="text-lg font-extrabold">{{ $d->etiqueta }}</h3>
                    @if ($d->predeterminada)
                        <span class="rounded bg-espuma-yellow px-2 py-0.5 text-[10px] font-bold uppercase">Predeterminada</span>
                    @endif
                </div>
                <p class="mt-3 text-sm font-bold">{{ $d->linea }}</p>
                @if ($d->referencia)
                    <p class="mt-1 text-sm text-espuma-muted">{{ $d->referencia }}</p>
                @endif

                <div class="mt-6 flex items-center gap-4 text-sm font-bold">
                    <a href="{{ route('direcciones.edit', $d) }}" class="underline">Editar</a>
                    @unless ($d->predeterminada)
                        <form method="POST" action="{{ route('direcciones.destroy', $d) }}"
                              onsubmit="return confirm('¿Eliminar esta dirección?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 underline">Eliminar</button>
                        </form>
                    @endunless
                </div>
            </div>
        @empty
            <p class="text-espuma-muted">Aún no tienes direcciones guardadas.</p>
        @endforelse

        <a href="{{ route('direcciones.create') }}"
           class="flex min-h-40 flex-col items-center justify-center rounded-2xl border-2 border-dashed border-espuma-navy/30 p-6 text-center hover:bg-espuma-navy/5">
            <span class="text-3xl">+</span>
            <span class="mt-2 text-sm font-bold">Agregar dirección</span>
        </a>
    </div>
</x-cliente-layout>
