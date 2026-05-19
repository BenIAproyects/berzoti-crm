<?php

namespace App\Http\Controllers;

use App\Exports\ClientesExport;
use App\Exports\TemplateClientesExport;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ClienteController extends Controller
{
    public function index()
    {
        return view('clientes.index');
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Cliente $cliente)
    {
        return view('clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        //
    }

    public function destroy(Cliente $cliente)
    {
        //
    }

    public function importar()
    {
        return view('clientes.importar');
    }

    public function descargarTemplate()
    {
        return Excel::download(new TemplateClientesExport(), 'template_clientes.xlsx');
    }

    public function exportar(Request $request)
    {
        $export = new ClientesExport(
            busqueda:       $request->string('busqueda')->toString(),
            filtroEstado:   $request->string('filtroEstado')->toString(),
            filtroTipo:     $request->string('filtroTipo')->toString(),
            filtroVendedor: $request->string('filtroVendedor')->toString(),
        );

        $nombre = 'clientes_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download($export, $nombre);
    }
}
