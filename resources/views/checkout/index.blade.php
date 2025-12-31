<x-app-layout>

    <div class="-mb-16 text-gray-700" x-data="{
        pago: 1
    }">
        <div class="grid grid-cols-1 lg:grid-cols-2">
            <div class="col-span-1 bg-white">
                <div class="lg:max-w-[40rem] py-12 px-4 lg:pr-8 sm:pl-6 lg:pl-8 ml-auto">
                    <h1 class="text-2xl font-semibold mb-2">
                        Pago
                    </h1>

                    <div class="shadow rounded-lg overflow-hidden border border-gray-400">
                        <ul class="divide-y divide-gray-400">
                            <li>
                                <label class="p-4 flex items-center">
                                    <input type="radio" x-model="pago" value="1">

                                    <span class="ml-2">
                                        Tarjeta de débito/crédito
                                    </span>

                                    <img class="h-6 ml-auto" src="{{ asset('img/credit-cards.png') }}" alt="">
                                </label>

                                <div class="p-4 bg-gray-100 text-center border-t border-gray-400" x-show="pago == 1">
                                    <i class="fa-regular fa-credit-card text-9xl"></i>

                                    <p class="mt-2">
                                        Luego de hacer click en "Pagar ahora", se abrirá Mercado Pago para completa tu
                                        compra de forma segura.
                                    </p>
                                </div>
                            </li>

                            <li>
                                <label class="p-4 flex items-center">
                                    <input type="radio" x-model="pago" value="2">

                                    <span class="ml-2">
                                        Depósito bancario
                                    </span>
                                </label>

                                <div class="p-4 bg-gray-100 flex justify-center border-t border-gray-400" x-cloak
                                    x-show="pago == 2">
                                    <div>
                                        <p>1. Pago por depósito o tranferencia bancaria:</p>
                                        <p>- Santander: 198-987456321-45</p>
                                        <p>- CCI: 002 - 198-987456321</p>
                                        <p>- Razón social: Eccomerce S.A</p>
                                        <p>- CUIT: 20-25456987-7</p>
                                        <p>2. Mercado Pago</p>
                                        <p>- 54654654234387 (Eccomerce S.A)</p>
                                        <p>
                                            Enviar el comprobante de pago a 466066
                                        </p>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-span-1">
                <div class="lg:max-w-[40rem] py-12 px-4 lg:pl-8 sm:pr-6 lg:pr-8 mr-auto">
                    <ul class="space-y-4 mb-4">
                        @foreach (Cart::instance('shopping')->content() as $item)
                            <li class="flex items-center space-x-4">
                                <div class="flex-shrink-0 relative">
                                    <img class="h-16 aspect-square" src="{{ $item->options->image }}" alt="">

                                    <div
                                        class="flex justify-center items-center h-6 w-6 bg-gray-900 opacity-70 rounded-full absolute -right-2 -top-2">
                                        <span class="text-white font-semibold">
                                            {{ $item->qty }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex-1">
                                    <p>
                                        {{ $item->name }}
                                    </p>
                                </div>

                                <div class="flex-shrink-0">
                                    <p>
                                        $ {{ $item->price }}
                                    </p>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <div class="flex justify-between">
                        <p>
                            Subtotal
                        </p>

                        <p>
                            $ {{ Cart::instance('shopping')->subtotal() }}
                        </p>
                    </div>

                    <div class="flex justify-between">
                        <p>
                            Precio de envío

                            <i class="fas fa-info-circle" title="El precio de envío es de $1000"></i>
                        </p>

                        <p>
                            $ 1000
                        </p>
                    </div>

                    <hr class="my-3">

                    <div class="flex justify-between mb-4">
                        <p class="text-lg font-semibold">
                            Total
                        </p>

                        <p>
                            $ {{ Cart::instance('shopping')->subtotal() + 1000 }}
                        </p>
                    </div>

                    {{-- <div>
                        <button class="btn btn-purple w-full">
                            Finalizar pedido
                        </button>
                    </div> --}}
                    
                    {{-- //MERCADO PAGO --}}
                    <div id="walletBrick_container"></div>
                </div>
            </div>
        </div>
    </div>

    @push('js')
        <script src="https://sdk.mercadopago.com/js/v2"></script>
        <script>
            // Configure sua chave pública do Mercado Pago
            const publicKey = "{{ config('services.mercadopago.public_key') }}";
            // Configure o ID de preferência que você deve receber do seu backend
            const preferenceId = "{{ $preferenceId }}";

            // Inicializa o SDK do Mercado Pago
            const mp = new MercadoPago(publicKey);

            // Cria o botão de pagamento
            const bricksBuilder = mp.bricks();
            const renderWalletBrick = async (bricksBuilder) => {
                await bricksBuilder.create("wallet", "walletBrick_container", {
                    initialization: {
                        preferenceId: "{{ $preferenceId }}",
                        redirectMode: "blank"
                    }
                });
            };

            renderWalletBrick(bricksBuilder);
        </script>
    @endpush

</x-app-layout>
