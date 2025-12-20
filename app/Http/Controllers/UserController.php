<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Department; // Necesario para el selector de zonas
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\ActivityLog;

class UserController extends Controller
{
    /**
     * Lista de usuarios. 
     * Muestra tabla con nombre, DNI y rol.
     */
    public function index(Request $request)
    {
        $departments = Department::all();

        // Ordenamos por Apellido para que sea una lista alfabética
        $query = User::with(['department', 'mesas'])
                     ->orderBy('lastname', 'asc');

        // 1. MODO PAPELERA
        if ($request->has('view_deleted')) {
            $query->onlyTrashed();
        }

        // 2. FILTRO POR DEPARTAMENTO
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // 3. FILTRO POR ROL (Nuevo)
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // 4. BUSCADOR (DNI, Nombre o Apellido)
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $term = $request->search;
                $q->where('dni', 'like', "%{$term}%")
                  ->orWhere('name', 'like', "%{$term}%")
                  ->orWhere('lastname', 'like', "%{$term}%");
            });
        }

        // 5. PAGINACIÓN
        $users = $query->paginate(15);

        return view('users.index', compact('users', 'departments'));
    }

    /**
     * Formulario de creación de Fiscal.
     */
    public function create()
    {
        // Necesitamos los departamentos para el <select>
        $departments = Department::all();
        
        return view('users.create', compact('departments'));
    }

    /**
     * Guardar nuevo Fiscal.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'dni' => 'required|string|size:8|unique:users,dni',
            'name' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email|max:50',
            'password' => 'required|string|min:6',
            'department_id' => 'required|exists:departments,id',
            'role' => 'required|in:admin,user',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:100',
        ]);

        // Encriptar contraseña antes de guardar
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return redirect()->route('users.index');
    }

    /**
     * Formulario de edición.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        $departments = Department::all();

        return view('users.edit', compact('user', 'departments'));
    }

    /**
     * Actualizar Fiscal.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'dni' => ['sometimes', 'size:8', Rule::unique('users')->ignore($user->id)],
            'name' => 'sometimes|string|max:50',
            'lastname' => 'sometimes|string|max:50',
            'email' => ['sometimes', 'email', Rule::unique('users')->ignore($user->id)],
            'department_id' => 'sometimes|exists:departments,id',
            'role' => 'sometimes|in:admin,user',
            'password' => 'nullable|string|min:6', // Opcional
        ]);

        // Solo hasheamos si enviaron una nueva contraseña
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']); // Evita guardar null o string vacío
        }

        $user->update($validated);

        return redirect()->route('users.index');
    }

    /**
     * Eliminar usuario.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete(); // Esto hará la baja lógica gracias al Trait


        return redirect()->route('users.index');
    }

    public function restore($id)
    {
        // Buscamos INCLUSO entre los eliminados
        $user = User::withoutGlobalScope('active')->findOrFail($id);
        
        $user->restore(); // Método del Trait

    
        return redirect()->route('users.index', ['view_deleted' => 1])
                         ->with('success', 'Usuario restaurado correctamente.');
    }


}