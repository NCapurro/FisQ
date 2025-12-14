<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\PoliticalPartyController;
use App\Http\Controllers\MesaController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\GraphicController;
use App\Http\Controllers\MapController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Aquí registramos todas las rutas de la aplicación.
*/

// =========================================================================
// 1. RUTAS PÚBLICAS (GUEST)
// Solo pueden entrar quienes NO están logueados.
// =========================================================================
Route::middleware('guest')->group(function () {
    
    // Redirección raíz: Si entras a fisq.com, te manda al login
    Route::get('/', function () {
        return redirect()->route('login');
    });

    // Login
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Registro Público (Cualquiera se crea su usuario Fiscal)
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
});

// =========================================================================
// 2. RUTAS PROTEGIDAS (AUTH)
// Solo usuarios logueados (Fiscales y Admins)
// =========================================================================
Route::middleware('auth')->group(function () {

    // Logout (Cerrar sesión)
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard principal (puede ser el listado de mesas)
    Route::get('/dashboard', function () {
        return redirect()->route('mesas.index');
    })->name('dashboard');

    // --- RECURSOS ESTÁNDAR (CRUDS) ---
    // Laravel crea automáticamente: index, create, store, show, edit, update, destroy
    
    Route::resource('schools', SchoolController::class);
    Route::resource('political-parties', PoliticalPartyController::class);
    Route::post('/political-parties/{id}/restore', [PoliticalPartyController::class, 'restore'])->name('political-parties.restore');
    

    //restaurar escuela
    Route::post('/schools/{id}/restore', [SchoolController::class, 'restore'])->name('schools.restore');

    // creacion masiva de mesas
    Route::get('/mesas/creacion-masiva', [MesaController::class, 'batchCreate'])->name('mesas.batch_create');
    Route::post('/mesas/creacion-masiva', [MesaController::class, 'batchStore'])->name('mesas.batch_store');
    Route::get('/api/schools/{department_id}', [MesaController::class, 'getSchoolsByDepartment']); // API interna
    Route::post('/mesas/{id}/restore', [MesaController::class, 'restore'])->name('mesas.restore');



    // Asignación Masiva
    Route::get('/mesas/asignacion-masiva', [MesaController::class, 'batchAssign'])->name('mesas.batch_assign');
    Route::post('/mesas/asignacion-masiva', [MesaController::class, 'batchAssignStore'])->name('mesas.batch_assign_store');


    // Mesas (CRUD estándar + Rutas personalizadas)
    Route::resource('mesas', MesaController::class);
    // Ruta especial para subir la foto del acta (Axios/Vue)
    Route::post('/mesas/{id}/upload-acta', [MesaController::class, 'uploadActa'])->name('mesas.upload_acta');

    // --- CARGA DE VOTOS (RESULT CONTROLLER) ---
    // Estas no son estándar, las definimos a mano
    Route::get('/mesas/{id}/cargar-votos', [ResultController::class, 'create'])->name('results.create'); // La Boleta
    Route::post('/mesas/{id}/cargar-votos', [ResultController::class, 'store'])->name('results.store');   // Guardar Escrutinio
    Route::get('/mesas/{id}/resultados', [ResultController::class, 'show'])->name('results.show');        // Ver Finales
  

    // --- GRÁFICOS DE RESULTADOS ---
    Route::get('/resultados-graficos', [GraphicController::class, 'index'])->name('graphics.index');

    // --- MAPA EN VIVO DE CARGAS ---
    Route::get('/mapa-en-vivo', [MapController::class, 'index'])->name('maps.index');

    // --- GESTIÓN DE USUARIOS (SOLO ADMIN) ---
    // Usamos un middleware extra o simplemente confiamos en el Controller que ya tiene validación
    // Para mayor seguridad visual, puedes envolverlo:
    /*
    Route::middleware('can:admin-access')->group(function () {
        Route::resource('users', UserController::class);
    });
    */
    // Por ahora, lo dejamos abierto y que el Controller rechace si no es admin:
    Route::resource('users', UserController::class);

    Route::post('/users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
});