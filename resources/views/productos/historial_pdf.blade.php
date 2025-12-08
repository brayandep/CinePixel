<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; text-align: center; }
        th { background: #f0f0f0; }
    </style>
</head>
<body>

<h2>Historial de Movimientos de Productos</h2>

<table>
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

</body>
</html>
