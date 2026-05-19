<?php

namespace App\Livewire\Clientes;

use App\Imports\ClientesImport;
use App\Models\ImportacionCliente;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class ImportarClientes extends Component
{
    use WithFileUploads;

    public $archivo = null;
    public string $paso = 'subir'; // subir | preview | resultado
    public array $preview = [];
    public ?array $resultado = null;
    public string $error = '';

    protected function rules(): array
    {
        return [
            'archivo' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ];
    }

    public function updatedArchivo(): void
    {
        $this->error = '';
        $this->validate(['archivo' => 'required|file|mimes:xlsx,xls,csv|max:5120']);
        $this->generarPreview();
    }

    private function generarPreview(): void
    {
        try {
            $data = Excel::toArray(new \App\Imports\PreviewImport(), $this->archivo->getRealPath());

            if (empty($data) || empty($data[0])) {
                $this->error = 'El archivo está vacío o no tiene el formato correcto.';
                return;
            }

            $filas = $data[0];
            $encabezados = array_map('strtolower', array_map('trim', $filas[0]));

            $requeridos = ['razon_social', 'contacto_principal'];
            foreach ($requeridos as $col) {
                if (!in_array($col, $encabezados)) {
                    $this->error = "El archivo debe tener la columna \"{$col}\".";
                    return;
                }
            }

            // Mostrar hasta 5 filas de preview (sin el encabezado)
            $this->preview = array_slice(array_filter(
                array_slice($filas, 1),
                fn($f) => !empty(array_filter($f, fn($v) => $v !== null && $v !== ''))
            ), 0, 5);

            $this->paso = 'preview';
        } catch (\Throwable $e) {
            $this->error = 'No se pudo leer el archivo. Asegúrate de usar el template correcto.';
        }
    }

    public function confirmarImportacion(): void
    {
        $this->validate();

        try {
            $importer = new ClientesImport();
            Excel::import($importer, $this->archivo->getRealPath());

            ImportacionCliente::create([
                'usuario_id'       => auth()->id(),
                'archivo'          => $this->archivo->getClientOriginalName(),
                'total_filas'      => $importer->totalFilas(),
                'total_importadas' => $importer->importadas,
                'total_duplicadas' => $importer->duplicadas,
                'total_error'      => $importer->errores,
                'estado'           => $importer->errores > 0 ? 'con_errores' : 'completado',
                'resultado_json'   => $importer->erroresDetalle,
            ]);

            $this->resultado = [
                'importadas' => $importer->importadas,
                'duplicadas' => $importer->duplicadas,
                'errores'    => $importer->errores,
                'detalle'    => $importer->erroresDetalle,
            ];

            $this->paso = 'resultado';
            $this->archivo = null;
        } catch (\Throwable $e) {
            $this->error = 'Error al procesar el archivo: ' . $e->getMessage();
            $this->paso = 'subir';
        }
    }

    public function reiniciar(): void
    {
        $this->reset(['archivo', 'preview', 'resultado', 'error']);
        $this->paso = 'subir';
    }

    public function render()
    {
        return view('livewire.clientes.importar-clientes', [
            'historial' => ImportacionCliente::with('usuario')
                ->orderByDesc('created_at')
                ->limit(10)
                ->get(),
        ]);
    }
}
