<?php

namespace App\Livewire\Correos;

use App\Models\Campana;
use App\Models\PlantillaCorreo;
use App\Services\CorreoService;
use Livewire\Component;

class EnvioMasivo extends Component
{
    public Campana $campana;
    public string $plantilla_id = '';
    public bool $confirmar = false;
    public ?array $resultado = null;

    public function mount(Campana $campana): void
    {
        $this->campana = $campana;
    }

    public function preparar(): void
    {
        $this->validate([
            'plantilla_id' => 'required|exists:plantillas_correo,id',
        ], ['plantilla_id.required' => 'Selecciona una plantilla.']);

        $this->confirmar = true;
    }

    public function cancelar(): void
    {
        $this->confirmar  = false;
        $this->resultado  = null;
    }

    public function enviarTodos(): void
    {
        try {
            $plantilla = PlantillaCorreo::findOrFail($this->plantilla_id);
            [$enviados, $omitidos] = app(CorreoService::class)->programarEnvioMasivo($this->campana, $plantilla);

            $this->resultado  = compact('enviados', 'omitidos');
            $this->confirmar  = false;
            $this->plantilla_id = '';
        } catch (\Throwable $e) {
            $this->addError('plantilla_id', $e->getMessage());
            $this->confirmar = false;
        }
    }

    public function render()
    {
        $sinCorreo = $this->campana->clientes()->activos()->whereNull('correo')->count();
        $conCorreo = $this->campana->clientes()->activos()->whereNotNull('correo')->count();

        return view('livewire.correos.envio-masivo', [
            'plantillas' => PlantillaCorreo::where('activo', true)->orderBy('nombre')->get(),
            'sinCorreo'  => $sinCorreo,
            'conCorreo'  => $conCorreo,
        ]);
    }
}
