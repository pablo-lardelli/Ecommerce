<?php

namespace App\Livewire\Admin\Orders;

use App\Enums\OrderStatus;
use App\Enums\ShipmentStatus;
use App\Models\Driver;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Support\Facades\Storage;

class OrderTable extends DataTableComponent
{
    protected $model = Order::class;

    public $drivers;

    public $new_shipment = [
        'openModal' => false,
        'order_id' => '',
        'driver_id' => '',
    ];

    public function mount()
    {
        $this->drivers = Driver::all();
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("N° orden", "id")
                ->sortable(),
            Column::make("Ticket")
                ->label(function($row){
                    return view('admin.orders.ticket', ['order' => $row]);
                }),
            Column::make("Fecha orden", "created_at")
                ->format(function($value){
                    return $value->format('d/m/Y');
                })
                ->sortable(),
            Column::make("total")
                ->format(function($value){
                    return "$ " .  number_format($value, 2);
                })
                ->sortable(),
            Column::make("Cantidad", "content")
                ->format(function($value){ 
                    return count($value);
                })
                ->sortable(),
            Column::make("Estado", "status")
                ->format(function($value){ 
                    return $value->name;
                })
                ->sortable(),
            Column::make("Acciones")
                ->label(function($row){
                    return view('admin.orders.actions', ['order' => $row]);
                }),
        ];
    }

    public function dowloadTicket(Order $order)
    {
        return Storage::download($order->pdf_path);
    }

    public function markAsProcessing(Order $order)
    {
        $order->status = OrderStatus::Processing;
        $order->save();
    }

    public function assignDriver(Order $order)
    {
        $this->new_shipment['order_id'] = $order->id;
        $this->new_shipment['openModal'] = true;
    }

    public function saveShipment()
    {
        $this->validate([
            'new_shipment.driver_id' => 'required|exists:drivers,id'
        ]);

        $order = Order::find($this->new_shipment['order_id']);

        $order->status = OrderStatus::Shipped;
        $order->save();

        $order->shipments()->create([
            'driver_id' => $this->new_shipment['driver_id']
        ]);

        $this->reset('new_shipment');
    }

    public function markAsRefunded(Order $order)
    {
        $order->status = OrderStatus::Refunded;
        $order->save();

        $shipment = $order->shipments->last();
        $shipment->refunded_at = now();
        $shipment->save();
    }

    public function cancelOrder(Order $order)
    {
        if($order->status == OrderStatus::Shipped){
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'No se puede cancelar la orden',
                'text' => 'La orden tiene envios pendientes',
            ]);

            return;
        }

        if($order->status == OrderStatus::Failed){
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'No se puede cancelar la orden',
                'text' => 'El pedido no ha sido retornado',
            ]);

            return;
        }

        $order->status = OrderStatus::Cancelled;
        $order->save();
    }

    public function customView(): string
    {
        return 'admin.orders.modal';
    }
}
