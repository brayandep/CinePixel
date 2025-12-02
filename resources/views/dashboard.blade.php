@extends('layouts.app')

@section('title', 'Panel - Cine Pixel')

@section('content')
    <div class="container py-5">
        <div class="alert alert-success" role="alert">
            Hola, {{ auth()->user()->name }} 👋
            Has iniciado sesión correctamente en Cine Pixel.
        </div>

        <p>Esta es una vista de prueba del panel principal. Aquí luego mostraremos la cartelera y las reservas.</p>

        <form action="{{ route('logout') }}" method="POST" class="mt-3">
            @csrf
            <button type="submit" class="btn btn-outline-danger">
                Cerrar sesión
            </button>
        </form>
    </div>
@endsection
