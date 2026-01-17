@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fa-solid fa-circle-question me-2 text-primary"></i>Manual de Ayuda y Documentación</h2>
            <p class="text-muted">Guía completa para el uso del Sistema de Gestión Electoral.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="list-group sticky-top" style="top: 20px;" id="list-tab" role="tablist">
                
                <a class="list-group-item list-group-item-action active" id="list-intro-list" data-bs-toggle="list" href="#list-intro" role="tab">
                    <i class="fa-solid fa-home me-2"></i> Introducción
                </a>

                {{-- MÓDULO MESAS (Todos) --}}
                <a class="list-group-item list-group-item-action" id="list-mesas-list" data-bs-toggle="list" href="#list-mesas" role="tab">
                    <i class="fa-solid fa-check-to-slot me-2"></i> Gestión de Mesas
                </a>

                {{-- MÓDULO RESULTADOS (Todos) --}}
                <a class="list-group-item list-group-item-action" id="list-resultados-list" data-bs-toggle="list" href="#list-resultados" role="tab">
                    <i class="fa-solid fa-pen-to-square me-2"></i> Carga de Votos
                </a>

                {{-- MÓDULOS SOLO ADMIN --}}
                @if($role === 'admin')
                    <a class="list-group-item list-group-item-action" id="list-users-list" data-bs-toggle="list" href="#list-users" role="tab">
                        <i class="fa-solid fa-users-gear me-2"></i> Usuarios y Roles
                    </a>
                    
                    <a class="list-group-item list-group-item-action" id="list-schools-list" data-bs-toggle="list" href="#list-schools" role="tab">
                        <i class="fa-solid fa-school me-2"></i> Escuelas
                    </a>

                    <a class="list-group-item list-group-item-action" id="list-partys-list" data-bs-toggle="list" href="#list-partys" role="tab">
                        <i class="fa-solid fa-flag me-2"></i> Partidos Políticos 
                    </a>

                    <a class="list-group-item list-group-item-action" id="list-maps-list" data-bs-toggle="list" href="#list-maps" role="tab">
                        <i class="fa-solid fa-map me-2"></i> Mapa Electoral
                    </a>

                    <a class="list-group-item list-group-item-action" id="list-audit-list" data-bs-toggle="list" href="#list-audit" role="tab">
                        <i class="fa-solid fa-eye me-2"></i> Auditoría (Logs)
                    </a>
                @endif
            </div>
        </div>

        <div class="col-md-9">
            <div class="tab-content" id="nav-tabContent">
                
                <div class="tab-pane fade show active" id="list-intro" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h4 class="text-primary mb-3">Bienvenido al Sistema</h4>
                            <p>Esta plataforma permite gestionar el proceso de carga y cómputo de mesas electorales de manera digital, segura y auditable.</p>
                            
                            <div class="alert alert-info">
                                <i class="fa-solid fa-info-circle me-2"></i>
                                <strong>Tu Rol actual es: {{ strtoupper($role) }}</strong>. 
                                @if($role === 'admin')
                                    Tienes acceso total al sistema.
                                @else
                                    Tienes acceso restringido a las mesas que se te han asignado.
                                @endif
                            </div>

                            <h5>Conceptos Básicos</h5>
                            <ul>
                                <li><strong>Mesas:</strong> La unidad mínima donde se registran los votos.</li>
                                <li><strong>Fiscales:</strong> Usuarios encargados de cargar los datos de una mesa.</li>
                                <li><strong>Escrutinio:</strong> El proceso de pasar una mesa de estado "Pendiente" a "Escrutada".</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="list-mesas" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h4 class="text-primary mb-3">Gestión de Mesas</h4>
                            <p>El panel de mesas es el corazón del sistema. Aquí verás el estado de cada urna.</p>

                            <h5>Estados de una Mesa</h5>
                            <div class="d-flex gap-2 mb-3">
                                <span class="badge bg-secondary">Pendiente</span>
                                <small>Nadie la ha tocado aún.</small>
                            </div>
                            <div class="d-flex gap-2 mb-3">
                                <span class="badge bg-warning text-dark">Asignada</span>
                                <small>Ya tiene un Fiscal responsable, pero no se han cargado datos.</small>
                            </div>
                            <div class="d-flex gap-2 mb-3">
                                <span class="badge bg-success">Escrutada</span>
                                <small>Los votos han sido cargados y guardados.</small>
                            </div>
                            @if ($role === 'user')
                            <hr>
                            <h5>Acciones Disponibles</h5>
                            <p>Como fiscal puedes:</p>
                            <ul>
                                <li><strong>Asignar Mesa:</strong> Apretando el botón  <i class="fa-solid fa-user-tag"></i> puedes reasignar la mesa a otro fiscal de su mismo departamento. </li>
                                <li><strong>Asignar Lote:</strong> Reasignar múltiples mesas a un fiscal rápidamente.</li>
                                <li><strong>Ver resultados de la mesa:</strong> Apretando el boton <i class="fa-solid fa-square-poll-vertical"></i> puede visualizar los resultados de la mesa. </li> 
                                <p>Nota: Las mesas "Escrutadas" no se pueden eliminar ni reasignar para garantizar la integridad de los datos.</p>
                                
                            </ul>
                            @endif
                            @if($role === 'admin')
                            <hr>
                            <h5>Funciones de Administrador</h5>
                            <p>Como administrador puedes:</p>
                            <ul>
                                <li><strong>Editar/Asignar Mesa:</strong> Apretando el botón  <i class="fa-solid fa-pen"></i> puede editar la mesa o asignarle un fiscal. </li>
                                <li><strong>Asignar Lote:</strong> Asignar múltiples mesas a un fiscal rápidamente.</li>
                                <li><strong>Creación Masiva:</strong> Generar mesas automáticas por rangos numéricos.</li>
                                <li><strong>Ver resultados de la mesa:</strong> Apretando el boton <i class="fa-solid fa-square-poll-vertical"></i> puede visualizar los resultados de la mesa. </li> 
                                <li><strong>Papelera:</strong> Restaurar mesas eliminadas accidentalmente.</li>
                                <p>Nota: Las mesas "Escrutadas" no se pueden eliminar ni reasignar para garantizar la integridad de los datos.</p>
                                
                            </ul>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="list-resultados" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h4 class="text-primary mb-3">Carga de Resultados</h4>
                            <p>Pasos para realizar la carga de una mesa:</p>
                            <ol>
                                <li>Dirígete al listado de <strong>Mesas</strong>.</li>
                                <li>Haz clic en el botón azul <strong><i class="fa-solid fa-pen-to-square"></i> Cargar</strong>.</li>
                                <li>Ingresa la cantidad de votos para cada partido político.</li>
                                <li>Sube una <strong>foto del telegrama</strong> o acta (Obligatorio/Opcional según config).</li>
                                <li>Presiona <strong>Guardar Resultados</strong>.</li>
                                <li>En caso de guardar resultados erroneos, se pueden modificar con el boton <strong>Modificar Carga</strong>.</li>
                                
                            </ol>
                            <div class="alert alert-warning">
                                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                                <strong>Importante:</strong> Una vez guardada, la mesa pasa a estado "Escrutada" y no se puede reasignar ni eliminar.
                            </div>
                        </div>
                    </div>
                </div>

                @if($role === 'admin')
                <div class="tab-pane fade" id="list-users" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h4 class="text-primary mb-3">Administración de Usuarios</h4>
                            <p>Gestione el personal con acceso al sistema.</p>
                            
                            <h5>Roles Disponibles</h5>
                            <ul>
                                <li><strong>Administrador:</strong> Control total. Puede crear/editar usuarios, escuelas, partidos, resultados y ver logs.</li>
                                <li><strong>Fiscal:</strong> Usuario operativo. Solo ve las mesas que se le asignan.</li>
                            </ul>

                            <h5>Acciones Clave</h5>
                            <ul>
                                <li><strong>Ascender/Degradar:</strong> Puede cambiar el rol de un usuario desde el botón en el listado.</li>
                                <li><strong>Baja Lógica:</strong> Al eliminar un usuario, este va a la papelera. Sus mesas asignadas quedan huérfanas.
                                En caso de restaurarse el usuario eliminado, debe volver a asignarse a la mesa.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="list-schools" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h4 class="text-primary mb-3">Escuelas y Departamentos</h4>
                            <p>Las escuelas agrupan las mesas. Es fundamental tener cargadas las escuelas antes de crear mesas.</p>
                            <p>Use el buscador del listado para encontrar rápidamente una institución por nombre.</p>
                        </div>
                    </div>
                </div>

                 <div class="tab-pane fade" id="list-partys" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h4 class="text-primary mb-3">Partidos Politicos</h4>
                            <p>Se pueden crear/editar/eliminar o recuperar partidos politicos para la elección.</p>
                            <p>Los partidos "Blanco", "Nulo" e "Impugnado" vienen por defecto.</p>
                            <p>Los votos de un partido en cada resultado de cada mesa se eliminan y se restauran en cascada junto con el partido.</p>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="list-maps" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h4 class="text-primary mb-3">Mapa Interactivo</h4>
                            <p>En el mapa se puede ver la localización de cada resultado cargado.</p>
                            <p>Apretando el pin <i class="fa-solid fa-location-dot me-1"></i> se puede acceder a los resultados de esa mesa. </p>
                            </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="list-audit" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h4 class="text-primary mb-3">Auditoría y Seguridad</h4>
                            <p>El sistema registra automáticamente todas las acciones sensibles.</p>
                            
                            <h5>¿Qué se registra?</h5>
                            <ul>
                                <li>Inicios de sesión.</li>
                                <li>Creación, Edición y Eliminación de cualquier dato (Mesa, Usuario, Escuela).</li>
                                <li>Cambios de asignación de fiscales.</li>
                            </ul>
                            <p>Estos registros son inalterables y sirven para garantizar la transparencia del comicio.</p>
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection