@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fa-solid fa-users-gear me-2"></i>Gestión de Usuarios</h2>
        
        <div class="d-flex gap-2">
            <a href="{{ route('users.create') }}" class="btn btn-primary text-nowrap">
                <i class="fa-solid fa-user-plus me-1"></i> Nuevo Usuario
            </a>
            
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

    <div class="card mb-4 border-0 shadow-sm bg-light">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('users.index') }}" id="filterUserForm" class="row align-items-center gx-2">
                
                {{-- Mantener estado de papelera --}}
                @if(request('view_deleted'))
                    <input type="hidden" name="view_deleted" value="1">
                @endif

                <div class="col-auto">
                    <label class="fw-bold text-secondary m-0"><i class="fa-solid fa-filter me-1"></i> Filtros:</label>
                </div>

                <div class="col-md-3">
                    <select name="department_id" class="form-select form-select-sm" onchange="document.getElementById('filterUserForm').submit()">
                        <option value="">-- Todos los Deptos --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="role" class="form-select form-select-sm" onchange="document.getElementById('filterUserForm').submit()">
                        <option value="">-- Rol --</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrador</option>
                        <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>Fiscal</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Buscar por DNI o Apellido...">
                        <button type="submit" class="btn btn-secondary"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </div>
                </div>

                @if(request('department_id') || request('role') || request('search'))
                    <div class="col-auto">
                        <a href="{{ route('users.index', request('view_deleted') ? ['view_deleted' => 1] : []) }}" class="text-danger small text-decoration-none fw-bold">
                            <i class="fa-solid fa-times-circle"></i> Limpiar
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <div class="accordion shadow-sm" id="accordionUsers">
        @forelse($users as $user)
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading{{ $user->id }}">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $user->id }}">
                        <div class="d-flex w-100 justify-content-between align-items-center me-3">
                            <div>
                                <i class="fa-solid fa-user-circle me-2 text-secondary"></i>
                                <strong>{{ $user->lastname }}, {{ $user->name }}</strong>
                                <span class="text-muted small ms-2"><i class="fa-solid fa-id-card me-1"></i>{{ $user->dni }}</span>
                            </div>
                            
                            <div>
                                @if($user->role === 'admin')
                                    <span class="badge bg-danger me-1">Admin</span>
                                @else
                                    <span class="badge bg-info text-dark me-1">Fiscal</span>
                                @endif
                                
                                <span class="badge bg-secondary rounded-pill fw-normal">
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
                                <h6 class="fw-bold text-primary mb-3">Datos de Contacto</h6>
                                <p class="mb-1 text-muted"><i class="fa-solid fa-envelope me-2 w-20"></i> {{ $user->email }}</p>
                                <p class="mb-1 text-muted"><i class="fa-brands fa-whatsapp me-2 w-20"></i> {{ $user->phone ?? 'Sin teléfono' }}</p>
                                <p class="mb-1 text-muted"><i class="fa-solid fa-location-dot me-2 w-20"></i> {{ $user->address ?? 'Sin dirección' }}</p>
                            </div>

                            <div class="col-md-4 border-end">
                                <h6 class="fw-bold text-primary mb-3">
                                    Mesas Asignadas <span class="badge bg-secondary ms-1">{{ $user->mesas->count() }}</span>
                                </h6>
                                @if($user->mesas->count() > 0)
                                    <div style="max-height: 150px; overflow-y: auto;">
                                        <ul class="list-group list-group-flush small">
                                            @foreach($user->mesas as $mesa)
                                                <li class="list-group-item bg-transparent py-1 border-0">
                                                    <i class="fa-solid fa-check text-success me-2"></i> Mesa <strong>{{ $mesa->number }}</strong>
                                                    <span class="text-muted">- {{ Str::limit($mesa->school->name, 20) }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @else
                                    <div class="alert alert-secondary py-2 small mb-0">
                                        <i class="fa-solid fa-info-circle me-1"></i> Sin asignaciones.
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-4 d-flex flex-column justify-content-center gap-2 p-3">
                                
                                @if(request('view_deleted'))
                                    {{-- MODO PAPELERA --}}
                                    <form action="{{ route('users.restore', $user->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success w-100 text-white shadow-sm">
                                            <i class="fa-solid fa-trash-arrow-up me-2"></i> Restaurar Usuario
                                        </button>
                                    </form>
                                @else
                                    {{-- MODO NORMAL --}}
                                    
                                    {{-- Botón 1: Asignar --}}
                                    <a href="{{ route('mesas.batch_assign') }}" class="btn btn-sm btn-outline-primary text-start">
                                        <i class="fa-solid fa-check-to-slot me-2"></i> Ir a Asignar Mesas
                                    </a>

                                    {{-- Botón 2: Cambiar Rol --}}
                                    <form action="{{ route('users.update', $user->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        @if($user->role === 'user')
                                            <input type="hidden" name="role" value="admin">
                                            <button type="submit" class="btn btn-sm btn-warning w-100 text-start">
                                                <i class="fa-solid fa-crown me-2"></i> Ascender a Admin
                                            </button>
                                        @elseif(Auth::id() !== $user->id)
                                            <input type="hidden" name="role" value="user">
                                            <button type="submit" class="btn btn-sm btn-outline-dark w-100 text-start">
                                                <i class="fa-solid fa-user-shield me-2"></i> Degradar a Fiscal
                                            </button>
                                        @endif
                                    </form>

                                    {{-- Botón 3: Eliminar --}}
                                    @if(Auth::id() !== $user->id && $user->role !== 'admin')
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('¿Eliminar usuario?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger w-100 text-start">
                                                <i class="fa-solid fa-trash me-2"></i> Eliminar
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
            <div class="alert alert-info text-center m-0">
                <i class="fa-solid fa-magnifying-glass me-2"></i> No se encontraron usuarios con los filtros seleccionados.
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-end mt-4">
        {{ $users->withQueryString()->links() }}
    </div>
</div>
@endsection