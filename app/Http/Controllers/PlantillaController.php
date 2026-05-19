<?php

namespace App\Http\Controllers;

use App\Models\PlantillaCorreo;
use Illuminate\Http\Request;

class PlantillaController extends Controller
{
    public function index()
    {
        return view('plantillas.index');
    }

    public function create()
    {
        return view('plantillas.create');
    }

    public function store(Request $request) { }

    public function show(PlantillaCorreo $plantilla)
    {
        return view('plantillas.show', compact('plantilla'));
    }

    public function edit(PlantillaCorreo $plantilla)
    {
        return view('plantillas.edit', compact('plantilla'));
    }

    public function update(Request $request, PlantillaCorreo $plantilla) { }

    public function destroy(PlantillaCorreo $plantilla) { }
}
