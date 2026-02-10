<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // ==========================================
    //  REGISTRO (Cualquiera puede entrar)
    // ==========================================

    /**
     * Muestra el formulario de registro.
     */
    public function showRegisterForm()
    {
        // Necesitamos los departamentos para que el usuario diga de dónde es
        $departments = Department::all();
        return view('auth.register', compact('departments'));
    }

    /**
     * Procesa el registro.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'dni' => 'required|string|size:8|unique:users,dni',
            'name' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email|max:50',
            'password' => 'required|string|min:6|confirmed', // 'confirmed' exige un campo password_confirmation
            'department_id' => 'required|exists:departments,id',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:100',
        ]);

        // 1. Encriptar contraseña
        $validated['password'] = Hash::make($validated['password']);

        // 2. Crear usuario
        // NOTA: No pasamos 'role'. La base de datos pondrá 'user' por defecto.
        $user = User::create($validated);

        // 3. Loguear automáticamente al usuario recién creado
        Auth::login($user);

        // 4. Redirigir al home o dashboard
        return redirect()->route('mesas.index'); 
    }

    // ==========================================
    //  LOGIN (Ingreso)
    // ==========================================

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Login exitoso
            return redirect()->intended('mesas');
        }

        // Login fallido
        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    // ==========================================
    //  LOGOUT (Salida)
    // ==========================================

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}