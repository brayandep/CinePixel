<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    private function getDateRange(string $type, string $date): array
    {
        $ref = Carbon::parse($date);

        switch ($type) {
            case 'week':
                $start = $ref->copy()->startOfWeek(Carbon::MONDAY);
                $end   = $ref->copy()->endOfWeek(Carbon::SUNDAY);
                break;
            case 'month':
                $start = $ref->copy()->startOfMonth();
                $end   = $ref->copy()->endOfMonth();
                break;
            case 'day':
            default:
                $start = $ref->copy()->startOfDay();
                $end   = $ref->copy()->endOfDay();
                break;
        }

        return [$start, $end];
    }

    public function sales(Request $request)
    {
        $type = $request->get('type', 'day'); // day | week | month
        $date = $request->get('date', now()->toDateString());

        [$start, $end] = $this->getDateRange($type, $date);

        // todas las reservas del rango (puedes filtrar solo finalizadas si quieres)
        $reservations = Reservation::with('room')
            ->whereBetween('start_time', [$start, $end])
            ->orderBy('start_time')
            ->get();

        // Solo entradas (salas)
        $totalQrEntries = $reservations->where('payment_method', 'qr')->sum('entry_amount');
        $totalEfEntries = $reservations->where('payment_method', 'efectivo')->sum('entry_amount');
        $totalEntries   = $reservations->sum('entry_amount');

        // Productos aparte
        $totalProducts  = $reservations->sum('products_amount');

        // Total global si quieres verlo (entradas + productos)
        $totalAll = $totalEntries + $totalProducts;
        return view('reportes.ventas', [
        'reservations'   => $reservations,
        'type'           => $type,
        'date'           => $date,
        'start'          => $start,
        'end'            => $end,
        'totalQrEntries' => $totalQrEntries,
        'totalEfEntries' => $totalEfEntries,
        'totalEntries'   => $totalEntries,
        'totalProducts'  => $totalProducts,
        'totalAll'       => $totalAll,
        ]);
    }

    public function salesPdf(Request $request)
    {
    $type = $request->get('type', 'day');
    $date = $request->get('date', now()->toDateString());

    [$start, $end] = $this->getDateRange($type, $date);

    $reservations = Reservation::with('room')
        ->whereBetween('start_time', [$start, $end])
        ->orderBy('start_time')
        ->get();

    // Solo ENTRADAS (sala)
    $totalQrEntries = $reservations->where('payment_method', 'qr')->sum('entry_amount');
    $totalEfEntries = $reservations->where('payment_method', 'efectivo')->sum('entry_amount');
    $totalEntries   = $reservations->sum('entry_amount');

    // Productos aparte
    $totalProducts  = $reservations->sum('products_amount');

    // Total general (entradas + productos)
    $totalAll = $totalEntries + $totalProducts;

    $pdf = Pdf::loadView('reportes.ventas_pdf', [
        'reservations'   => $reservations,
        'type'           => $type,
        'date'           => $date,
        'start'          => $start,
        'end'            => $end,
        'totalQrEntries' => $totalQrEntries,
        'totalEfEntries' => $totalEfEntries,
        'totalEntries'   => $totalEntries,
        'totalProducts'  => $totalProducts,
        'totalAll'       => $totalAll,
    ]);

    $filename = 'reporte_ventas_' . $type . '_' . $date . '.pdf';

    return $pdf->download($filename);
    }
}
