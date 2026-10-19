<div class="max-w-lg">
    <form wire:submit="guardar" class="space-y-5 rounded-2xl border border-espuma-navy bg-white p-6">
        <div>
            <label class="font-bold text-sm">Marca</label>
            <select wire:model="marca" class="mt-2 block w-full rounded-lg border-2 border-espuma-navy">
                <option value="Visa">Visa</option>
                <option value="Mastercard">Mastercard</option>
            </select>
            @error('marca') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="font-bold text-sm">Número de tarjeta</label>
            <input wire:model="numero" maxlength="16" inputmode="numeric" placeholder="4242 4242 4242 4242"
                   class="mt-2 block w-full rounded-lg border-2 border-espuma-navy px-4 py-3">
            @error('numero') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="font-bold text-sm">Vencimiento</label>
                <input wire:model="vence" placeholder="MM/AA" maxlength="5"
                       class="mt-2 block w-full rounded-lg border-2 border-espuma-navy px-4 py-3">
                @error('vence') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="font-bold text-sm">CVC</label>
                <input wire:model="cvc" maxlength="3" inputmode="numeric" placeholder="123"
                       class="mt-2 block w-full rounded-lg border-2 border-espuma-navy px-4 py-3">
                @error('cvc') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
            </div>
        </div>

        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" wire:model="predeterminada" class="rounded border-2 border-espuma-navy text-espuma-navy">
            Usar como tarjeta predeterminada
        </label>

        <div class="flex gap-3">
            <button type="submit"
                    class="rounded-lg bg-espuma-navy px-6 py-3 font-bold text-espuma-yellow shadow-[3px_3px_0_#F5D25F]">
                Guardar tarjeta
            </button>
            <a href="{{ route('pagos.index') }}" class="rounded-lg border-2 border-espuma-navy px-6 py-3 font-bold">
                Cancelar
            </a>
        </div>
    </form>
</div>
