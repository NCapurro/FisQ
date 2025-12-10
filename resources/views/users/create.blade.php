@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-user-plus me-2"></i>Alta de Nuevo Usuario</h5>
                </div>
                
                <div class="card-body">
                    <form id="createUserForm"> 
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
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Departamento</label>
                                <select name="department_id" class="form-select" required>
                                    <option value="">Seleccione...</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Rol de Usuario</label>
                                <select name="role" class="form-select" required>
                                    <option value="user">Fiscal (Usuario Básico)</option>
                                    <option value="admin">Administrador</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="password" class="form-control" required minlength="6">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Teléfono (Opcional)</label>
                                <input type="text" name="phone" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Dirección (Opcional)</label>
                                <input type="text" name="address" class="form-control">
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="button" onclick="crearUsuario()" class="btn btn-success">
                                <i class="fa-solid fa-save me-2"></i> Guardar Usuario
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function crearUsuario() {
        const form = document.getElementById('createUserForm');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        // Enviamos a la ruta de la API /users
        axios.post('/users', data)
            .then(response => {
                alert('Usuario creado con éxito');
                window.location.href = '/users';
            })
            .catch(error => {
                console.error(error);
                if(error.response.status === 422) {
                    alert('Error de validación: Revisa que el DNI o Email no estén repetidos.');
                } else {
                    alert('Error al crear usuario. Revisa la consola.');
                }
            });
    }
</script>
@endsection