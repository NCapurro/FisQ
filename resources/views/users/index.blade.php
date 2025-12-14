@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4 gap-3">
    <h2 class="mb-0"><i class="fa-solid fa-users-gear me-2"></i>Gestión de Usuarios</h2>
    
    <div class="d-flex gap-2 align-items-center">
        
        <form method="GET" action="{{ route('users.index') }}" id="filterUserForm">
            <div class="input-group">
                <span class="input-group-text bg-white text-muted">
                    <i class="fa-solid fa-filter"></i>
                </span>
                
                <select name="department_id" class="form-select" onchange="document.getElementById('filterUserForm').submit()">
                    <option value="">-- Todos los Deptos --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>

                @if(request('department_id'))
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary" title="Ver todos">
                        <i class="fa-solid fa-times"></i>
                    </a>
                @endif
            </div>
        </form>

        <a href="{{ route('users.create') }}" class="btn btn-primary text-nowrap">
            <i class="fa-solid fa-user-plus me-1"></i> Registrar Nuevo
        </a>
        <div class="d-flex gap-2">
    @if(request('view_deleted'))
        <a href="{{ route('users.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Volver a Activos
        </a>
    @else
        <a href="{{ route('users.index', ['view_deleted' => 1]) }}" class="btn btn-outline-danger">
            <i class="fa-solid fa-trash-can me-1"></i> Papelera
        </a>
    @endif
    
    </div>
    </div>

    
</div>




    <div class="accordion" id="accordionUsers">
        @forelse($users as $user)
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading{{ $user->id }}">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $user->id }}">
                        <div class="d-flex w-100 justify-content-between align-items-center me-3">
                            <span>
                                <strong>{{ $user->dni }}</strong> - {{ $user->lastname }}, {{ $user->name }}
                            </span>
                            
                            <div>
                                @if($user->role === 'admin')
                                    <span class="badge bg-danger me-2">Admin</span>
                                @else
                                    <span class="badge bg-info text-dark me-2">Fiscal</span>
                                @endif
                                
                                <span class="badge bg-secondary rounded-pill" style="font-size: 0.7em;">
                                    {{ $user->department->name }}
                                </span>
                            </div>
                        </div>
                    </button>
                </h2>
                
                <div id="collapse{{ $user->id }}" class="accordion-collapse collapse" data-bs-parent="#accordionUsers">
                    <div class="accordion-body bg-light">
                        <div class="row">
                            <div class="col-md-4 border-end">
                                <h6 class="fw-bold text-primary">Datos de Contacto</h6>
                                <p class="mb-1"><i class="fa-solid fa-envelope me-2 text-muted"></i> {{ $user->email }}</p>
                                <p class="mb-1"><i class="fa-solid fa-phone me-2 text-muted"></i> {{ $user->phone ?? 'Sin teléfono' }}</p>
                                <p class="mb-1"><i class="fa-solid fa-location-dot me-2 text-muted"></i> {{ $user->address ?? 'Sin dirección' }}</p>
                            </div>

                            <div class="col-md-4 border-end">
                                <h6 class="fw-bold text-primary">Mesas Asignadas</h6>
                                @if($user->mesas->count() > 0)
                                    <ul class="list-group list-group-flush">
                                        @foreach($user->mesas as $mesa)
                                            <li class="list-group-item bg-transparent py-1">
                                                <i class="fa-solid fa-box-archive text-success me-2"></i>
                                                Mesa N° {{ $mesa->number }}
                                                <small class="text-muted">({{ $mesa->school->name }})</small>
                                            </li>
                                            
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-muted fst-italic small">No tiene mesas asignadas actualmente.</p>
                                @endif
                            </div>

                            <div class="col-md-4 d-flex flex-column justify-content-center gap-2">
                                <h6 class="fw-bold text-primary">Acciones</h6>

                                {{-- LÓGICA DE PAPELERA VS ACTIVOS --}}
                                @if(request('view_deleted'))
                                    
                                    {{-- CASO 1: ESTAMOS EN LA PAPELERA (Solo botón Restaurar) --}}
                                    <form action="{{ route('users.restore', $user->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success w-100 text-start text-white">
                                            <i class="fa-solid fa-trash-arrow-up me-2"></i> Restaurar Usuario
                                        </button>
                                    </form>

                                @else

                                    {{-- CASO 2: USUARIO ACTIVO (Botones normales) --}}
                                    
                                    <a href="{{ route('mesas.index') }}" class="btn btn-sm btn-outline-primary text-start">
                                        <i class="fa-solid fa-check-to-slot me-2"></i> Ir a Asignar Mesas
                                    </a>

                                    <form action="{{ route('users.update', $user->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        
                                        @if($user->role === 'user')
                                            <input type="hidden" name="role" value="admin">
                                            <button type="submit" class="btn btn-sm btn-warning w-100 text-start">
                                                <i class="fa-solid fa-crown me-2"></i> Ascender a Admin
                                            </button>
                                        @else
                                            @if(Auth::id() !== $user->id)
                                                <input type="hidden" name="role" value="user">
                                                <button type="submit" class="btn btn-sm btn-outline-dark w-100 text-start">
                                                    <i class="fa-solid fa-user-shield me-2"></i> Volver a Fiscal
                                                </button>
                                            @endif
                                        @endif
                                    </form>

                                    @if(Auth::id() !== $user->id)
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar a este usuario? Se perderán sus asignaciones.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger w-100 text-start">
                                                <i class="fa-solid fa-trash me-2"></i> Eliminar Usuario
                                            </button>
                                        </form>
                                    @endif

                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
        @empty
            <div class="alert alert-info">No hay usuarios registrados.</div>
        @endforelse
    </div>
</div>
@endsection