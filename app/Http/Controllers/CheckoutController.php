<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Order;
use Gloudemans\Shoppingcart\Facades\Cart;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;

class CheckoutController extends Controller
{
    public function index()
    {
        $address = Address::where('user_id', auth()->id())
            ->where('default', true)
            ->first();

        $order = Order::create([
            'user_id' => auth()->id(),
            'content' => Cart::instance('shopping')->content(),
            'address' => $address,
            'payment_method' => 1, // Mercado Pago
            'payment_id' => '',
            'total' => (float) Cart::instance('shopping')->subtotal(),
            'status' => 1, // pendiente
        ]);

        $preferenceId = $this->generatePreferenceId($order);

        return view('checkout.index', compact('preferenceId'));
    }

    public function generatePreferenceId(Order $order)
    {
        // Agrega credenciales
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));

        $client = new PreferenceClient();
        $preference = $client->create([
            "items" => [
                [
                    "id" => "1234",
                    "title" => "Mi producto",
                    "quantity" => 1,
                    "unit_price" => (float) $order->total, //(float) Cart::instance('shopping')->subtotal()
                ],
            ],
            "external_reference" => $order->id, //(int)rand(1000000, 9999999),
            //"notification_url" => route('webhooks.mercadopago'),
            "notification_url" => "https://morgan-surly-inquietly.ngrok-free.dev/webhooks/mercadopago"
        ]);

        $preference->back_urls = array(
            "success" => route('gracias'),
            "failure" => route('checkout.index'),
            "pending" => route('checkout.index'),
        );
        $preference->auto_return = "approved";

        return $preference->id;
    }
}
