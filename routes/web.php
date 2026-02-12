<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\PoliticalPartyController;
use App\Http\Controllers\MesaController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\GraphicController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\IncidentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Aquí registramos todas las rutas de la aplicación.
*/

// =========================================================================
// 0. RUTAS DE AUTENTICACIÓN (Password Reset)
// =========================================================================
// Desactivamos login/register automáticos para usar nuestro AuthController personalizado,
// pero dejamos activado 'reset' para que funcione la recuperación de contraseña.
Auth::routes([
    'login'    => false, 
    'logout'   => false, 
    'register' => false, 
    'reset'    => true, // <--- ESTO HABILITA "OLVIDÉ MI CONTRASEÑA"
    'verify'   => false,
]);

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



    // Vista de asignación individual
    Route::get('/mesas/{mesa}/asignar', [MesaController::class, 'assign'])->name('mesas.assign');
    
    // Guardar la asignación
    Route::put('/mesas/{mesa}/asignar', [MesaController::class, 'updateAssignment'])->name('mesas.update_assignment');


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
   
    // Por ahora el Controller rechaza si no es admin:
    Route::resource('users', UserController::class);

    Route::post('/users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');

   Route::get('/auditoria', [ActivityLogController::class, 'index'])
    ->name('logs.index')
    ->middleware('auth');

    // --- PÁGINA DE AYUDA ---
    Route::get('/ayuda', [HelpController::class, 'index'])->name('help.index');

    // Backups (Solo Admin se valida en el controller)
    Route::get('/backups', [BackupController::class, 'index'])->name('backups.index');
    Route::post('/backups/create', [BackupController::class, 'create'])->name('backups.create');
    Route::get('/backups/download/{file_name}', [BackupController::class, 'download'])->name('backups.download');
    Route::delete('/backups/delete/{file_name}', [BackupController::class, 'delete'])->name('backups.delete');


    Route::get('/reportes', [ReportController::class, 'index'])->name('reports.index');
    // Grupo de Reportes (Protegido por autenticación)
    Route::prefix('reportes')->name('reports.')->group(function () {
    // 1. Matriz de Resultados
    Route::get('/resultados', [ReportController::class, 'resultados'])->name('resultados');
    // 2. Mapa de Calor (Ganadores)
    Route::get('/mapa', [ReportController::class, 'mapa'])->name('mapa');
    // 3. Log de Incidentes
    Route::get('/incidentes', [ReportController::class, 'incidentes'])->name('incidentes');

    
                                                                    });
       

    Route::post('/resolver-incidencia-id/{incident}', [IncidentController::class, 'resolve'])
    ->name('incidents.mark_resolved');
      Route::resource('incidents', IncidentController::class);
    
    

});