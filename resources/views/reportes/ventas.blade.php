@extends('layouts.app')

@section('title', 'Reporte de ventas - Cine Pixel')

@section('content')
<div class="report-wrapper">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="report-title">REPORTE DE VENTAS</h1>
        </div>

        {{-- Botón descargar PDF con mismos filtros --}}
        <div>
            <a href="{{ route('reports.sales.pdf', ['type' => $type, 'date' => $date]) }}"
               class="btn btn-success">
                Descargar reporte
            </a>
        </div>
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('reports.sales') }}" class="report-filters mb-3">
        <div class="me-4">
            <span class="report-label">BUSCAR</span>
        </div>

        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="type" id="type_day"
                   value="day" {{ $type === 'day' ? 'checked' : '' }}>
            <label class="form-check-label" for="type_day">Fecha</label>
        </div>

        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="type" id="type_week"
                   value="week" {{ $type === 'week' ? 'checked' : '' }}>
            <label class="form-check-label" for="type_week">Semanal</label>
        </div>

        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="type" id="type_month"
                   value="month" {{ $type === 'month' ? 'checked' : '' }}>
            <label class="form-check-label" for="type_month">Mes</label>
        </div>

        <div class="ms-4">
            <input type="date" name="date" value="{{ $date }}" class="form-control form-control-sm">
        </div>

        <div class="ms-3">
            <button type="submit" class="btn btn-primary btn-sm">
                Aplicar
            </button>
        </div>
    </form>

    {{-- Info del rango seleccionado --}}
    <p class="text-muted mb-2">
        Mostrando ventas desde <strong>{{ $start->format('d/m/Y H:i') }}</strong>
        hasta <strong>{{ $end->format('d/m/Y H:i') }}</strong>
    </p>

    {{-- Tabla de ventas --}}
    <div class="table-responsive report-table-wrapper">
        <table class="table table-bordered table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th>Nro</th>
                    <th>Nro de sala</th>
                    <th>Número de personas</th>
                    <th>Hora de inicio</th>
                    <th>Hora finalizada</th>
                    <th>Tipo de pago</th>
                    <th>Entrada (Bs)</th>
                    <th>Productos consumidos (Bs)</th>
                    <th>Total a pagar (Bs)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reservations as $idx => $res)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>{{ $res->room->name ?? '-' }}</td>
                        <td>{{ $res->num_people }}</td>
                        <td>{{ $res->start_time->format('H:i') }}</td>
                        <td>{{ $res->end_time->format('H:i') }}</td>
                        <td>{{ ucfirst($res->payment_method) }}</td>
                        <td>{{ number_format($res->entry_amount, 2) }}</td>
                        <td>{{ number_format($res->products_amount, 2) }}</td>
                        <td>{{ number_format($res->total, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">
                            No hay ventas registradas para este período.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Totales --}}
        <div class="report-totals mt-4">
        <div class="report-total-row">
            <span>Total ENTRADAS en QR</span>
            <span class="badge bg-success fs-5">
                {{ number_format($totalQrEntries, 2) }} Bs
            </span>
        </div>

        <div class="report-total-row">
            <span>Total ENTRADAS en Efectivo</span>
            <span class="badge bg-primary fs-5">
                {{ number_format($totalEfEntries, 2) }} Bs
            </span>
        </div>

        <div class="report-total-row">
            <span>Total ENTRADAS (todas las salas)</span>
            <span class="badge bg-dark fs-5">
                {{ number_format($totalEntries, 2) }} Bs
            </span>
        </div>

        <div class="report-total-row">
            <span>Total PRODUCTOS consumidos</span>
            <span class="badge bg-info text-dark fs-5">
                {{ number_format($totalProducts, 2) }} Bs
            </span>
        </div>

        <div class="report-total-row">
            <span>Total GENERAL (entradas + productos)</span>
            <span class="badge bg-warning text-dark fs-5">
                {{ number_format($totalAll, 2) }} Bs
            </span>
        </div>
    </div>


</div>
@endsection
