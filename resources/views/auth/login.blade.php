@extends('layouts.app')

@section('content')

@push('styles')
<style>
    body {
        /* Ruta de la imagen: public/img/fondo-login.jpg */
        background-image: url("{{ asset('img/fisq-fondo.png') }}");
        
        /* Propiedades para que cubra toda la pantalla correctamente */
        background-size: auto;
        background-position: center;
        background-repeat: repeat;
        background-attachment: fixed;
        min-height: 100vh;
    }

    /* Opcional: Un fondo semitransparente negro sobre la imagen para que no encandile */
    body::before {
        content: "";
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background-color: rgba(0, 0, 0, 0.4); /* 40% de oscuridad */
        z-index: -1;
    }
</style>
@endpush
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4><i class="fa-solid fa-right-to-bracket me-2"></i>Ingresar</h4>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" name="email" class="form-control" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="password" class="form-control" required maxlength="16">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Ingresar</button>
                        @if (Route::has('password.request'))
                        <div class="text-center">
                             <a class="btn btn-link ps-0" href="{{ route('password.request') }}">
                            {{ __('¿Olvidaste tu contraseña?') }}
                             </a>
                        </div>
                        @endif
                    </form>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('register') }}" class="text-decoration-none">¿No tienes cuenta? Regístrate aquí</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection