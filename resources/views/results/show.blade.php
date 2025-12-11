@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-square-poll-vertical me-2 text-primary"></i>Resultados Mesa N° {{ $mesa->number }}
            </h2>
            <p class="text-muted mb-0 ms-1">
                {{ $mesa->school->name }} | 
                <span class="badge bg-success">Escrutada</span>
            </p>
        </div>
        
        <div class="d-flex gap-2">
            <a href="{{ route('mesas.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Volver al Panel
            </a>
            
            @if(Auth::user()->role === 'admin' || Auth::user()->id === $mesa->user_id)
                <a href="{{ route('results.create', $mesa->id) }}" class="btn btn-primary">
                    <i class="fa-solid fa-pen-to-square me-1"></i> Modificar Carga
                </a>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold text-secondary">
                        <i class="fa-solid fa-list-ol me-2"></i>Conteo Oficial
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 text-secondary text-uppercase small">Agrupación Política</th>
                                    <th class="text-end pe-4 text-secondary text-uppercase small">Votos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $total = 0; @endphp
                                @forelse($mesa->results as $result)
                                    @php $total += $result->votes; @endphp
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle me-3 shadow-sm border" 
                                                     style="width: 30px; height: 30px; background-color: {{ $result->politicalParty->color_hex }};">
                                                </div>
                                                <div>
                                                    <span class="fw-bold d-block">{{ $result->politicalParty->name }}</span>
                                                    <small class="text-muted">{{ $result->politicalParty->abbreviation }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-4">
                                            <span class="fs-5 fw-bold font-monospace">{{ $result->votes }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center py-4 text-muted">
                                            No hay resultados cargados aún.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-light border-top">
                                <tr>
                                    <td class="ps-4 py-3 fw-bold text-uppercase text-primary">Total General</td>
                                    <td class="text-end pe-4 fw-bold fs-4 text-primary font-monospace">{{ $total }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white text-muted small py-2">
                    <i class="fa-solid fa-user-clock me-1"></i> 
                    Última actualización: {{ $mesa->results->first()->updated_at->format('d/m/Y H:i') }}
                    @if($mesa->results->isNotEmpty() && $mesa->results->first()->fiscal)
                        por <strong>{{ $mesa->results->first()->fiscal->name }} {{ $mesa->results->first()->fiscal->lastname }}</strong>
                    @elseif($mesa->fiscal)
                        (Asignado a: {{ $mesa->fiscal->name }} {{ $mesa->fiscal->lastname }})
                     @endif
                </div>
            </div>
        </div>

        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold text-secondary">
                        <i class="fa-solid fa-camera me-2"></i>Foto del Acta
                    </h5>
                </div>
                
                <div class="card-body p-0 d-flex align-items-center justify-content-center bg-dark" 
                     style="min-height: 400px; position: relative; overflow: hidden;">
                    
                    @if($mesa->image_path)
                        <a href="{{ Storage::url($mesa->image_path) }}" target="_blank" class="w-100 h-100 d-flex align-items-center justify-content-center text-decoration-none">
                            <img src="{{ Storage::url($mesa->image_path) }}" 
                                 class="img-fluid" 
                                 style="max-height: 600px; width: 100%; object-fit: contain;" 
                                 alt="Foto del Acta">
                            
                            <div class="position-absolute bottom-0 w-100 bg-black bg-opacity-50 text-white text-center py-2 small">
                                <i class="fa-solid fa-magnifying-glass-plus me-1"></i> Clic para ampliar
                            </div>
                        </a>
                    @else
                        <div class="text-center text-secondary opacity-50 p-4">
                            <i class="fa-regular fa-image fa-4x mb-3"></i>
                            <p class="mb-0">No se adjuntó foto del acta.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection