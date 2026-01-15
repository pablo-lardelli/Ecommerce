<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Variant;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;

class WebhookController extends Controller
{
    public function mercadopago(Request $request)
    {
        Log::info('Webhook Mercado Pago recibido', $request->all());

        /*
        |--------------------------------------------------------------------------
        | 1️⃣ Tipo de notificación (Checkout Pro moderno usa "type")
        |--------------------------------------------------------------------------
        */
        $type = $request->input('type') ?? $request->input('topic');

        if ($type === 'merchant_order') {
            return response()->json(['status' => 'merchant_order ignored'], 200);
        }

        /*
        |--------------------------------------------------------------------------
        | 2️⃣ Obtener payment_id
        |--------------------------------------------------------------------------
        */
        $paymentId = $request->input('data.id') ?? $request->input('id');

        if (!$paymentId) {
            Log::warning('Webhook sin payment_id');
            return response()->json(['status' => 'ignored'], 200);
        }

        /*
        |--------------------------------------------------------------------------
        | 3️⃣ Configurar Mercado Pago
        |--------------------------------------------------------------------------
        */
        MercadoPagoConfig::setAccessToken(
            config('services.mercadopago.access_token')
        );

        try {
            $client  = new PaymentClient();
            $payment = $client->get($paymentId);
        } catch (\Throwable $e) {
            Log::error('Error consultando payment en MP', [
                'payment_id' => $paymentId,
                'error'      => $e->getMessage(),
            ]);

            return response()->json(['status' => 'payment fetch failed'], 200);
        }

        /*
        |--------------------------------------------------------------------------
        | 4️⃣ Buscar orden por external_reference
        |--------------------------------------------------------------------------
        */
        $order = Order::find($payment->external_reference);

        if (!$order) {
            Log::warning('Orden no encontrada', [
                'external_reference' => $payment->external_reference,
            ]);

            return response()->json(['status' => 'order not found'], 200);
        }

        /*
        |--------------------------------------------------------------------------
        | 5️⃣ Pago aprobado → marcar como pagada (idempotente)
        |--------------------------------------------------------------------------
        */
        if (
            $payment->status === 'approved' &&
            $payment->status_detail === 'accredited'
        ) {
            DB::transaction(function () use ($order, $payment) {

                $updated = Order::where('id', $order->id)
                    ->where('status', '!=', 2)
                    ->update([
                        'status'     => 2,
                        'payment_id' => (string) $payment->id,
                    ]);

                if ($updated === 0) {
                    return;
                }

                foreach ($order->content as $item) {
                    Variant::where('sku', $item['options']['sku'])
                        ->decrement('stock', $item['qty']);
                }
            });

            Log::info('Orden pagada y stock actualizado', [
                'order_id'   => $order->id,
                'payment_id' => $payment->id,
            ]);

            /* $user = User::find($order->user_id);
            Cart::instance('shopping')->restore($user->id);
            Cart::destroy();
            Cart::store($user->id); */
        }

        /*
        |--------------------------------------------------------------------------
        | 6️⃣ Pago rechazado o cancelado
        |--------------------------------------------------------------------------
        */
        if (in_array($payment->status, ['rejected', 'cancelled'], true)) {
            if ($order->status !== 2) {
                $order->update([
                    'status'     => 3, // cancelado
                    'payment_id' => (string) $payment->id,
                ]);
            }

            Log::info('Pago rechazado / cancelado', [
                'order_id'   => $order->id,
                'payment_id' => $payment->id,
                'status'     => $payment->status,
            ]);
        }

        return response()->json(['status' => 'ok'], 200);
    }
}
