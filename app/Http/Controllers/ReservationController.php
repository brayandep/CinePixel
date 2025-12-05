<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReservationController extends Controller
{
  public function index()
    {
        // Traemos TODAS las salas con todas sus reservas
        $rooms = Room::with('reservations')
            ->orderBy('id')
            ->get();

        // Calculamos el estado SOLO en función de status = 'activa'
        $rooms = $rooms->map(function ($room) {
            // Reserva activa más reciente (si hay)
            $active = $room->reservations
                ->where('status', 'activa')
                ->sortByDesc('start_time')
                ->first();

            $room->estado        = $active ? 'Ocupado' : 'Libre';
            $room->inicio_actual = $active ? $active->start_time->format('H:i') : '00:00';
            $room->fin_actual    = $active ? $active->end_time->format('H:i') : '00:00';
            $room->active_reservation_id = $active ? $active->id : null;

            return $room;
        });

        // Salas libres para el select
        $availableRooms = $rooms->filter(function ($room) {
            return $room->estado === 'Libre';
        });

        return view('reservas.index', [
            'rooms'          => $rooms,
            'availableRooms' => $availableRooms,
        ]);
    }
    public function store(Request $request)
{
    // Validación básica
    $data = $request->validate([
        'room_id'         => ['required_if:two_people,!=,3', 'exists:rooms,id'], // Solo obligatorio si no es "Solo tienda"
        'start_time'      => ['required', 'date_format:H:i'],
        'end_time'        => ['required', 'date_format:H:i', 'after:start_time'],
        'two_people'      => ['required', 'in:0,1,2,3'], // Incluimos la opción "Solo tienda"
        'products_amount' => ['nullable', 'numeric', 'min:0'],
        'payment_method'  => ['required', 'in:efectivo,qr'],
    ]);

    $today = now()->format('Y-m-d');
    $start = Carbon::createFromFormat('Y-m-d H:i', $today.' '.$data['start_time'], 'America/La_Paz');
    $end   = Carbon::createFromFormat('Y-m-d H:i', $today.' '.$data['end_time'], 'America/La_Paz');

    // Si selecciona "Solo tienda", no cobramos por la sala
    $numPeople = $data['two_people'] == 3 ? 0 : ($data['two_people'] == 1 ? 1 : 2);  // Cambiamos a 0 si es solo tienda
    $products  = $data['products_amount'] ?? 0;

    // Calculamos el costo por hora
    $entryAmount = 0; // Inicializamos en 0 para el caso "Solo tienda"
    if ($data['two_people'] != 3) {
        // Calculamos solo si no es "Solo tienda"
        $hours = $start->diffInHours($end);
        $hours = $hours < 1 ? 1 : $hours; // Aseguramos que no sea menos de 1 hora
        $entryAmount = 10 * $numPeople * $hours; // Costo por hora
    }

    // Total a pagar (entrada + productos)
    $total = $entryAmount + $products;

    // Si es "Solo tienda", asignamos el room_id de la sala "Tienda"
    $roomId = $data['two_people'] == 3 ? Room::where('name', 'Tienda')->first()->id : $data['room_id'];

    Reservation::create([
        'room_id'         => $roomId,  // Usamos el room_id de "Tienda" cuando es "Solo tienda"
        'user_id'         => auth()->id(),
        'start_time'      => $start,
        'end_time'        => $end,
        'num_people'      => $numPeople,
        'products_amount' => $products,
        'entry_amount'    => $entryAmount,
        'payment_method'  => $data['payment_method'],
        'total'           => $total,
        'status'          => 'activa',
    ]);

    return redirect()
        ->route('reservas.index')
        ->with('success', 'Reserva registrada correctamente.');
}


    public function finish(Reservation $reservation)
    {
        // Marcamos la reserva como finalizada
        $reservation->status = 'finalizada';
        $reservation->save();

        return back()->with('success', 'La sala ha sido marcada como libre.');
    }
}
