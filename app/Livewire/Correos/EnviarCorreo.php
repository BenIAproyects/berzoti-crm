<?php

namespace App\Livewire\Correos;

use App\Models\Campana;
use App\Models\Cliente;
use App\Models\PlantillaCorreo;
use App\Services\CorreoService;
use Livewire\Component;

class EnviarCorreo extends Component
{
    public Cliente $cliente;

    public string $plantilla_id = '';
    public string $campana_id = '';
    public bool $mostrarModal = false;
    public string $previewHtml = '';

    public function mount(Cliente $cliente): void
    {
        $this->cliente = $cliente;
    }

    public function updatedPlantillaId(): void
    {
        $this->generarPreview();
    }

    public function abrirModal(): void
    {
        $this->mostrarModal = true;
    }

    public function cerrarModal(): void
    {
        $this->mostrarModal = false;
        $this->reset(['plantilla_id', 'campana_id', 'previewHtml']);
    }

    private function generarPreview(): void
    {
        if (!$this->plantilla_id) {
            $this->previewHtml = '';
            return;
        }
        $plantilla = PlantillaCorreo::find($this->plantilla_id);
        $this->previewHtml = $plantilla?->renderizar($this->cliente) ?? '';
    }

    public function enviar(): void
    {
        $this->validate([
            'plantilla_id' => 'required|exists:plantillas_correo,id',
            'campana_id'   => 'nullable|exists:campanas,id',
        ], [
            'plantilla_id.required' => 'Selecciona una plantilla.',
        ]);

        if (!$this->cliente->correo) {
            $this->addError('plantilla_id', 'Este cliente no tiene correo registrado.');
            return;
        }

        try {
            $plantilla = PlantillaCorreo::findOrFail($this->plantilla_id);
            $campana   = $this->campana_id ? Campana::find($this->campana_id) : null;

            app(CorreoService::class)->programarEnvio($this->cliente, $plantilla, $campana);

            // Actualizar fecha último contacto
            $this->cliente->update(['fecha_ultimo_contacto' => today()]);

            $this->cerrarModal();
            session()->flash('success', 'Correo enviado a la cola correctamente.');
        } catch (\Throwable $e) {
            $this->addError('plantilla_id', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.correos.enviar-correo', [
            'plantillas' => PlantillaCorreo::where('activo', true)->orderBy('nombre')->get(),
            'campanas'   => Campana::activas()->orderBy('nombre')->get(),
        ]);
    }
}
