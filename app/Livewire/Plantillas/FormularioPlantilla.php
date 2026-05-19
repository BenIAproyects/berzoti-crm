<?php

namespace App\Livewire\Plantillas;

use App\Models\PlantillaCorreo;
use Livewire\Attributes\Computed;
use Livewire\Component;

class FormularioPlantilla extends Component
{
    public ?PlantillaCorreo $plantilla = null;
    public bool $modoEdicion = false;
    public bool $mostrarPreview = false;

    public string $nombre = '';
    public string $asunto = '';
    public string $cuerpo_html = '';
    public bool $activo = true;

    // Variables disponibles para insertar
    public array $variables = [
        '{{contacto_principal}}',
        '{{razon_social}}',
        '{{nombre_comercial}}',
        '{{tipo_cliente}}',
        '{{vendedor_nombre}}',
    ];

    public function mount(?PlantillaCorreo $plantilla = null): void
    {
        if ($plantilla && $plantilla->exists) {
            $this->plantilla   = $plantilla;
            $this->modoEdicion = true;
            $this->nombre      = $plantilla->nombre;
            $this->asunto      = $plantilla->asunto;
            $this->cuerpo_html = $plantilla->cuerpo_html;
            $this->activo      = $plantilla->activo;
        }
    }

    protected function rules(): array
    {
        return [
            'nombre'     => 'required|string|max:255',
            'asunto'     => 'required|string|max:255',
            'cuerpo_html' => 'required|string',
            'activo'     => 'boolean',
        ];
    }

    protected function messages(): array
    {
        return [
            'nombre.required'     => 'El nombre de la plantilla es obligatorio.',
            'asunto.required'     => 'El asunto es obligatorio.',
            'cuerpo_html.required' => 'El cuerpo del correo es obligatorio.',
        ];
    }

    public function guardar(): void
    {
        $datos = $this->validate();
        $datos['created_by'] = $this->modoEdicion ? $this->plantilla->created_by : auth()->id();

        if ($this->modoEdicion) {
            $this->plantilla->update($datos);
            session()->flash('success', 'Plantilla actualizada correctamente.');
        } else {
            PlantillaCorreo::create($datos);
            session()->flash('success', 'Plantilla creada correctamente.');
        }

        $this->redirectRoute('plantillas.index');
    }

    public function togglePreview(): void
    {
        $this->mostrarPreview = !$this->mostrarPreview;
    }

    #[Computed]
    public function previewHtml(): string
    {
        $preview = str_replace(
            ['{{contacto_principal}}', '{{razon_social}}', '{{nombre_comercial}}', '{{tipo_cliente}}', '{{vendedor_nombre}}'],
            ['[Nombre del contacto]', '[Razón Social S.A.C.]', '[Nombre Comercial]', '[Corporación]', '[Vendedor]'],
            $this->cuerpo_html
        );
        return $preview;
    }

    public function render()
    {
        return view('livewire.plantillas.formulario-plantilla');
    }
}
