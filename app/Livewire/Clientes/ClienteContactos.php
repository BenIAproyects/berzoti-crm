<?php

namespace App\Livewire\Clientes;

use App\Models\Cliente;
use App\Models\ClienteContacto;
use App\Models\ClienteCorreo;
use App\Models\ClienteTelefono;
use Livewire\Component;

class ClienteContactos extends Component
{
    public Cliente $cliente;

    public bool $mostrarFormulario = false;
    public ?int $editId = null;

    public string $nombre_contacto = '';
    public string $cargo = '';
    public string $telefono = '';
    public string $correo = '';
    public string $observaciones = '';
    public bool $es_principal = false;

    public function mount(Cliente $cliente): void
    {
        $this->cliente = $cliente;
    }

    protected function rules(): array
    {
        return [
            'nombre_contacto' => 'required|string|max:150',
            'cargo'           => 'nullable|string|max:100',
            'telefono'        => 'nullable|string|max:30',
            'correo'          => 'nullable|email|max:255',
            'observaciones'   => 'nullable|string',
            'es_principal'    => 'boolean',
        ];
    }

    protected function messages(): array
    {
        return [
            'nombre_contacto.required' => 'El nombre del contacto es obligatorio.',
            'correo.email'             => 'El correo no tiene formato válido.',
        ];
    }

    public function nuevo(): void
    {
        $this->resetFormulario();
        $this->mostrarFormulario = true;
    }

    public function editar(int $id): void
    {
        $contacto = ClienteContacto::findOrFail($id);
        $this->editId          = $id;
        $this->nombre_contacto = $contacto->nombre_contacto;
        $this->cargo           = $contacto->cargo ?? '';
        $this->telefono        = $contacto->telefono ?? '';
        $this->correo          = $contacto->correo ?? '';
        $this->observaciones   = $contacto->observaciones ?? '';
        $this->es_principal    = $contacto->es_principal;
        $this->mostrarFormulario = true;
    }

    public function guardar(): void
    {
        $datos = $this->validate();

        if ($datos['es_principal']) {
            $this->cliente->contactos()->update(['es_principal' => false]);
        }

        if ($this->editId) {
            ClienteContacto::findOrFail($this->editId)->update($datos);
            $contactoId = $this->editId;
            session()->flash('success_contactos', 'Contacto actualizado.');
        } else {
            $contacto = $this->cliente->contactos()->create($datos);
            $contactoId = $contacto->id;
            session()->flash('success_contactos', 'Contacto agregado.');
        }

        $this->sincronizarCorreoTelefono($contactoId);
        $this->resetFormulario();
    }

    public function eliminar(int $id): void
    {
        ClienteCorreo::where('contacto_id', $id)->delete();
        ClienteTelefono::where('contacto_id', $id)->delete();
        ClienteContacto::findOrFail($id)->delete();
        session()->flash('success_contactos', 'Contacto eliminado.');
    }

    private function sincronizarCorreoTelefono(int $contactoId): void
    {
        // --- Correo ---
        $correoAnterior = ClienteCorreo::where('contacto_id', $contactoId)->first();

        if (!empty($this->correo)) {
            if ($correoAnterior && $correoAnterior->email !== $this->correo) {
                $correoAnterior->delete();
            }
            ClienteCorreo::updateOrCreate(
                ['cliente_id' => $this->cliente->id, 'email' => $this->correo],
                ['nombre' => $this->nombre_contacto ?: null, 'contacto_id' => $contactoId, 'estado' => 'activo']
            );
        } else {
            $correoAnterior?->delete();
        }

        // --- Teléfono ---
        $telAnterior = ClienteTelefono::where('contacto_id', $contactoId)->first();

        if (!empty($this->telefono)) {
            if ($telAnterior && $telAnterior->numero !== $this->telefono) {
                $telAnterior->delete();
            }
            ClienteTelefono::updateOrCreate(
                ['cliente_id' => $this->cliente->id, 'numero' => $this->telefono],
                ['nombre' => $this->nombre_contacto ?: null, 'tipo' => 'celular', 'contacto_id' => $contactoId]
            );
        } else {
            $telAnterior?->delete();
        }
    }

    public function togglePrincipal(int $id): void
    {
        $this->cliente->contactos()->update(['es_principal' => false]);
        ClienteContacto::findOrFail($id)->update(['es_principal' => true]);
    }

    public function cancelar(): void
    {
        $this->resetFormulario();
    }

    private function resetFormulario(): void
    {
        $this->editId          = null;
        $this->nombre_contacto = '';
        $this->cargo           = '';
        $this->telefono        = '';
        $this->correo          = '';
        $this->observaciones   = '';
        $this->es_principal    = false;
        $this->mostrarFormulario = false;
    }

    public function render()
    {
        return view('livewire.clientes.cliente-contactos', [
            'contactos' => $this->cliente->contactos()
                ->orderByDesc('es_principal')
                ->orderBy('nombre_contacto')
                ->get(),
        ]);
    }
}
