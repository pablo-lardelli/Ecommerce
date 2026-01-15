<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Order;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;
use Illuminate\Support\Facades\Log;
use MercadoPago\Client\Payment\PaymentClient;

class CheckoutController extends Controller
{
    public function index()
    {
        Cart::instance('shopping');

        $content = Cart::content()->filter(function ($item) {
            return $item->qty <= $item->options->stock;
        });

        $subtotal = $content->sum(function ($item) {
            return $item->subtotal;
        });

        $delivery = number_format(100, 2);

        $total = $subtotal + $delivery;

        $address = Address::where('user_id', auth()->id())
            ->where('default', true)
            ->first();

        if(!$address){
            $address = "Retiro con orden en mano";
        }

        $order = Order::create([
            'user_id' => auth()->id(),
            'content' => $content,
            'address' => $address,
            'payment_method' => 1, // Mercado Pago
            'payment_id' => '',
            'total' => $total,
            'status' => 1, // pendiente
        ]);

        $preferenceId = $this->generatePreferenceId($order);

        Cart::destroy();

        return view('checkout.index', compact('preferenceId', 'content', 'subtotal', 'delivery', 'total'));
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
            "external_reference" => $order->id,
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
