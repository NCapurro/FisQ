<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Controlador de Reseteo de Password
    |--------------------------------------------------------------------------
    */

    use ResetsPasswords;

    // A dónde te lleva después de cambiar la clave exitosamente
    protected $redirectTo = '/dashboard';
}