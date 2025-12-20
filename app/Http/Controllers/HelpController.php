<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HelpController extends Controller
{
    public function index()
    {
        // Solo necesitamos saber el rol para mostrar u ocultar secciones en la vista
        $role = auth()->user()->role;

        return view('help.index', compact('role'));
    }
}