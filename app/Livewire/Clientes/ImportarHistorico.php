<?php

namespace App\Livewire\Clientes;

use App\Models\ImportacionCliente;
use App\Services\HistoricoImportService;
use Livewire\Component;
use Livewire\WithFileUploads;

class ImportarHistorico extends Component
{
    use WithFileUploads;

    public $archivo = null;
    public string $paso = 'subir'; // subir | configurar | resultado

    // Opciones de importación
    public bool $importarClientes      = true;
    public bool $importarContactos     = true;
    public bool $importarCorreos       = true;
    public bool $importarTelefonos     = true;
    public bool $importarObservaciones = true;

    // Datos de preview
    public array $statsPreview = [];
    public string $archivoNombre = '';

    // Resultados
    public ?array $resultados = null;
    public string $error = '';

    public function updatedArchivo(): void
    {
        $this->error = '';
        $this->validate(['archivo' => 'required|file|mimes:xlsx,xls|max:20480']);
        $this->generarPreview();
    }

    private function generarPreview(): void
    {
        try {
            $service = new HistoricoImportService();
            $this->statsPreview  = $service->preview($this->archivo->getRealPath());
            $this->archivoNombre = $this->archivo->getClientOriginalName();
            $this->paso          = 'configurar';
        } catch (\Throwable $e) {
            $this->error = 'No se pudo leer el archivo. Asegúrate de subir el Excel histórico correcto. (' . $e->getMessage() . ')';
        }
    }

    public function ejecutarImportacion(): void
    {
        if (!$this->archivo) {
            $this->error = 'El archivo ya no está disponible. Vuelve a cargarlo.';
            $this->paso  = 'subir';
            return;
        }

        if (!$this->importarClientes && !$this->importarContactos &&
            !$this->importarCorreos && !$this->importarTelefonos && !$this->importarObservaciones) {
            $this->error = 'Selecciona al menos una hoja para importar.';
            return;
        }

        $this->error = '';

        try {
            $service = new HistoricoImportService();

            $this->resultados = $service->importar(
                $this->archivo->getRealPath(),
                [
                    'importar_clientes'      => $this->importarClientes,
                    'importar_contactos'     => $this->importarContactos,
                    'importar_correos'       => $this->importarCorreos,
                    'importar_telefonos'     => $this->importarTelefonos,
                    'importar_observaciones' => $this->importarObservaciones,
                ],
                auth()->id()
            );

            // Registrar en historial
            $resumenJson = array_map(
                fn($r) => array_diff_key($r, array_flip(['detalle'])),
                $this->resultados
            );

            $totalImportadas = collect($this->resultados)->sum('importadas');
            $totalErrores    = collect($this->resultados)->sum('errores');

            ImportacionCliente::create([
                'usuario_id'       => auth()->id(),
                'archivo'          => $this->archivoNombre,
                'total_filas'      => collect($this->resultados)->sum(fn($r) => ($r['importadas'] ?? 0) + ($r['duplicadas'] ?? $r['omitidas'] ?? 0) + ($r['errores'] ?? 0)),
                'total_importadas' => $totalImportadas,
                'total_duplicadas' => collect($this->resultados)->sum(fn($r) => $r['duplicadas'] ?? $r['omitidas'] ?? 0),
                'total_error'      => $totalErrores,
                'estado'           => $totalErrores > 0 ? 'con_errores' : 'completado',
                'resultado_json'   => $resumenJson,
            ]);

            $this->paso = 'resultado';
        } catch (\Throwable $e) {
            $this->error = 'Error durante la importación: ' . $e->getMessage();
        }
    }

    public function reiniciar(): void
    {
        $this->reset(['archivo', 'statsPreview', 'resultados', 'error', 'archivoNombre']);
        $this->importarClientes      = true;
        $this->importarContactos     = true;
        $this->importarCorreos       = true;
        $this->importarTelefonos     = true;
        $this->importarObservaciones = true;
        $this->paso = 'subir';
    }

    public function render()
    {
        return view('livewire.clientes.importar-historico', [
            'historial' => ImportacionCliente::with('usuario')
                ->orderByDesc('created_at')
                ->limit(5)
                ->get(),
        ]);
    }
}
