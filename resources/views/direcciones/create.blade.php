<x-cliente-layout heading="Nueva dirección">
    <form method="POST" action="{{ route('direcciones.store') }}"
          class="max-w-lg space-y-5 rounded-2xl border border-espuma-navy bg-white p-6">
        @csrf

        <div>
            <label class="font-bold text-sm">Etiqueta</label>
            <input name="etiqueta" value="{{ old('etiqueta') }}" placeholder="Casa, Oficina…" required
                   class="mt-2 block w-full rounded-lg border-2 border-espuma-navy px-4 py-3">
            @error('etiqueta') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="font-bold text-sm">Dirección</label>
            <input name="linea" value="{{ old('linea') }}" placeholder="Calle, número, colonia, ciudad" required
                   class="mt-2 block w-full rounded-lg border-2 border-espuma-navy px-4 py-3">
            @error('linea') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="font-bold text-sm">Referencia (opcional)</label>
            <input name="referencia" value="{{ old('referencia') }}" placeholder="Portón azul, timbre 3"
                   class="mt-2 block w-full rounded-lg border-2 border-espuma-navy px-4 py-3">
        </div>

        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="predeterminada" value="1"
                   class="rounded border-2 border-espuma-navy text-espuma-navy">
            Usar como dirección predeterminada
        </label>

        <div class="flex gap-3">
            <button class="rounded-lg bg-espuma-navy px-6 py-3 font-bold text-espuma-yellow shadow-[3px_3px_0_#F5D25F]">
                Guardar
            </button>
            <a href="{{ route('direcciones.index') }}" class="rounded-lg border-2 border-espuma-navy px-6 py-3 font-bold">
                Cancelar
            </a>
        </div>
    </form>
</x-cliente-layout>
