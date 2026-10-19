<x-cliente-layout heading="Perfil">
    @if (session('status'))
        <div class="mb-6 rounded-lg bg-espuma-green/20 px-4 py-3 text-sm font-bold">{{ session('status') }}</div>
    @endif

    <div class="grid gap-6 md:grid-cols-2">
        <div class="rounded-2xl border border-espuma-navy bg-white p-6">
            <h2 class="text-lg font-extrabold">Datos personales</h2>
            <p class="text-sm text-espuma-muted">Actualiza tu información de contacto.</p>

            <form method="POST" action="{{ route('perfil.update') }}" class="mt-6 space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="font-bold text-sm">Nombre completo</label>
                    <input name="name" value="{{ old('name', auth()->user()->name) }}" required
                           class="mt-2 block w-full rounded-lg border-2 border-espuma-navy px-4 py-3">
                    @error('name') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="font-bold text-sm">Correo electrónico</label>
                    <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                           class="mt-2 block w-full rounded-lg border-2 border-espuma-navy px-4 py-3">
                    @error('email') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>

                <hr class="border-espuma-navy/15">

                <h3 class="text-sm font-extrabold">Cambiar contraseña (opcional)</h3>

                <div>
                    <label class="font-bold text-sm">Contraseña actual</label>
                    <input type="password" name="current_password" autocomplete="current-password"
                           class="mt-2 block w-full rounded-lg border-2 border-espuma-navy px-4 py-3">
                    @error('current_password') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="font-bold text-sm">Nueva contraseña</label>
                    <input type="password" name="password" autocomplete="new-password"
                           class="mt-2 block w-full rounded-lg border-2 border-espuma-navy px-4 py-3">
                    @error('password') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="font-bold text-sm">Confirmar nueva contraseña</label>
                    <input type="password" name="password_confirmation" autocomplete="new-password"
                           class="mt-2 block w-full rounded-lg border-2 border-espuma-navy px-4 py-3">
                    @error('password_confirmation') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>

                <button class="rounded-lg bg-espuma-navy px-6 py-3 font-bold text-espuma-yellow shadow-[3px_3px_0_#F5D25F]">
                    Guardar cambios
                </button>
            </form>
        </div>

        <div class="rounded-2xl border border-espuma-navy bg-white p-6">
            <h2 class="text-lg font-extrabold">Notificaciones</h2>
            <p class="text-sm text-espuma-muted">Elige cómo quieres que te avisemos.</p>

            <form method="POST" action="{{ route('perfil.notificaciones') }}" class="mt-6 space-y-4">
                @csrf
                @method('PUT')

                @php
                    $opciones = [
                        'notif_whatsapp_lista' => 'Recibir un WhatsApp cuando mi ropa esté lista',
                        'notif_en_camino' => 'Avisarme cuando el repartidor vaya en camino',
                        'notif_promos' => 'Recibir promociones y descuentos',
                    ];
                @endphp

                @foreach ($opciones as $campo => $texto)
                    <label class="flex items-center gap-3 text-sm">
                        <input type="checkbox" name="{{ $campo }}" value="1" @checked(auth()->user()->$campo)
                               class="rounded border-2 border-espuma-navy text-espuma-navy">
                        {{ $texto }}
                    </label>
                @endforeach

                <button class="rounded-lg bg-espuma-navy px-6 py-3 font-bold text-espuma-yellow shadow-[3px_3px_0_#F5D25F]">
                    Guardar preferencias
                </button>
            </form>
        </div>
    </div>
</x-cliente-layout>
