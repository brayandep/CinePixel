@extends('layouts.app')

@section('title', 'Ver tienda - Cine Pixel')

@section('content')
<div class="productos-wrapper">

    <h1 class="productos-title">VER TIENDA</h1>

    {{-- Mensajes --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
            <strong>Ups...</strong> Revisa los datos ingresados.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- FILTROS --}}
    <form method="GET" action="{{ route('products.index') }}" class="row g-3 mb-3 align-items-end mt-3">
        <div class="col-md-4">
            <label class="productos-label d-block">Buscar por tipo</label>
            <select class="form-select" name="product_type">
                <option value="" {{ empty($product_type) ? 'selected' : '' }}>Todos</option>
                <option value="regalo" {{ $product_type === 'regalo' ? 'selected' : '' }}>Regalos</option>
                <option value="snack" {{ $product_type === 'snack' ? 'selected' : '' }}>Snacks</option>
            </select>
        </div>

        <div class="col-md-4">
            <label class="productos-label d-block">Buscar por nombre</label>
            <input
                type="text"
                name="q"
                class="form-control"
                placeholder="Ej. 'cubo', 'coca', 'peluche'..."
                value="{{ $search }}">
        </div>

        <div class="col-md-4">
            <button type="submit" class="btn btn-primary mt-3 w-100">
                Buscar
            </button>
        </div>
    </form>

    {{-- TABLA DE PRODUCTOS --}}
    <div class="table-responsive mt-3">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 60px;">Nro</th>
                    <th>Nombre de producto</th>
                    <th style="width: 140px;">Costo de producto</th>
                    <th style="width: 140px;">Imagen del producto</th>
                    <th style="width: 100px;">Cantidad</th>
                    <th style="width: 150px;">Agregar productos</th>
                    <th style="width: 150px;">Vender</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        {{-- Nro correlativo considerando la página --}}
                        <td>
                            {{ ($products->currentPage() - 1) * $products->perPage() + $loop->iteration }}
                        </td>

                        <td>{{ $product->name }}</td>

                        <td>{{ number_format($product->price, 2) }} Bs</td>

                        <td class="text-center">
                            @if ($product->image_path)
                                <img src="{{ asset('storage/'.$product->image_path) }}"
                                     alt="Imagen"
                                     style="width: 70px; height: 70px; object-fit: cover; border-radius: 6px;">
                            @else
                                <span class="text-muted">Sin imagen</span>
                            @endif
                        </td>

                        <td class="text-center">
                            {{ $product->stock }}
                        </td>

                        {{-- Botón ADD (abrir modal de agregar) --}}
                        <td class="text-center">
                            <button class="btn btn-success btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalAddProduct{{ $product->id }}">
                                Add
                            </button>
                        </td>

                        {{-- Botón VENDER (abrir modal de vender) --}}
                        <td class="text-center">
                            <button class="btn btn-danger btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalSellProduct{{ $product->id }}">
                                Vender
                            </button>
                        </td>
                    </tr>

                    {{-- MODAL: AGREGAR PRODUCTOS --}}
                    <div class="modal fade"
                         id="modalAddProduct{{ $product->id }}"
                         tabindex="-1"
                         aria-labelledby="modalAddProductLabel{{ $product->id }}"
                         aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalAddProductLabel{{ $product->id }}">
                                        Agregar producto: {{ $product->name }}
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('products.add', $product->id) }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="add_quantity_{{ $product->id }}" class="form-label">
                                                Cantidad a agregar
                                            </label>
                                            <input
                                                type="number"
                                                class="form-control"
                                                id="add_quantity_{{ $product->id }}"
                                                name="quantity"
                                                min="1"
                                                required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            Agregar al inventario
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- MODAL: VENDER PRODUCTOS --}}
                    <div class="modal fade"
                         id="modalSellProduct{{ $product->id }}"
                         tabindex="-1"
                         aria-labelledby="modalSellProductLabel{{ $product->id }}"
                         aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalSellProductLabel{{ $product->id }}">
                                        Vender producto: {{ $product->name }}
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('products.sell', $product->id) }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="sell_quantity_{{ $product->id }}" class="form-label">
                                                Cantidad a vender (stock actual: {{ $product->stock }})
                                            </label>
                                            <input
                                                type="number"
                                                class="form-control"
                                                id="sell_quantity_{{ $product->id }}"
                                                name="quantity"
                                                min="1"
                                                max="{{ $product->stock }}"
                                                required>
                                        </div>
                                        <button type="submit" class="btn btn-danger">
                                            Registrar venta
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            No se encontraron productos con los filtros aplicados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINACIÓN --}}
   @if ($products->hasPages())
        <div class="mt-3 d-flex justify-content-center">
            {{ $products->onEachSide(1)->links('pagination::bootstrap-4') }}
        </div>
    @endif

</div>
@endsection
