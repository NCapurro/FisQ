<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Controlador de Envío de Email de Recuperación
    |--------------------------------------------------------------------------
    */

    use SendsPasswordResetEmails;
}