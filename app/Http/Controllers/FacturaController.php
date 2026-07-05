<?php

namespace App\Http\Controllers;

class FacturaController extends Controller
{
    public function index()
    {
        return view('facturas.index');
    }
}
