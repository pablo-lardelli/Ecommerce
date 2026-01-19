<?php

namespace App\Livewire;

use App\Livewire\Forms\Shipping\CreateAddressForm;
use App\Livewire\Forms\Shipping\EditAddressForm;
use App\Models\Address;
use App\Models\User;
use Livewire\Component;

class ShippingAddresses extends Component
{
    public $addresses;
    public $newAddress = false;
    public CreateAddressForm $createAddress;
    public EditAddressForm $editAddress;

    public function mount()
    {
        $this->addresses = Address::where('user_id', auth()->id())->get();

        $this->createAddress->receiver_info = [
            'name' => auth()->user()->name,
            'last_name' => auth()->user()->last_name,
            'document_type' => auth()->user()->document_type,
            'document_number' => auth()->user()->document_number,
            'phone' => auth()->user()->phone,
        ];
    }

    public function store()
    {
        $this->createAddress->save();
        $this->addresses = Address::where('user_id', auth()->id())->get();
        $this->newAddress = false;
    }

    public function edit($id)
    {
        $address = Address::find($id);
        $this->editAddress->edit($address);
    }

    public function update()
    {
        $this->editAddress->update();
        $this->addresses = Address::where('user_id', auth()->id())->get();
    }

    public function setDefaultAddress($id)
    {
        $this->addresses->each(function ($address) use ($id) {
            $address->update([
                'default' => $address->id == $id
            ]);
        });
    }

    public function deleteAddress($id)
    {
        Address::find($id)->delete();
        $this->addresses = Address::where('user_id', auth()->id())->get();

        if ($this->addresses->where('default', true)->count() == 0 && $this->addresses->count() > 0) {
            $this->addresses->first()->update(['default' => true]);
        }
    }

    public function render()
    {
        return view('livewire.shipping-addresses');
    }
}
