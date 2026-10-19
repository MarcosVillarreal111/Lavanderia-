<x-guest-split title="Login">
    <h2 class="text-4xl font-extrabold">Login</h2>
    <p class="mt-2 text-espuma-muted">Entra con el correo de tu cuenta de cliente.</p>

    <x-validation-errors class="mt-6" />

    @session('status')
        <div class="mt-6 font-medium text-sm text-green-600">
            {{ $value }}
        </div>
    @endsession

    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-6">
        @csrf
        <div>
            <x-label for="email" value="Correo electrónico" class="font-bold" />
            <x-input id="email" type="email" name="email" :value="old('email')" required autofocus
                     class="mt-2 block w-full rounded-lg border-2 border-espuma-navy px-4 py-3" />
        </div>

        <div x-data="{ show: false }">
            <div class="flex justify-between">
                <x-label for="password" value="Contraseña" class="font-bold" />
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm font-bold underline">¿La olvidaste?</a>
                @endif
            </div>
            <div class="relative mt-2">
                <x-input id="password" :type="'password'" name="password" required
                         x-bind:type="show ? 'text' : 'password'"
                         class="block w-full rounded-lg border-2 border-espuma-navy px-4 py-3 pr-12" />
                <button type="button" @click="show = !show"
                        class="absolute inset-y-0 right-3 text-espuma-muted" aria-label="Mostrar contraseña">👁</button>
            </div>
        </div>

        <button type="submit"
                class="w-full rounded-lg bg-espuma-navy py-4 font-bold text-espuma-yellow shadow-[4px_4px_0_#F5D25F] hover:opacity-90">
            Entrar
        </button>
    </form>

    <p class="mt-8 text-center text-sm text-espuma-muted">
        ¿Primera vez aquí?
        <a href="{{ route('register') }}" class="font-bold underline">Crea tu cuenta</a>
    </p>
</x-guest-split>
