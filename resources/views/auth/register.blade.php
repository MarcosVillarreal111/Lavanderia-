<x-guest-split title="Register">
    <h2 class="text-4xl font-extrabold">Register</h2>
    <p class="mt-2 text-espuma-muted">Crea tu cuenta de cliente. Es gratis.</p>

    <x-validation-errors class="mt-6" />

    <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-5">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div>
                <x-label for="name" value="Nombre completo" class="font-bold" />
                <x-input id="name" name="name" :value="old('name')" required
                         class="mt-2 block w-full rounded-lg border-2 border-espuma-navy px-4 py-3" />
            </div>
            <div>
                <x-label for="whatsapp" value="WhatsApp" class="font-bold" />
                <x-input id="whatsapp" name="whatsapp" :value="old('whatsapp')" placeholder="+52 55 1234 5678"
                         class="mt-2 block w-full rounded-lg border-2 border-espuma-navy px-4 py-3" />
            </div>
        </div>

        <div>
            <x-label for="email" value="Correo electrónico" class="font-bold" />
            <x-input id="email" type="email" name="email" :value="old('email')" required
                     class="mt-2 block w-full rounded-lg border-2 border-espuma-navy px-4 py-3" />
        </div>

        <div>
            <x-label for="password" value="Contraseña" class="font-bold" />
            <x-input id="password" type="password" name="password" required placeholder="Mínimo 8 caracteres"
                     class="mt-2 block w-full rounded-lg border-2 border-espuma-navy px-4 py-3" />
        </div>

        <div>
            <x-label for="password_confirmation" value="Confirmar contraseña" class="font-bold" />
            <x-input id="password_confirmation" type="password" name="password_confirmation" required
                     class="mt-2 block w-full rounded-lg border-2 border-espuma-navy px-4 py-3" />
        </div>

        <label class="flex items-center gap-3 text-sm">
            <input type="checkbox" name="terms" required class="rounded border-2 border-espuma-navy text-espuma-navy">
            Acepto los <a href="{{ route('terms.show') }}" class="font-bold underline">términos</a>
            y el aviso de privacidad.
        </label>

        <button type="submit"
                class="w-full rounded-lg bg-espuma-navy py-4 font-bold text-espuma-yellow shadow-[4px_4px_0_#F5D25F]">
            Crear cuenta
        </button>
    </form>

    <p class="mt-8 text-center text-sm text-espuma-muted">
        ¿Ya tienes cuenta?
        <a href="{{ route('login') }}" class="font-bold underline">Inicia sesión</a>
    </p>
</x-guest-split>
