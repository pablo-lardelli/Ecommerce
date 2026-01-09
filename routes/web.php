<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\FamilyController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\WelcomeController;
use App\Models\Order;
use App\Models\Product;
use App\Models\Variant;
use Barryvdh\DomPDF\Facade\Pdf;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Route;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;

Route::get('/', [WelcomeController::class, 'index'])->name('welcome.index');

Route::get('/families/{family}', [FamilyController::class, 'show'])->name('families.show');
Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/subcategories/{subcategory}', [SubcategoryController::class, 'show'])->name('subcategories.show');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/shipping', [ShippingController::class, 'index'])
    ->middleware('auth')
    ->name('shipping.index');
Route::get('/checkout', [CheckoutController::class, 'index'])
    ->middleware('auth')
    ->name('checkout.index');
Route::post('webhooks/mercadopago', [WebhookController::class, 'mercadopago'])->name('webhooks.mercadopago');

Route::get('gracias', function () {
    return view('gracias');
})->name('gracias');


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::get('prueba', function(){
    MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
    
    $client = new PaymentClient();
    $payment = json_encode($client->get('138709911569'));

    return json_decode($payment);
});

// Route::get('prueba', function () {
//     Cart::instance('shopping');
//     return Cart::content();
// });

Route::get('prueba', function () {
    $order = Order::first();

    $pdf = Pdf::loadView('orders.ticket', compact('order'))->setPaper('a5');

    $pdf->save(storage_path('app/public/tickets/ticket-' . $order->id . '.pdf'));
    
    $order->pdf_path = 'tickets/ticket-' . $order->id . '.pdf';
    $order->save();

    return "Ticket generado correctamente";
    return view('orders.ticket', compact('order'));
});