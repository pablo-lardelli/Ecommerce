<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Address;

class ShippingController extends Controller
{
    public function index()
    {
        $address = Address::where('user_id', auth()->id())
            ->where('default', true)
            ->first();

        return view('shipping.index', compact('address'));
    }
}
