<?php

namespace App\Observers;

use App\Models\Variant;
use Illuminate\Support\Str;

class VariantObserver
{
    public function created(Variant $variant)
    {
        if($variant->product->options->count() == 0){
            $variant->sku = $variant->product->sku;
            $variant->save();

            return;
        }

        //Queremos generar un sku de 12 dígitos
        $variant->sku = Str::random(12);
        $variant->save();
    }
}
