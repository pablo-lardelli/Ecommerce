<x-dialog-modal wire:model="new_shipment.openModal">

    <x-slot name="title">
        {{ __('Order Details') }}
    </x-slot>

    <x-slot name="content">
        <x-label>
            Unidad
        </x-label>

        <x-select class="w-full" wire:model="new_shipment.driver_id">
            <option value="" disabled>
                Seleccione una unidad
            </option>

            @foreach ($drivers as $driver)
                <option value="{{ $driver->id }}">
                    {{ $driver->user->name }}
                </option>
            @endforeach
        </x-select>

        <x-input-error for="new_shipment.driver_id" />
    </x-slot>

    <x-slot name="footer">
        <x-danger-button wire:click="$set('new_shipment.openModal', false)">
            Cancelar
        </x-danger-button>

        <x-button class="ml-2" wire:click="saveShipment">
            Asignar
        </x-button>
    </x-slot>

</x-dialog-modal>