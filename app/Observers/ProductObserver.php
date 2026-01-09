<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\Variant;

class ProductObserver
{
    public function created(Product $product)
    {
        Variant::create([
            'product_id' => $product->id
        ]);
    }
}
