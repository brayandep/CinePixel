@extends('layouts.app')

@section('title', 'Registrar productos - Cine Pixel')

@section('content')
<div class="productos-wrapper">

    <h1 class="productos-title">REGISTRAR PRODUCTOS</h1>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
            <strong>Ups...</strong> Revisa los datos del formulario.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="productos-form-card mt-3">
        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                {{-- COLUMNA IZQUIERDA --}}
                <div class="col-md-7">
                    {{-- Nombre --}}
                    <div class="mb-3">
                        <label class="productos-label" for="name">Nombre de producto</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control"
                            placeholder="Ingresa el nombre de producto"
                            value="{{ old('name') }}">
                        @error('name')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Costo --}}
                    <div class="mb-3">
                        <label class="productos-label" for="price">Costo de producto (Bs)</label>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            id="price"
                            name="price"
                            class="form-control"
                            placeholder="Ingresa el costo de producto"
                            value="{{ old('price') }}">
                        @error('price')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Cantidad --}}
                    <div class="mb-3">
                        <label class="productos-label" for="stock">Cantidad del producto</label>
                        <input
                            type="number"
                            min="0"
                            id="stock"
                            name="stock"
                            class="form-control"
                            placeholder="Ingresa la cantidad de producto"
                            value="{{ old('stock') }}">
                        @error('stock')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Descripción --}}
                    <div class="mb-3">
                        <label class="productos-label" for="description">Descripción del producto</label>
                        <textarea
                            id="description"
                            name="description"
                            rows="4"
                            class="form-control"
                            placeholder="Ingresa las características">{{ old('description') }}</textarea>
                    </div>
                </div>

                {{-- COLUMNA DERECHA --}}
                <div class="col-md-5">
                    {{-- Estado --}}
                    <div class="mb-3">
                        <label class="productos-label">Estado del producto</label>
                        <select name="status" class="form-select">
                            <option value="disponible" {{ old('status', 'disponible') == 'disponible' ? 'selected' : '' }}>
                                Disponible
                            </option>
                            <option value="no_disponible" {{ old('status') == 'no_disponible' ? 'selected' : '' }}>
                                No disponible
                            </option>
                        </select>
                    </div>
                    
                    {{-- Tipo de producto --}}
                    <div class="mb-3">
                        <label class="productos-label">Tipo de producto</label>
                        <select name="product_type" class="form-select">
                            <option value="regalo" {{ old('product_type', 'regalo') == 'regalo' ? 'selected' : '' }}>
                                Regalos
                            </option>
                            <option value="snack" {{ old('product_type') == 'snack' ? 'selected' : '' }}>
                                Snacks
                            </option>
                        </select>
                    </div>
                    {{-- Imagen actual / preview simple --}}
                    <div class="mb-3 text-center">
                        <div class="productos-image-preview mb-2" id="imagePreview">
                            <span class="text-muted">Sin imagen</span>
                        </div>

                        <label class="btn btn-success btn-sm">
                            Subir imagen
                            <input type="file" name="image" id="imageInput" accept="image/*" hidden>
                        </label>

                        @error('image')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary w-100">
                            REGISTRAR
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Preview rápida de la imagen seleccionada --}}
<script>
    const imageInput = document.getElementById('imageInput');
    const imagePreview = document.getElementById('imagePreview');

    if (imageInput) {
        imageInput.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) {
                imagePreview.innerHTML = '<span class="text-muted">Sin imagen</span>';
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                imagePreview.innerHTML =
                    '<img src="' + e.target.result + '" alt="Vista previa" style="max-width:100%; max-height:200px; border-radius:8px; object-fit:cover;">';
            };
            reader.readAsDataURL(file);
        });
    }
</script>
@endsection
