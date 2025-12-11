<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mesa;
use App\Models\School;
use App\Models\User;
use App\Models\Department;


class MesaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // 1. Traemos los departamentos para el select del filtro (solo Admin lo usa, pero no daña traerlo)
        $departments = Department::all();

        // 2. Iniciamos la consulta base cargando las relaciones necesarias
        // 'school.department' es vital para mostrar el nombre del depto en la tarjeta o filtro
        $query = Mesa::with(['school.department', 'fiscal']);

        // 3. Lógica según el Rol
        if ($user->role === 'admin') {
            // Si es Admin, revisamos si mandó el filtro por URL (?department_id=5)
            if ($request->filled('department_id')) {
                // Filtramos las mesas cuya escuela pertenezca a ese departamento
                $query->whereHas('school', function($q) use ($request) {
                    $q->where('department_id', $request->department_id);
                });
            }
        } else {
            // Si es Fiscal, forzamos que solo vea sus mesas
            $query->where('user_id', $user->id);
        }

        // 4. Ordenamos y ejecutamos la consulta final
        $mesas = $query->orderBy('number', 'asc')->get();

        // 5. Enviamos 'mesas' Y 'departments' a la vista
        return view('mesas.index', compact('mesas', 'departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Necesitamos la lista de escuelas para el <select>
        $schools = School::all();
        return view('mesas.create', compact('schools'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'number' => 'required|integer|unique:mesas,number',
            'school_id' => 'required|exists:schools,id',
        ]);

        // Por defecto nace con status 'created' (definido en la migración)
        $mesa = Mesa::create($validated);

        return response()->json([
            'message' => 'Mesa creada exitosamente',
            'mesa' => $mesa
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Cargamos la mesa con sus resultados y partidos políticos asociados
        $mesa = Mesa::with(['school', 'fiscal', 'results.politicalParty'])->findOrFail($id);
        
        return view('mesas.show', compact('mesa'));
    }

    /**
     * Show the form for editing the specified resource.
     */
  public function edit(string $id)
    {
        $mesa = Mesa::findOrFail($id);
        $user = auth()->user();

        // SEGURIDAD: Si es fiscal, solo puede editar SUS mesas
        if ($user->role !== 'admin' && $mesa->user_id !== $user->id) {
            abort(403, 'No tienes permiso para editar esta mesa.');
        }

        $schools = School::all();
        
        // LOGICA DE FISCALES DISPONIBLES
        if ($user->role === 'admin') {
            // Admin ve a todos
            $fiscals = User::where('role', 'user')->get();
        } else {
            // Fiscal ve solo a compañeros de SU departamento
            $fiscals = User::where('role', 'user')
                        ->where('department_id', $user->department_id)
                        ->get();
        }

        return view('mesas.edit', compact('mesa', 'schools', 'fiscals'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
{
    $mesa = Mesa::findOrFail($id);
    $user = auth()->user();

    // 1. SEGURIDAD: Solo Admin o el Dueño pueden entrar
    if ($user->role !== 'admin' && $mesa->user_id !== $user->id) {
        return response()->json(['message' => 'No tienes permiso para editar esta mesa.'], 403);
    }

    // 2. REGLAS: Quitamos 'status' de aquí. Nadie puede enviarlo.
    $rules = [
        'user_id' => 'nullable|exists:users,id',
    ];

    if ($user->role === 'admin') {
        $rules['number'] = 'required|integer|unique:mesas,number,' . $mesa->id;
        $rules['school_id'] = 'required|exists:schools,id';
    }

    $validated = $request->validate($rules);

    // 3. PREPARAR DATOS (Solo lo que se validó)
    // El Admin puede actualizar todo (number, school, user_id)
    // El Fiscal solo actualizará user_id (porque las otras reglas no corrieron para él)
    $dataToUpdate = $validated;

    // 4. AUTOMATIZACIÓN DE ESTADO (La única forma de cambiar estado aquí)
    // Si se asigna un fiscal (y no es nulo) y la mesa estaba virgen...
    if ($request->filled('user_id') && $mesa->status === 'created') {
        $dataToUpdate['status'] = 'asigned';
    }

    // 5. GUARDAR
    $mesa->update($dataToUpdate);

    return response()->json([
        'message' => 'Mesa actualizada correctamente',
        'mesa' => $mesa
    ], 200);
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $mesa = Mesa::findOrFail($id);
        $mesa->delete();

        return response()->json(['message' => 'Mesa eliminada'], 200);
    }

    /**
     * METODO ESPECIAL: Subir Foto del Acta
     * Se llamará desde Vue con FormData
     */
    public function uploadActa(Request $request, string $id)
    {
        $mesa = Mesa::findOrFail($id);

        // Validación de imagen (JPG/PNG, máx 10MB para cámaras modernas)
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:10240', 
        ]);

        // 1. Guardar archivo en disco 'public' dentro de la carpeta 'actas'
        // Esto devuelve el path relativo: "actas/nombre_archivo.jpg"
        $path = $request->file('image')->store('actas', 'public');

        // 2. Actualizar base de datos
        $mesa->update([
         'image_path' => $path,
         'status' => 'scrutinized'
            ]);

        return response()->json([
            'message' => 'Acta subida correctamente',
            'path' => $path
        ], 200);
    }


    // --- CREACIÓN MASIVA ---

    public function batchCreate()
    {
        // Necesitamos los departamentos para el primer select
        $departments = \App\Models\Department::all();
        return view('mesas.batch_create', compact('departments'));
    }

    public function batchStore(Request $request)
    {
        $request->validate([
            'school_id' => 'required|exists:schools,id',
            'from' => 'required|integer|min:1',
            'to' => 'required|integer|gt:from', // 'to' debe ser mayor que 'from'
        ]);

        $schoolId = $request->school_id;
        $from = (int)$request->from;
        $to = (int)$request->to;

        $createdCount = 0;
        $errors = [];

        // Bucle mágico
        for ($i = $from; $i <= $to; $i++) {
            // Verificamos si ya existe para no romper todo
            $exists = Mesa::where('number', $i)->exists();

            if (!$exists) {
                Mesa::create([
                    'number' => $i,
                    'school_id' => $schoolId,
                    'status' => 'created'
                ]);
                $createdCount++;
            } else {
                $errors[] = $i; // Guardamos cuáles no se pudieron crear
            }
        }

        $message = "Se crearon $createdCount mesas exitosamente.";
        if (count($errors) > 0) {
            $message .= " (Las mesas " . implode(', ', $errors) . " ya existían y se omitieron).";
        }

        return response()->json(['message' => $message], 200);
    }
    
    // Y necesitamos una API pequeña para obtener escuelas por departamento
    public function getSchoolsByDepartment($departmentId)
    {
        $schools = \App\Models\School::where('department_id', $departmentId)->get();
        return response()->json($schools);
    }



    // --- ASIGNACIÓN MASIVA ---

    public function batchAssign()
    {
        $user = auth()->user();
        
        // Iniciamos la consulta de fiscales
        $query = \App\Models\User::where('role', 'user');

        // Si NO es admin (es Fiscal), aplicamos el filtro de departamento
        if ($user->role !== 'admin') {
            // "Traer fiscales cuyo departamento sea igual al mío, pero que no sea yo mismo"
            $query->where('department_id', $user->department_id)
                  ->where('id', '!=', $user->id);
        }

        $fiscals = $query->get();

        return view('mesas.batch_assign', compact('fiscals'));
    }

    public function batchAssignStore(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'from' => 'required|integer|min:1',
            'to' => 'required|integer|gt:from',
        ]);

        $targetUserId = $request->user_id; // El fiscal DESTINO
        $currentUser = auth()->user();     // El fiscal ACTUAL (Logged in)
        $from = (int)$request->from;
        $to = (int)$request->to;

        // --- VALIDACIÓN DE SEGURIDAD PARA FISCALES ---
        if ($currentUser->role !== 'admin') {
            // 1. Verificar que el destino sea del mismo departamento
            $targetUser = \App\Models\User::find($targetUserId);
            if ($targetUser->department_id !== $currentUser->department_id) {
                return response()->json(['message' => 'No puedes asignar mesas a fiscales de otro departamento.'], 403);
            }
        }

        // --- CONSULTA BASE DE MESAS ---
        $query = Mesa::whereBetween('number', [$from, $to])
                     ->where('status', '!=', 'scrutinized');

        // --- RESTRICCIÓN DE PROPIEDAD ---
        if ($currentUser->role !== 'admin') {
            // El fiscal solo puede reasignar mesas que:
            // A) Sean suyas actualmente (user_id == current)
            // B) O estén vacías (user_id == null) dentro de su zona (si quisieras permitir eso)
            // Por seguridad, generalmente solo dejamos que reasigne LO SUYO:
            $query->where('user_id', $currentUser->id);
        }

        // Ejecutar la actualización
        $affected = $query->update([
            'user_id' => $targetUserId,
            'status' => 'asigned'
        ]);

        if ($affected === 0) {
            $msg = ($currentUser->role === 'admin') 
                ? 'No se encontraron mesas disponibles en ese rango.' 
                : 'No tienes mesas asignadas en ese rango para transferir.';
            return response()->json(['message' => $msg], 422);
        }

        return response()->json([
            'message' => "Se transfirieron $affected mesas correctamente."
        ], 200);
    }

}
