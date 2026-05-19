<?php

namespace App\Livewire\Clientes;

use App\Enums\EstadoComercial;
use App\Enums\TipoCliente;
use App\Models\Cliente;
use App\Models\User;
use Livewire\Component;

class FormularioCliente extends Component
{
    public ?Cliente $cliente = null;
    public bool $modoEdicion = false;

    public string $razon_social = '';
    public string $nombre_comercial = '';
    public string $ruc = '';
    public string $tipo_cliente = 'otro';
    public string $sector = '';
    public string $contacto_principal = '';
    public string $cargo_contacto = '';
    public string $telefono = '';
    public string $whatsapp = '';
    public string $correo = '';
    public string $correo_secundario = '';
    public string $pais = 'Perú';
    public string $departamento = '';
    public string $provincia = '';
    public string $distrito = '';
    public string $direccion = '';
    public string $referencia = '';
    public string $vendedor_asignado_id = '';
    public string $estado_comercial = 'nuevo';
    public string $prioridad = 'media';
    public string $origen = '';
    public string $cantidad_compra = '';
    public string $mes_contacto = '';
    public string $precio_ano_anterior = '';
    public string $observaciones = '';
    public string $fecha_proximo_contacto = '';

    public function mount(?Cliente $cliente = null): void
    {
        if ($cliente && $cliente->exists) {
            $this->cliente = $cliente;
            $this->modoEdicion = true;
            $this->fill($cliente->only([
                'razon_social', 'nombre_comercial', 'ruc', 'sector',
                'contacto_principal', 'cargo_contacto', 'telefono', 'whatsapp',
                'correo', 'correo_secundario', 'pais', 'departamento', 'provincia',
                'distrito', 'direccion', 'referencia', 'prioridad', 'origen', 'observaciones',
            ]));
            $this->tipo_cliente = $cliente->tipo_cliente->value;
            $this->estado_comercial = $cliente->estado_comercial->value;
            $this->vendedor_asignado_id = (string) ($cliente->vendedor_asignado_id ?? '');
            $this->fecha_proximo_contacto = $cliente->fecha_proximo_contacto?->format('Y-m-d') ?? '';
            $this->cantidad_compra = $cliente->cantidad_compra !== null ? (string) $cliente->cantidad_compra : '';
            $this->mes_contacto = $cliente->mes_contacto ?? '';
            $this->precio_ano_anterior = $cliente->precio_ano_anterior !== null ? (string) $cliente->precio_ano_anterior : '';
        }
    }

    protected function rules(): array
    {
        $rulesRuc = $this->modoEdicion
            ? 'nullable|string|max:20|unique:clientes,ruc,' . $this->cliente->id
            : 'nullable|string|max:20|unique:clientes,ruc';

        return [
            'razon_social' => 'required|string|max:255',
            'nombre_comercial' => 'nullable|string|max:255',
            'ruc' => $rulesRuc,
            'tipo_cliente' => 'required|string',
            'sector' => 'nullable|string|max:100',
            'contacto_principal' => 'nullable|string|max:150',
            'cargo_contacto' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:30',
            'whatsapp' => 'nullable|string|max:30',
            'correo' => 'nullable|email|max:255',
            'correo_secundario' => 'nullable|email|max:255',
            'pais' => 'nullable|string|max:60',
            'departamento' => 'nullable|string|max:60',
            'provincia' => 'nullable|string|max:60',
            'distrito' => 'nullable|string|max:60',
            'direccion' => 'nullable|string|max:255',
            'referencia' => 'nullable|string|max:255',
            'vendedor_asignado_id' => 'nullable|exists:users,id',
            'estado_comercial' => 'required|string',
            'prioridad' => 'required|in:alta,media,baja',
            'origen' => 'nullable|string|max:60',
            'cantidad_compra' => 'nullable|integer|min:0',
            'mes_contacto' => 'nullable|string|max:20',
            'precio_ano_anterior' => 'nullable|numeric|min:0',
            'observaciones' => 'nullable|string',
            'fecha_proximo_contacto' => 'nullable|date',
        ];
    }

    protected function messages(): array
    {
        return [
            'razon_social.required' => 'La razón social es obligatoria.',
            'ruc.unique' => 'Este RUC ya está registrado.',
            'correo.email' => 'El correo no tiene un formato válido.',
        ];
    }

    public function guardar(): void
    {
        $datos = $this->validate();

        if (empty($datos['vendedor_asignado_id'])) {
            $datos['vendedor_asignado_id'] = null;
        }
        if (empty($datos['fecha_proximo_contacto'])) {
            $datos['fecha_proximo_contacto'] = null;
        }
        if ($datos['cantidad_compra'] === '' || $datos['cantidad_compra'] === null) {
            $datos['cantidad_compra'] = null;
        }
        if (empty($datos['precio_ano_anterior'])) {
            $datos['precio_ano_anterior'] = null;
        }

        if ($this->modoEdicion) {
            $this->cliente->update($datos);
            session()->flash('success', 'Cliente actualizado correctamente.');
            $this->redirectRoute('clientes.show', $this->cliente);
        } else {
            $cliente = Cliente::create($datos);
            session()->flash('success', 'Cliente creado correctamente.');
            $this->redirectRoute('clientes.show', $cliente);
        }
    }

    public function render()
    {
        return view('livewire.clientes.formulario-cliente', [
            'tipos' => TipoCliente::cases(),
            'estados' => EstadoComercial::cases(),
            'vendedores' => User::role('vendedor')->orderBy('name')->get(),
        ]);
    }
}
