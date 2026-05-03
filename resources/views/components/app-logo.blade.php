@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand name="Gita Car Rental" {{ $attributes }}>
        <x-slot name="logo" class="flex items-center justify-center">
            <img src="{{ asset('img/logogitacar.png') }}" class="size-8 object-contain" alt="Gita Car Rental Logo" />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="Gita Car Rental" {{ $attributes }}>
        <x-slot name="logo" class="flex items-center justify-center">
            <img src="{{ asset('img/logogitacar.png') }}" class="size-8 object-contain" alt="Gita Car Rental Logo" />
        </x-slot>
    </flux:brand>
@endif
