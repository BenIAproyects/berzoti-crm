<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TareaController extends Controller
{
    public function index() { return view('tareas.index'); }
    public function create() { return view('tareas.create'); }
    public function store(Request $request) { }
    public function show(string $id) { return view('tareas.show'); }
    public function edit(string $id) { return view('tareas.edit'); }
    public function update(Request $request, string $id) { }
    public function destroy(string $id) { }
}
