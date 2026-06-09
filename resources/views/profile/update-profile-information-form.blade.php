<x-form-section submit="updateProfileInformation">
    <x-slot name="title">
        {{ __('Profilinformationen') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Aktualisiere deinen Namen, deine E-Mail und deinen Temperatur-Offset.') }}
    </x-slot>

    <x-slot name="form">
        <div class="col-span-6 sm:col-span-4">
            <x-label for="name" value="{{ __('Name') }}" />
            <x-input id="name" type="text" class="mt-1 block w-full" wire:model="state.name" required />
            <x-input-error for="name" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="email" value="{{ __('Email') }}" />
            <x-input id="email" type="email" class="mt-1 block w-full" wire:model="state.email" required />
            <x-input-error for="email" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="temperature_offset" value="{{ __('Temperatur Offset (°C)') }}" />
            <x-input id="temperature_offset" type="number" class="mt-1 block w-full" wire:model="state.temperature_offset" />
            <x-input-error for="temperature_offset" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{ __('Gespeichert.') }}
        </x-action-message>

        <x-button>
            {{ __('Speichern') }}
        </x-button>
    </x-slot>
</x-form-section>