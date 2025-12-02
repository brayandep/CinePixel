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
            'room_id'         => ['required', 'exists:rooms,id'],
            'start_time'      => ['required', 'date_format:H:i'],
            'end_time'        => ['required', 'date_format:H:i', 'after:start_time'],
            'two_people'      => ['required', 'in:0,1'],
            'products_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method'  => ['required', 'in:efectivo,qr'],
        ]);

        $today = now()->format('Y-m-d');

        $start = Carbon::createFromFormat('Y-m-d H:i', $today.' '.$data['start_time']);
        $end   = Carbon::createFromFormat('Y-m-d H:i', $today.' '.$data['end_time']);
    
       $numPeople = $data['two_people'] ? 2 : 1;
        $products  = $data['products_amount'] ?? 0;

        // Cálculo de horas redondeado hacia arriba
        $minutes = $start->diffInMinutes($end);
        $hours   = (int) ceil($minutes / 60);
        if ($hours < 1) $hours = 1;

        // 10 Bs POR PERSONA POR HORA → esto es SOLO la sala
        $entryAmount = 10 * $numPeople * $hours;

        // TOTAL que paga el cliente (sala + productos)
        $total = $entryAmount + $products;

        Reservation::create([
            'room_id'         => $data['room_id'],
            'user_id'         => auth()->id(),
            'start_time'      => $start,
            'end_time'        => $end,
            'num_people'      => $numPeople,
            'products_amount' => $products,
            'entry_amount'    => $entryAmount,   // 👈 solo entradas
            'payment_method'  => $data['payment_method'],
            'total'           => $total,         // 👈 entradas + productos
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
