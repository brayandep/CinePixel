@extends('layouts.app')

@section('title', 'Iniciar sesión - Cine Pixel')

@section('content')
    <div class="login-container">

        {{-- IZQUIERDA: FORMULARIO --}}
        <div class="login-left">
            <div class="login-card">

                {{-- Mensajes de error generales --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                        <strong>Ups...</strong> Revisa los datos ingresados.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <h1 class="title">BIENVENIDO A<br>CINE PIXEL</h1>

                <p class="subtitle">
                    Para ingresar a la página ingresa tus credenciales,<br>
                    ¡Qué bueno verte!
                </p>

                <form method="POST" action="{{ route('login.post') }}">
                    @csrf
                    <div class="form-group mb-2">
                        <label for="username">Nombre de usuario</label>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            value="{{ old('username') }}"
                            placeholder="Ingresa el usuario">

                        {{-- Error específico del campo --}}
                        @error('username')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-2">
                        <label for="password">Contraseña</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Ingresa la contraseña de usuario">

                        @error('password')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    <button type="submit" class="btn-submit">
                        Iniciar Sesión
                    </button>
                </form>

                <p class="footer-text">“Aquí empieza tu escena favorita”</p>
            </div>
        </div>

        {{-- DERECHA: IMAGEN --}}
        <div class="login-right">
            <div class="login-image"
                 style="background-image: url('{{ asset('img/fondo_cine.jpg') }}');">
            </div>
        </div>

    </div>
@endsection
