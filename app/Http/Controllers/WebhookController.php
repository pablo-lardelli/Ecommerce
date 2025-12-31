<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Order;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;

use function Pest\Laravel\session;

class WebhookController extends Controller
{
    public function mercadopago(Request $request)
    {
        //Log::info($request->all());
        if ($request->post('type') === 'payment') {
            
            MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
            $client = new PaymentClient();

            $payment = $client->get($request->get('data_id'));

            if ($payment->status === 'approved') {
                //Realizamos una acción en nuestra web
                $address = Address::where('user_id', auth()->id())
                    ->where('default', true)
                    ->first();
                
                Order::create([
                    'user_id' => auth()->id(),
                    'content' => Cart::instance('shopping')->content(),
                    'address' => $address,
                    'payment_id' => $payment,
                    'total' => Cart::subtotal(),
                ]);

                Cart::destroy();

                Log::info('Pago aprobado');

            }
        }
    }
}
