<?php

namespace App\Imports;

use App\Enums\EstadoComercial;
use App\Enums\TipoCliente;
use App\Models\Cliente;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;

class ClientesImport implements ToCollection, WithHeadingRow, SkipsOnFailure
{
    use SkipsFailures;

    public int $importadas = 0;
    public int $duplicadas = 0;
    public int $errores = 0;
    public array $erroresDetalle = [];

    private array $tiposValidos;
    private array $prioridadesValidas = ['alta', 'media', 'baja'];

    public function __construct()
    {
        $this->tiposValidos = array_column(TipoCliente::cases(), 'value');
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $fila = $index + 2; // +2 porque fila 1 es el encabezado

            $razonSocial  = trim($row['razon_social'] ?? '');
            $contacto     = trim($row['contacto_principal'] ?? '');

            if (empty($razonSocial) && empty(trim($row['nombre_comercial'] ?? ''))) {
                $this->errores++;
                $this->erroresDetalle[] = "Fila {$fila}: Debe tener razón social o nombre comercial.";
                continue;
            }

            $ruc    = trim($row['ruc'] ?? '');
            $correo = strtolower(trim($row['correo'] ?? ''));
            $nombreComercial = trim($row['nombre_comercial'] ?? '');

            if (empty($ruc) && empty($correo)) {
                $this->errores++;
                $this->erroresDetalle[] = "Fila {$fila}: Debe tener al menos RUC o correo para poder identificar al cliente ({$razonSocial}).";
                continue;
            }

            // Detección de duplicados
            if ($this->esDuplicado($ruc, $correo, $razonSocial, $contacto)) {
                $this->duplicadas++;
                $this->erroresDetalle[] = "Fila {$fila}: Duplicado omitido ({$razonSocial}).";
                continue;
            }

            $tipo = strtolower(trim($row['tipo_cliente'] ?? 'otro'));
            if (!in_array($tipo, $this->tiposValidos)) {
                $tipo = 'otro';
            }

            $prioridad = strtolower(trim($row['prioridad'] ?? 'media'));
            if (!in_array($prioridad, $this->prioridadesValidas)) {
                $prioridad = 'media';
            }

            try {
                Cliente::create([
                    'razon_social'       => $razonSocial ?: $nombreComercial,
                    'nombre_comercial'   => $nombreComercial ?: null,
                    'ruc'                => $ruc ?: null,
                    'tipo_cliente'       => $tipo,
                    'sector'             => trim($row['sector'] ?? '') ?: null,
                    'contacto_principal' => $contacto ?: null,
                    'cargo_contacto'     => trim($row['cargo_contacto'] ?? '') ?: null,
                    'telefono'           => trim($row['telefono'] ?? '') ?: null,
                    'whatsapp'           => trim($row['whatsapp'] ?? '') ?: null,
                    'correo'             => $correo ?: null,
                    'correo_secundario'  => strtolower(trim($row['correo_secundario'] ?? '')) ?: null,
                    'pais'               => trim($row['pais'] ?? 'Perú') ?: 'Perú',
                    'departamento'       => trim($row['departamento'] ?? '') ?: null,
                    'provincia'          => trim($row['provincia'] ?? '') ?: null,
                    'distrito'           => trim($row['distrito'] ?? '') ?: null,
                    'direccion'          => trim($row['direccion'] ?? '') ?: null,
                    'referencia'         => trim($row['referencia'] ?? '') ?: null,
                    'prioridad'           => $prioridad,
                    'origen'              => trim($row['origen'] ?? '') ?: null,
                    'cantidad_compra'     => is_numeric($row['cantidad_compra'] ?? null) ? (int) $row['cantidad_compra'] : null,
                    'mes_contacto'        => trim($row['mes_contacto'] ?? '') ?: null,
                    'precio_ano_anterior' => is_numeric($row['precio_ano_anterior'] ?? null) ? (float) $row['precio_ano_anterior'] : null,
                    'observaciones'       => trim($row['observaciones'] ?? '') ?: null,
                    'estado_comercial'    => EstadoComercial::Nuevo->value,
                    'activo'              => true,
                ]);

                $this->importadas++;
            } catch (\Throwable $e) {
                $this->errores++;
                $this->erroresDetalle[] = "Fila {$fila}: Error al guardar ({$e->getMessage()}).";
            }
        }
    }

    private function esDuplicado(string $ruc, string $correo, string $razonSocial, string $contacto): bool
    {
        if ($ruc && Cliente::where('ruc', $ruc)->exists()) {
            return true;
        }
        if ($correo && Cliente::where('correo', $correo)->exists()) {
            return true;
        }
        if ($razonSocial && $contacto &&
            Cliente::where('razon_social', $razonSocial)
                   ->where('contacto_principal', $contacto)
                   ->exists()) {
            return true;
        }
        return false;
    }

    public function totalFilas(): int
    {
        return $this->importadas + $this->duplicadas + $this->errores;
    }
}
