<?php

namespace App\Livewire\Clientes;

use App\Enums\TipoTelefono;
use App\Models\Cliente;
use App\Models\ClienteTelefono;
use Livewire\Component;

class ClienteTelefonos extends Component
{
    public Cliente $cliente;

    public bool $mostrarFormulario = false;
    public ?int $editId = null;

    public string $numero = '';
    public string $nombre = '';
    public string $tipo   = 'celular';
    public bool $es_principal = false;

    public function mount(Cliente $cliente): void
    {
        $this->cliente = $cliente;
    }

    protected function rules(): array
    {
        $uniqueRule = $this->editId
            ? 'unique:cliente_telefonos,numero,' . $this->editId . ',id,cliente_id,' . $this->cliente->id
            : 'unique:cliente_telefonos,numero,NULL,id,cliente_id,' . $this->cliente->id;

        return [
            'numero'       => ['required', 'string', 'max:30', $uniqueRule],
            'nombre'       => 'nullable|string|max:150',
            'tipo'         => 'required|string',
            'es_principal' => 'boolean',
        ];
    }

    protected function messages(): array
    {
        return [
            'numero.required' => 'El número de teléfono es obligatorio.',
            'numero.unique'   => 'Este número ya está registrado para este cliente.',
        ];
    }

    public function nuevo(): void
    {
        $this->resetFormulario();
        $this->mostrarFormulario = true;
    }

    public function editar(int $id): void
    {
        $tel = ClienteTelefono::findOrFail($id);
        $this->editId       = $id;
        $this->numero       = $tel->numero;
        $this->nombre       = $tel->nombre ?? '';
        $this->tipo         = $tel->tipo->value;
        $this->es_principal = $tel->es_principal;
        $this->mostrarFormulario = true;
    }

    public function guardar(): void
    {
        $datos = $this->validate();

        if ($datos['es_principal']) {
            $this->cliente->telefonos()->update(['es_principal' => false]);
        }

        if ($this->editId) {
            ClienteTelefono::findOrFail($this->editId)->update($datos);
            session()->flash('success_telefonos', 'Teléfono actualizado.');
        } else {
            $this->cliente->telefonos()->create($datos);
            session()->flash('success_telefonos', 'Teléfono agregado.');
        }

        $this->resetFormulario();
    }

    public function eliminar(int $id): void
    {
        ClienteTelefono::findOrFail($id)->delete();
        session()->flash('success_telefonos', 'Teléfono eliminado.');
    }

    public function togglePrincipal(int $id): void
    {
        $this->cliente->telefonos()->update(['es_principal' => false]);
        ClienteTelefono::findOrFail($id)->update(['es_principal' => true]);
    }

    public function cancelar(): void
    {
        $this->resetFormulario();
    }

    private function resetFormulario(): void
    {
        $this->editId       = null;
        $this->numero       = '';
        $this->nombre       = '';
        $this->tipo         = 'celular';
        $this->es_principal = false;
        $this->mostrarFormulario = false;
    }

    public function render()
    {
        return view('livewire.clientes.cliente-telefonos', [
            'telefonos' => $this->cliente->telefonos()
                ->orderByDesc('es_principal')
                ->orderBy('numero')
                ->get(),
            'tipos' => TipoTelefono::cases(),
        ]);
    }
}
