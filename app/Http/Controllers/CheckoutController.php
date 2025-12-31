<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Order;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;

class CheckoutController extends Controller
{
    public function index()
    {
        $preferenceId = $this->generatePreferenceId();

        return view('checkout.index', compact('preferenceId'));
    }

    public function generatePreferenceId()
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
                    "unit_price" => (float) $order->total,//(float) Cart::instance('shopping')->subtotal()
                ],
            ],
            "external_reference" => $order->id, //(int)rand(1000000, 9999999),
            // "back_urls" => [
            //     "success" => route('gracias'),
            //     "failure" => route('gracias'),
            //     "pending" => route('gracias')
            // ],
            //"notification_url" => route('webhooks.mercadopago'),
            "notification_url" => "https://morgan-surly-inquietly.ngrok-free.dev/webhooks/mercadopago"
            //"notification_url" => "https://morgan-surly-inquietly.ngrok-free.dev/checkout/paid"
        ]);

        $preference->back_urls = array(
            "success" => route('gracias'),
            "failure" => route('checkout.index'),
            "pending" => route('checkout.index'),
        );
        $preference->auto_return = "approved";

        //$preference->external_reference = (int)rand(1000000,9999999);

        return $preference->id;
    }
}
