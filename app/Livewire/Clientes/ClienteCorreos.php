<?php

namespace App\Livewire\Clientes;

use App\Enums\EstadoCorreoCliente;
use App\Models\Cliente;
use App\Models\ClienteCorreo;
use Livewire\Component;

class ClienteCorreos extends Component
{
    public Cliente $cliente;

    public bool $mostrarFormulario = false;
    public ?int $editId = null;

    public string $email  = '';
    public string $nombre = '';
    public string $fuente = '';
    public string $estado = 'activo';
    public bool $es_principal = false;

    public function mount(Cliente $cliente): void
    {
        $this->cliente = $cliente;
    }

    protected function rules(): array
    {
        $uniqueRule = $this->editId
            ? 'unique:cliente_correos,email,' . $this->editId . ',id,cliente_id,' . $this->cliente->id
            : 'unique:cliente_correos,email,NULL,id,cliente_id,' . $this->cliente->id;

        return [
            'email'        => ['required', 'email', 'max:255', $uniqueRule],
            'nombre'       => 'nullable|string|max:150',
            'fuente'       => 'nullable|string|max:50',
            'estado'       => 'required|string',
            'es_principal' => 'boolean',
        ];
    }

    protected function messages(): array
    {
        return [
            'email.required' => 'El correo es obligatorio.',
            'email.email'    => 'El formato del correo no es válido.',
            'email.unique'   => 'Este correo ya está registrado para este cliente.',
        ];
    }

    public function nuevo(): void
    {
        $this->resetFormulario();
        $this->mostrarFormulario = true;
    }

    public function editar(int $id): void
    {
        $correo = ClienteCorreo::findOrFail($id);
        $this->editId       = $id;
        $this->email        = $correo->email;
        $this->nombre       = $correo->nombre ?? '';
        $this->fuente       = $correo->fuente ?? '';
        $this->estado       = $correo->estado->value;
        $this->es_principal = $correo->es_principal;
        $this->mostrarFormulario = true;
    }

    public function guardar(): void
    {
        $datos = $this->validate();

        if ($datos['es_principal']) {
            $this->cliente->correos()->update(['es_principal' => false]);
        }

        if ($this->editId) {
            ClienteCorreo::findOrFail($this->editId)->update($datos);
            session()->flash('success_correos', 'Correo actualizado.');
        } else {
            $this->cliente->correos()->create($datos);
            session()->flash('success_correos', 'Correo agregado.');
        }

        $this->resetFormulario();
    }

    public function eliminar(int $id): void
    {
        ClienteCorreo::findOrFail($id)->delete();
        session()->flash('success_correos', 'Correo eliminado.');
    }

    public function toggleEstado(int $id): void
    {
        $correo = ClienteCorreo::findOrFail($id);
        $correo->update([
            'estado' => $correo->estado === EstadoCorreoCliente::Activo
                ? EstadoCorreoCliente::Inactivo->value
                : EstadoCorreoCliente::Activo->value,
        ]);
    }

    public function cancelar(): void
    {
        $this->resetFormulario();
    }

    private function resetFormulario(): void
    {
        $this->editId       = null;
        $this->email        = '';
        $this->nombre       = '';
        $this->fuente       = '';
        $this->estado       = 'activo';
        $this->es_principal = false;
        $this->mostrarFormulario = false;
    }

    public function render()
    {
        return view('livewire.clientes.cliente-correos', [
            'correos'  => $this->cliente->correos()
                ->orderByDesc('es_principal')
                ->orderBy('email')
                ->get(),
            'estados'  => EstadoCorreoCliente::cases(),
        ]);
    }
}
