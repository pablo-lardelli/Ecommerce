<?php

namespace App\Livewire;

use Gloudemans\Shoppingcart\Facades\Cart;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ShoppingCart extends Component
{
    public function mount()
    {
        Cart::instance('shopping');
    }

    #[Computed()]
    public function subtotal()
    {
        return Cart::content()->filter(function ($item){
            return $item->qty <= $item->options['stock'];
        })->sum(function($item){
            return $item->subtotal;
        });
    }

    public function decrease($rowId)
    {
        Cart::instance('shopping');   
        $item = Cart::get($rowId);
        
        if($item->qty > 1){
            Cart::update($rowId, $item->qty - 1);
        }else{
            Cart::remove($rowId);
        }

        if(auth()->check()){
            Cart::store(auth()->id());
        }

        $this->dispatch('cartUpdated', Cart::count());
    }

    public function increase($rowId)
    {
        Cart::instance('shopping');   
        Cart::update($rowId, Cart::get($rowId)->qty + 1);

        if(auth()->check()){
            Cart::store(auth()->id());
        }

        $this->dispatch('cartUpdated', Cart::count());
    }

    public function remove($rowId)
    {
        Cart::instance('shopping');   
        Cart::remove($rowId);

        if(auth()->check()){
            Cart::store(auth()->id());
        }

        $this->dispatch('cartUpdated', Cart::count());
    }

    public function destroy()
    {
        Cart::instance('shopping');   
        Cart::destroy();

        if(auth()->check()){
            Cart::store(auth()->id());
        }

        $this->dispatch('cartUpdated', Cart::count());
    }

    public function render()
    {
        return view('livewire.shopping-cart');
    }
}
