<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de ventas</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { font-size: 20px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 4px 6px; text-align: center; }
        th { background: #f0f0f0; }
        .totals { margin-top: 15px; }
        .totals div { margin-bottom: 4px; }
    </style>
</head>
<body>
    <h1>Reporte de ventas - Cine Pixel</h1>
    <p>
        Período:
        {{ $start->format('d/m/Y H:i') }} - {{ $end->format('d/m/Y H:i') }}<br>
        Generado: {{ now()->format('d/m/Y H:i') }}
    </p>

    <table>
       <thead>
            <tr>
                <th>Nro</th>
                <th>Sala</th>
                <th>Personas</th>
                <th>Inicio</th>
                <th>Fin</th>
                <th>Pago</th>
                <th>Entrada (Bs)</th>   <!-- nuevo -->
                <th>Prod. (Bs)</th>
                <th>Total (Bs)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reservations as $idx => $res)
                <tr>
                   <td>{{ $idx + 1 }}</td>
                    <td>{{ $res->room->name ?? '-' }}</td>
                    <td>{{ $res->num_people }}</td>
                    <td>{{ $res->start_time->format('d/m H:i') }}</td>
                    <td>{{ $res->end_time->format('d/m H:i') }}</td>
                    <td>{{ ucfirst($res->payment_method) }}</td>
                    <td>{{ number_format($res->entry_amount, 2) }}</td>       <!-- entrada -->
                    <td>{{ number_format($res->products_amount, 2) }}</td>    <!-- productos -->
                    <td>{{ number_format($res->total, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">No hay datos para este período.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

       <div class="totals">
        <div><strong>Total ENTRADAS en QR:</strong> {{ number_format($totalQrEntries, 2) }} Bs</div>
        <div><strong>Total ENTRADAS en Efectivo:</strong> {{ number_format($totalEfEntries, 2) }} Bs</div>
        <div><strong>Total ENTRADAS (todas las salas):</strong> {{ number_format($totalEntries, 2) }} Bs</div>
        <div><strong>Total PRODUCTOS consumidos:</strong> {{ number_format($totalProducts, 2) }} Bs</div>
        <div><strong>Total GENERAL (entradas + productos):</strong> {{ number_format($totalAll, 2) }} Bs</div>
    </div>


</body>
</html>
