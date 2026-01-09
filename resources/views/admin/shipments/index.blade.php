<x-admin-layout :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'route' => route('admin.dashboard'),
    ],
    [
        'name' => 'Envíos',
    ],
]">

    @livewire('admin.shipments.shipment-table')

</x-admin-layout>