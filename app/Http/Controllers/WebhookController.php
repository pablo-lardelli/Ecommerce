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
        Log::info('Webhook Mercado Pago', $request->all());

        // 🚫 1️⃣ Ignorar merchant_order inmediatamente
        if ($request->input('topic') === 'merchant_order') {
            return response()->json(['status' => 'merchant_order ignored'], 200);
        }

        // 2️⃣ Obtener payment_id correctamente
        $paymentId =
            $request->input('data.id') ??
            ($request->input('topic') === 'payment' ? $request->input('id') : null);

        if (!$paymentId) {
            return response()->json(['status' => 'ignored'], 200);
        }

        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));

        try {
            $client = new PaymentClient();
            $payment = $client->get($paymentId);
        } catch (\Exception $e) {
            Log::error('Error consultando payment', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage()
            ]);
            return response()->json(['status' => 'payment fetch failed'], 200);
        }

        // 3️⃣ Buscar la orden
        $order = Order::find($payment->external_reference);

        if (!$order) {
            Log::warning('Orden no encontrada', [
                'external_reference' => $payment->external_reference
            ]);
            return response()->json(['status' => 'order not found'], 200);
        }

        // 4️⃣ Idempotencia
        if ($order->status === 2) {
            return response()->json(['status' => 'already processed'], 200);
        }

        // 5️⃣ Actualizar estado
        if ($payment->status === 'approved') {
            $order->update([
                'status' => 2, // pagado
                'payment_id' => (string) $payment->id,
            ]);

            Log::info('Orden pagada', [
                'order_id' => $order->id,
                'payment_id' => $payment->id
            ]);
        }

        if (in_array($payment->status, ['rejected', 'cancelled'])) {
            $order->update([
                'status' => 3,
                'payment_id' => (string) $payment->id,
            ]);
        }

        return response()->json(['status' => 'ok'], 200);
    }
}
