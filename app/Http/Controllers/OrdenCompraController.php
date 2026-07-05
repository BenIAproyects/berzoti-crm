<?php

namespace App\Http\Controllers;

class OrdenCompraController extends Controller
{
    public function index()
    {
        return view('ordenes-compra.index');
    }
}
