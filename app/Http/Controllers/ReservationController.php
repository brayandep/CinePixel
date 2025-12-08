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
        $rooms = Room::with('reservations')->orderBy('id')->get();

        $rooms = $rooms->map(function ($room) {
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

        $availableRooms = $rooms->filter(fn ($room) => $room->estado === 'Libre');

        return view('reservas.index', [
            'rooms'          => $rooms,
            'availableRooms' => $availableRooms,
        ]);
    }


    public function store(Request $request)
    {
        // Validación
        $data = $request->validate([
            'room_id'         => ['required_if:two_people,!=,3', 'exists:rooms,id'],
            'start_time'      => ['required', 'date_format:H:i'],
            'end_time'        => ['required', 'date_format:H:i', 'after:start_time'],
            'two_people'      => ['required', 'in:0,1,3'], 
            'products_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method'  => ['required', 'in:efectivo,qr'],
        ]);

        // FECHA – siempre Bolivia
        $today = now()->setTimezone('America/La_Paz')->format('Y-m-d');

        $start = Carbon::createFromFormat('Y-m-d H:i', $today.' '.$data['start_time'], 'America/La_Paz');
        $end   = Carbon::createFromFormat('Y-m-d H:i', $today.' '.$data['end_time'], 'America/La_Paz');


        // -------------------------------
        // ASIGNACIÓN CORRECTA DE PERSONAS
        // -------------------------------
        if ($data['two_people'] == 1) {
            // ✔ Sí (20 Bs) → 2 personas
            $numPeople = 2;
        } elseif ($data['two_people'] == 0) {
            // ✔ No (10 Bs) → 1 persona
            $numPeople = 1;
        } else {
            // ✔ Solo tienda → 0 personas
            $numPeople = 0;
        }

        // Productos
        $products = $data['products_amount'] ?? 0;

        // Cálculo de horas (mínimo 1)
        $hours = max(1, $start->diffInMinutes($end) / 60);
        $hours = ceil($hours);

        // -----------------------------------
        // COSTO POR HORA (corregido totalmente)
        // -----------------------------------
        if ($data['two_people'] == 3) {
            // Solo tienda
            $entryAmount = 0;
        } else {
            // 10 Bs POR PERSONA POR HORA
            $entryAmount = 10 * $numPeople * $hours;
        }

        // Total
        $total = $entryAmount + $products;

        // Sala tienda si corresponde
        if ($data['two_people'] == 3) {
            $roomId = Room::where('name', 'Tienda')->first()->id;
        } else {
            $roomId = $data['room_id'];
        }

        // Crear reserva
        Reservation::create([
            'room_id'         => $roomId,
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
        $reservation->status = 'finalizada';
        $reservation->save();

        return back()->with('success', 'La sala ha sido marcada como libre.');
    }
}
