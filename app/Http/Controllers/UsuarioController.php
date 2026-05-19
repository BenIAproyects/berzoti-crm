<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index() { return view('usuarios.index'); }
    public function create() { return view('usuarios.create'); }
    public function store(Request $request) { }
    public function show(string $id) { return view('usuarios.show'); }
    public function edit(string $id) { return view('usuarios.edit'); }
    public function update(Request $request, string $id) { }
    public function destroy(string $id) { }
}
