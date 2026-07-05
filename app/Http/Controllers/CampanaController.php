<?php

namespace App\Http\Controllers;

use App\Models\Campana;
use Illuminate\Http\Request;

class CampanaController extends Controller
{
    public function index()
    {
        return view('campanas.index');
    }

    public function create()
    {
        return view('campanas.create');
    }

    public function store(Request $request) { }

    public function show(Campana $campana)
    {
        return view('campanas.show', compact('campana'));
    }

    public function edit(Campana $campana)
    {
        return view('campanas.edit', compact('campana'));
    }

    public function update(Request $request, Campana $campana) { }

    public function destroy(Campana $campana)
    {
        $campana->clientes()->detach();
        $campana->delete();
        return redirect()->route('campanas.index')->with('success', 'Campaña eliminada correctamente.');
    }
}
