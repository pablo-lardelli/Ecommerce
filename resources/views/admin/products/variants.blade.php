<x-admin-layout :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'route' => route('admin.dashboard'),
    ],
    [
        'name' => 'Productos',
        'route' => route('admin.products.index'),
    ],
    [
        'name' => $product->name,
        'route' => route('admin.products.edit', $product),
    ],
    [
        'name' => $variant->features->pluck('description')->implode(', '),
    ],
]">

    <form action="{{route('admin.products.variantsUpdate', [$product, $variant])}}" 
        method="POST"
        enctype="multipart/form-data">

        @csrf
        @method('PUT')

        <x-validation-errors class="mb-4" />  

        <div class="relative mb-6">

            <figure>
                <img class="aspect-[1/1] w-full object-cover object-center" src="{{ $variant->image }}" alt=""
                    id="imgPreview">
            </figure>

            <div class="absolute top-8 right-8">

                <label class="flex items-center bg-white px-4 py-2 rounded-lg cursor-pointer">
                    <i class="fas fa-camera mr-2"></i>
                    Actualizar imagen

                    <input class="hidden" type="file" accept="image/*" name="image"
                        onchange="previewImage(event, '#imgPreview')">
                </label>

            </div>

        </div>

        <div class="card">

            <div class="mb-4">

                <x-label class="mb-1">
                    Código (SKU)
                </x-label>

                <x-input name="sku" value="{{ old('sku', $variant->sku) }}" placeholder="Ingrese el código (SKU)"
                    class="w-full" />

            </div>

            <div class="mb-4">

                <x-label class="mb-1">
                    Stock
                </x-label>

                <x-input name="stock" value="{{ old('sku', $variant->stock) }}" placeholder="Ingrese el stock"
                    class="w-full" />

            </div>

            <div class="flex justify-end">
                <x-button>
                    Actualizar
                </x-button>
            </div>

        </div>

    </form>

    @push('js')
        <script>
            function previewImage(event, querySelector) {

                //Recuperamos el input que desencadeno la acción
                let input = event.target;

                //Recuperamos la etiqueta img donde cargaremos la imagen
                let imgPreview = document.querySelector(querySelector);

                // Verificamos si existe una imagen seleccionada
                if (!input.files.length) return

                //Recuperamos el archivo subido
                let file = input.files[0];

                //Creamos la url
                let objectURL = URL.createObjectURL(file);

                //Modificamos el atributo src de la etiqueta img
                imgPreview.src = objectURL;

            }
        </script>
    @endpush

</x-admin-layout>
