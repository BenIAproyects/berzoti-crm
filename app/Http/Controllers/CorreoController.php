<?php

namespace App\Http\Controllers;

use App\Models\CorreoEnviado;
use Illuminate\Http\Request;

class CorreoController extends Controller
{
    public function index()
    {
        return view('correos.index');
    }

    public function show(CorreoEnviado $correo)
    {
        return view('correos.show', compact('correo'));
    }
}
