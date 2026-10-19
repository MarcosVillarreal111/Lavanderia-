@props(['heading' => null])

<x-layouts.cliente :heading="$heading">
    {{ $slot }}
</x-layouts.cliente>
