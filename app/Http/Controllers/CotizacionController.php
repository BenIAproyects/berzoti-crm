<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;

class CotizacionController extends Controller
{
    public function index()
    {
        return view('cotizaciones.index');
    }

    public function imprimir(Cotizacion $cotizacion)
    {
        $cotizacion->load(['cliente', 'items', 'usuario', 'campana']);

        return view('cotizaciones.imprimir', compact('cotizacion'));
    }
}
