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
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white text-center">
                    <h4><i class="fa-solid fa-user-plus me-2"></i>Registro de Fiscales</h4>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Apellido</label>
                                <input type="text" name="lastname" class="form-control" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">DNI</label>
                                <input type="text" name="dni" class="form-control" maxlength="8" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Departamento / Zona</label>
                                <select name="department_id" class="form-select" required>
                                    <option value="">Seleccione...</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contraseña</label>
                                <input type="password" name="password" class="form-control" required minlength="6">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Confirmar Contraseña</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Registrarse</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection