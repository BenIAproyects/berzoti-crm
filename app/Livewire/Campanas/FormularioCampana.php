<?php

namespace App\Livewire\Campanas;

use App\Enums\EstadoCampana;
use App\Models\Campana;
use Livewire\Component;

class FormularioCampana extends Component
{
    public ?Campana $campana = null;
    public bool $modoEdicion = false;

    public string $nombre = '';
    public string $descripcion = '';
    public string $fecha_inicio = '';
    public string $fecha_fin = '';
    public string $estado = 'borrador';
    public string $objetivo_comercial = '';

    public function mount(?Campana $campana = null): void
    {
        if ($campana && $campana->exists) {
            $this->campana    = $campana;
            $this->modoEdicion = true;
            $this->nombre              = $campana->nombre;
            $this->descripcion         = $campana->descripcion ?? '';
            $this->fecha_inicio        = $campana->fecha_inicio?->format('Y-m-d') ?? '';
            $this->fecha_fin           = $campana->fecha_fin?->format('Y-m-d') ?? '';
            $this->estado              = $campana->estado->value;
            $this->objetivo_comercial  = $campana->objetivo_comercial ?? '';
        }
    }

    protected function rules(): array
    {
        return [
            'nombre'             => 'required|string|max:255',
            'descripcion'        => 'nullable|string',
            'fecha_inicio'       => 'nullable|date',
            'fecha_fin'          => 'nullable|date|after_or_equal:fecha_inicio',
            'estado'             => 'required|string',
            'objetivo_comercial' => 'nullable|string',
        ];
    }

    protected function messages(): array
    {
        return [
            'nombre.required'          => 'El nombre de la campaña es obligatorio.',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
        ];
    }

    public function guardar(): void
    {
        $datos = $this->validate();
        $datos['fecha_inicio'] = $datos['fecha_inicio'] ?: null;
        $datos['fecha_fin']    = $datos['fecha_fin'] ?: null;

        if ($this->modoEdicion) {
            $this->campana->update($datos);
            session()->flash('success', 'Campaña actualizada correctamente.');
            $this->redirectRoute('campanas.show', $this->campana);
        } else {
            $datos['created_by'] = auth()->id();
            $campana = Campana::create($datos);
            session()->flash('success', 'Campaña creada correctamente.');
            $this->redirectRoute('campanas.show', $campana);
        }
    }

    public function render()
    {
        return view('livewire.campanas.formulario-campana', [
            'estados' => EstadoCampana::cases(),
        ]);
    }
}
