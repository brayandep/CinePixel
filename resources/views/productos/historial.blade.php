@extends('layouts.app')

@section('title', 'Historial de Productos')

@section('content')

 <h1 class="productos-title">Historial de Movimientos</h1>
<form class="row g-3 mb-4" method="GET">
    <div class="col-md-3">
        <label class="form-label">Desde</label>
        <input type="date" name="from" class="form-control" value="{{ $from }}">
    </div>

    <div class="col-md-3">
        <label class="form-label">Hasta</label>
        <input type="date" name="to" class="form-control" value="{{ $to }}">
    </div>

    <div class="col-md-3">
        <label class="form-label">Tipo</label>
        <select name="movement_type" class="form-select">
            <option value="">Todos</option>
            <option value="entrada" {{ $movement_type=='entrada'?'selected':'' }}>Entrada</option>
            <option value="venta" {{ $movement_type=='venta'?'selected':'' }}>Venta</option>
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label d-block invisible">.</label>
        <button class="btn btn-primary w-100">Filtrar</button>
    </div>
</form>

<a href="{{ route('products.history.pdf', request()->all()) }}" class="btn btn-success mb-3">
    Descargar PDF
</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Producto</th>
            <th>Tipo</th>
            <th>Cantidad</th>
            <th>Precio Unit.</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($movements as $m)
            <tr>
                <td>{{ $m->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $m->product->name }}</td>
                <td>{{ ucfirst($m->movement_type) }}</td>
                <td>{{ $m->quantity }}</td>
                <td>{{ number_format($m->unit_price, 2) }} Bs</td>
                <td>{{ number_format($m->total_price, 2) }} Bs</td>
            </tr>
        @endforeach
    </tbody>
</table>

{{ $movements->links() }}

@endsection
