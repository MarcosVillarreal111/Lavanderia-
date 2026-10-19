<x-guest-split title="Términos y aviso de privacidad">
    <h2 class="text-3xl font-extrabold">Términos y aviso de privacidad</h2>
    <p class="mt-2 text-espuma-muted">Última actualización: {{ now()->translatedFormat('j \d\e F \d\e Y') }}</p>

    <div class="mt-8 space-y-6 text-sm leading-relaxed">
        <div>
            <h3 class="font-extrabold">Servicios</h3>
            <p class="mt-2 text-espuma-muted">
                Espuma ofrece servicios de lavado, secado, planchado y tintorería con recogida y entrega a domicilio.
                Los tiempos de entrega son estimados y pueden variar según la demanda.
            </p>
        </div>
        <div>
            <h3 class="font-extrabold">Pagos y precios</h3>
            <p class="mt-2 text-espuma-muted">
                Los precios publicados pueden cambiar sin previo aviso. El servicio exprés tiene un cargo adicional del 50%.
            </p>
        </div>
        <div>
            <h3 class="font-extrabold">Privacidad</h3>
            <p class="mt-2 text-espuma-muted">
                Usamos tus datos únicamente para gestionar tus pedidos y contactarte. No compartimos tu información con terceros.
            </p>
        </div>
    </div>

    <a href="{{ route('register') }}" class="mt-8 inline-block font-bold underline">Volver al registro</a>
</x-guest-split>
