@extends('layouts.app')

@section('title', 'Reserva de salas - Cine Pixel')

@section('content')
<div class="reservas-wrapper">

    {{-- IZQUIERDA: FORMULARIO --}}
    <div class="reservas-left">
        <h1 class="reservas-title">RESERVA DE SALAS</h1>
        <p class="reservas-subtitle">
            Aquí vamos a registrar las salas que están ocupadas o libres.
        </p>

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

        <div class="reservas-form-card mt-3">
            <form method="POST" action="{{ route('reservas.store') }}">
                @csrf

                {{-- Salas disponibles --}}
                <div class="mb-3">
                    <label class="reservas-label">Salas disponibles</label>
                    <select name="room_id" class="form-select">
                        @forelse ($availableRooms as $room)
                            <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                {{ $room->name }}
                            </option>
                        @empty
                            <option value="">No hay salas libres en este momento</option>
                        @endforelse
                    </select>
                </div>

                {{-- Hora inicio / salida --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="reservas-label">Hora de inicio</label>
                        <input type="time" name="start_time" class="form-control"
                               value="{{ old('start_time') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="reservas-label">Hora de salida</label>
                        <input type="time" name="end_time" class="form-control"
                               value="{{ old('end_time') }}">
                    </div>
                </div>

                {{-- Entrada para dos personas --}}
                <div class="mb-3">
                    <label class="reservas-label">Entrada para dos personas</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="two_people" id="two_yes" value="1"
                               {{ old('two_people', '1') == '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="two_yes">
                            Sí (20 Bs la hora)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="two_people" id="two_no" value="0"
                               {{ old('two_people') == '0' ? 'checked' : '' }}>
                        <label class="form-check-label" for="two_no">
                            No (10 Bs la hora)
                        </label>
                    </div>
                </div>

                {{-- Productos consumidos --}}
                <div class="mb-3">
                    <label class="reservas-label">Productos consumidos (Bs)</label>
                    <input type="number" step="0.01" min="0" name="products_amount"
                           id="products_amount" class="form-control"
                           value="{{ old('products_amount', 0) }}">
                </div>

                {{-- Método de pago --}}
                <div class="mb-3">
                    <label class="reservas-label">Método de pago</label>
                    <select name="payment_method" class="form-select">
                        <option value="efectivo" {{ old('payment_method') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                        <option value="qr" {{ old('payment_method') == 'qr' ? 'selected' : '' }}>QR</option>
                    </select>
                </div>

                {{-- Costos --}}
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="reservas-label d-block">Costo por hora</span>
                        <span id="hour_cost_display" class="badge bg-primary fs-6">10 Bs</span>
                    </div>
                    <div>
                        <span class="reservas-label d-block">Total a pagar</span>
                        <span id="total_display" class="badge bg-warning text-dark fs-5">0 Bs</span>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 mt-2">
                    REGISTRAR
                </button>
            </form>
        </div>
    </div>

    {{-- DERECHA: ESTADO DE SALAS --}}
    <div class="reservas-right">
        <h1 class="reservas-title">ESTADO DE SALAS</h1>

        <div class="salas-status-list mt-3">
            @foreach ($rooms as $room)
                <div class="sala-row">
                    <div class="sala-name">{{ strtoupper($room->name) }}</div>

                    <div class="d-flex align-items-center gap-2">
                        @if ($room->estado === 'Libre')
                            <div class="sala-badge sala-badge-libre">Libre</div>
                        @else
                            <div class="sala-badge sala-badge-ocupado">Ocupado</div>

                            @if ($room->active_reservation_id)
                                <form method="POST" action="{{ route('reservas.finish', $room->active_reservation_id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-info">
                                        Liberar
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>

                    <div class="sala-times">
                        Inicio {{ $room->inicio_actual }} am/pm<br>
                        Salida {{ $room->fin_actual }} am/pm
                    </div>
                </div>
            @endforeach

        </div>
    </div>

</div>
<script>
    const productsInput   = document.getElementById('products_amount');
    const hourCostDisplay = document.getElementById('hour_cost_display');
    const totalDisplay    = document.getElementById('total_display');
    const radioYes        = document.getElementById('two_yes');
    const radioNo         = document.getElementById('two_no');
    const startInput      = document.querySelector('[name="start_time"]');
    const endInput        = document.querySelector('[name="end_time"]');

    function calcHours() {
        const startVal = startInput.value;
        const endVal   = endInput.value;

        if (!startVal || !endVal) return 1;

        const [sh, sm] = startVal.split(':').map(Number);
        const [eh, em] = endVal.split(':').map(Number);

        const startMinutes = sh * 60 + sm;
        const endMinutes   = eh * 60 + em;

        if (endMinutes <= startMinutes) return 1;

        const diffMinutes = endMinutes - startMinutes;

        // Redondear hacia arriba a la hora completa
        let hours = Math.ceil(diffMinutes / 60);

        if (hours < 1) hours = 1;

        return hours;
    }

    function updateTotals() {
        const basePerPerson = 10;
        const twoPeople = radioYes.checked;
        const numPeople = twoPeople ? 2 : 1;

        const hours = calcHours();

        let products = parseFloat(productsInput.value);
        if (isNaN(products) || products < 0) products = 0;

        const hourCost = basePerPerson * numPeople * hours;
        const total    = hourCost + products;

        hourCostDisplay.textContent = hourCost.toFixed(2) + ' Bs';
        totalDisplay.textContent    = total.toFixed(2) + ' Bs';
    }

    radioYes.addEventListener('change', updateTotals);
    radioNo.addEventListener('change', updateTotals);
    productsInput.addEventListener('input', updateTotals);
    startInput.addEventListener('change', updateTotals);
    endInput.addEventListener('change', updateTotals);

    // cálculo inicial
    updateTotals();
</script>
<script>
    // Recargar la página cada 60 segundos para actualizar el estado de las salas
    setInterval(function () {
        window.location.reload();
    }, 60000);
</script>
@endsection
