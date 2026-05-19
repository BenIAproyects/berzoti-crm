<?php

namespace App\Livewire\Plantillas;

use App\Models\PlantillaCorreo;
use Livewire\Component;
use Livewire\WithPagination;

class ListaPlantillas extends Component
{
    use WithPagination;

    public string $busqueda = '';

    public function updatingBusqueda(): void { $this->resetPage(); }

    public function toggleActivo(int $id): void
    {
        $plantilla = PlantillaCorreo::findOrFail($id);
        $plantilla->update(['activo' => !$plantilla->activo]);
    }

    public function duplicar(int $id): void
    {
        $original = PlantillaCorreo::findOrFail($id);
        PlantillaCorreo::create([
            'nombre'     => 'Copia de ' . $original->nombre,
            'asunto'     => $original->asunto,
            'cuerpo_html' => $original->cuerpo_html,
            'activo'     => false,
            'created_by' => auth()->id(),
        ]);
        session()->flash('success', 'Plantilla duplicada correctamente.');
    }

    public function eliminar(int $id): void
    {
        PlantillaCorreo::findOrFail($id)->delete();
        session()->flash('success', 'Plantilla eliminada.');
        $this->resetPage();
    }

    public function render()
    {
        $plantillas = PlantillaCorreo::with('creador')
            ->when($this->busqueda, fn($q) => $q->where('nombre', 'like', "%{$this->busqueda}%")
                ->orWhere('asunto', 'like', "%{$this->busqueda}%"))
            ->orderByDesc('updated_at')
            ->paginate(15);

        return view('livewire.plantillas.lista-plantillas', compact('plantillas'));
    }
}
