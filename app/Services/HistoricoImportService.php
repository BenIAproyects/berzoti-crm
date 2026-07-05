<?php

namespace App\Services;

use App\Enums\EstadoComercial;
use App\Enums\TipoSeguimiento;
use App\Models\Cliente;
use App\Models\ClienteContacto;
use App\Models\ClienteCorreo;
use App\Models\ClienteTelefono;
use App\Models\Seguimiento;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class HistoricoImportService
{
    // Índices de hojas en el Excel (0-based)
    const HOJA_RESUMEN     = 0;
    const HOJA_CLIENTES    = 1;
    const HOJA_OBSERVACIONES = 2;
    const HOJA_CONTACTOS   = 3;
    const HOJA_CORREOS     = 4;
    const HOJA_TELEFONOS   = 5;

    // Umbral para marcar cantidad como sospechosa
    const UMBRAL_CANTIDAD_SOSPECHOSA = 99999;

    private array $data = [];
    private array $rucIndex = []; // ruc => cliente_id (construido durante importación)

    public array $resultados = [];

    public function preview(string $rutaArchivo): array
    {
        $this->data = Excel::toArray(new \App\Imports\PreviewImport(), $rutaArchivo);

        $preview = [];

        // MAESTRO CLIENTES
        if (isset($this->data[self::HOJA_CLIENTES])) {
            $filas = $this->filtrarFilasVacias($this->data[self::HOJA_CLIENTES]);
            $datos = array_slice($filas, 1); // skip header
            $sospechosas = count(array_filter($datos, fn($f) => $this->esCantidadSospechosa($f[10] ?? null)));
            $preview['clientes'] = [
                'total'       => count($datos),
                'muestra'     => array_slice($datos, 0, 3),
                'sospechosas' => $sospechosas,
                'columnas'    => ['ID','FUENTE','RUC','RAZÓN SOCIAL','ZONA','SEGMENTO','DISTRITO','PROVINCIA','DIRECCIÓN','VENDEDOR','CANTIDAD PANETONES','COMENTARIOS'],
            ];
        }

        // CONTACTOS
        if (isset($this->data[self::HOJA_CONTACTOS])) {
            $datos = array_slice($this->filtrarFilasVacias($this->data[self::HOJA_CONTACTOS]), 1);
            $preview['contactos'] = [
                'total'    => count($datos),
                'muestra'  => array_slice($datos, 0, 3),
                'columnas' => ['ID','RUC','NOMBRE CONTACTO','CARGO','ES PRINCIPAL'],
            ];
        }

        // CORREOS
        if (isset($this->data[self::HOJA_CORREOS])) {
            $datos = array_slice($this->filtrarFilasVacias($this->data[self::HOJA_CORREOS]), 1);
            $preview['correos'] = [
                'total'    => count($datos),
                'muestra'  => array_slice($datos, 0, 3),
                'columnas' => ['ID','RUC','EMAIL','FUENTE','ESTADO'],
            ];
        }

        // TELÉFONOS
        if (isset($this->data[self::HOJA_TELEFONOS])) {
            $datos = array_slice($this->filtrarFilasVacias($this->data[self::HOJA_TELEFONOS]), 1);
            $preview['telefonos'] = [
                'total'    => count($datos),
                'muestra'  => array_slice($datos, 0, 3),
                'columnas' => ['ID','RUC','NÚMERO','TIPO'],
            ];
        }

        // OBSERVACIONES
        if (isset($this->data[self::HOJA_OBSERVACIONES])) {
            $datos = array_slice($this->filtrarFilasVacias($this->data[self::HOJA_OBSERVACIONES]), 1);
            $preview['observaciones'] = [
                'total'    => count($datos),
                'muestra'  => array_slice($datos, 0, 3),
                'columnas' => ['ID','RUC','RAZÓN SOCIAL','ZONA','SEGMENTO','FECHA GESTIÓN','RESULTADO','ACCIÓN PENDIENTE','COMENTARIO ORIGINAL'],
            ];
        }

        return $preview;
    }

    public function importar(string $rutaArchivo, array $opciones, int $usuarioId): array
    {
        set_time_limit(300);

        $this->data = Excel::toArray(new \App\Imports\PreviewImport(), $rutaArchivo);
        $this->resultados = [];

        // 1. Clientes primero (los demás dependen del RUC → cliente_id)
        if ($opciones['importar_clientes'] ?? false) {
            $this->importarClientes();
        }

        // Cargar índice RUC → ID de todos los clientes existentes
        $this->rucIndex = Cliente::whereNotNull('ruc')
            ->pluck('id', 'ruc')
            ->toArray();

        // 2. Contactos
        if ($opciones['importar_contactos'] ?? false) {
            $this->importarContactos();
        }

        // 3. Correos
        if ($opciones['importar_correos'] ?? false) {
            $this->importarCorreos();
        }

        // 4. Teléfonos
        if ($opciones['importar_telefonos'] ?? false) {
            $this->importarTelefonos();
        }

        // 5. Observaciones → Seguimientos
        if ($opciones['importar_observaciones'] ?? false) {
            $this->importarObservaciones($usuarioId);
        }

        return $this->resultados;
    }

    // -------------------------------------------------------------------------
    // Importadores por hoja
    // -------------------------------------------------------------------------

    private function importarClientes(): void
    {
        $filas = array_slice($this->filtrarFilasVacias($this->data[self::HOJA_CLIENTES]), 1);

        $importadas  = 0;
        $duplicadas  = 0;
        $validar     = 0;
        $errores     = 0;
        $detalle     = [];

        // Pre-cargar RUCs existentes
        $rucsExistentes = Cliente::whereNotNull('ruc')->pluck('ruc')->flip()->toArray();

        // Mapa de vendedores por nombre
        $vendedores = User::pluck('id', 'name')->mapWithKeys(
            fn($id, $name) => [strtolower(trim($name)) => $id]
        )->toArray();

        foreach ($filas as $i => $row) {
            $fila         = $i + 2;
            $ruc          = trim((string) ($row[2] ?? ''));
            $razonSocial  = trim((string) ($row[3] ?? ''));

            if (empty($razonSocial)) {
                $errores++;
                $detalle[] = "Fila {$fila}: Sin razón social, omitida.";
                continue;
            }

            if ($ruc && isset($rucsExistentes[$ruc])) {
                $duplicadas++;
                $detalle[] = "Fila {$fila}: RUC {$ruc} ya existe ({$razonSocial}).";
                continue;
            }

            // Cantidad y flag sospechoso
            $cantidadRaw      = $row[10] ?? null;
            $esSospechosa     = $this->esCantidadSospechosa($cantidadRaw);
            $cantidadCompra   = null;
            $cantidadOriginal = null;

            if ($cantidadRaw !== null && $cantidadRaw !== '') {
                $cantidadRaw = (float) $cantidadRaw;
                if ($esSospechosa) {
                    $cantidadOriginal = (string) $cantidadRaw;
                    // Si tiene decimales, claramente es soles → no guardar como cantidad
                    $cantidadCompra = (fmod($cantidadRaw, 1) !== 0.0) ? null : (int) $cantidadRaw;
                } else {
                    $cantidadCompra = (int) $cantidadRaw;
                }
            }

            // Vendedor
            $vendedorNombre = strtolower(trim((string) ($row[9] ?? '')));
            $vendedorId     = $vendedores[$vendedorNombre] ?? null;

            // Fuente/Zona/Segmento
            $fuente   = $this->normalizarFuente(trim((string) ($row[1] ?? '')));
            $zona     = trim((string) ($row[4] ?? '')) ?: null;
            $segmento = $this->normalizarSegmento(trim((string) ($row[5] ?? '')));

            try {
                DB::transaction(function () use (
                    $ruc, $razonSocial, $fuente, $zona, $segmento,
                    $row, $vendedorId, $cantidadCompra, $cantidadOriginal, $esSospechosa,
                    &$rucsExistentes
                ) {
                    $cliente = Cliente::create([
                        'razon_social'           => $razonSocial,
                        'ruc'                    => $ruc ?: null,
                        'fuente'                 => $fuente,
                        'zona'                   => $zona,
                        'segmento'               => $segmento,
                        'distrito'               => trim((string) ($row[6] ?? '')) ?: null,
                        'provincia'              => trim((string) ($row[7] ?? '')) ?: null,
                        'direccion'              => trim((string) ($row[8] ?? '')) ?: null,
                        'vendedor_asignado_id'   => $vendedorId,
                        'cantidad_compra'        => $cantidadCompra,
                        'requiere_validacion'    => $esSospechosa,
                        'cantidad_original_excel'=> $cantidadOriginal,
                        'observaciones'          => trim((string) ($row[11] ?? '')) ?: null,
                        'estado_comercial'       => EstadoComercial::Nuevo->value,
                        'activo'                 => true,
                        'pais'                   => 'Perú',
                    ]);

                    if ($ruc) {
                        $rucsExistentes[$ruc] = true;
                    }
                });

                $importadas++;
                if ($esSospechosa) {
                    $validar++;
                    $detalle[] = "Fila {$fila}: {$razonSocial} — cantidad sospechosa ({$cantidadOriginal}), marcada para validar.";
                }
            } catch (\Throwable $e) {
                $errores++;
                $detalle[] = "Fila {$fila}: Error al guardar {$razonSocial} — {$e->getMessage()}";
            }
        }

        $this->resultados['clientes'] = compact('importadas', 'duplicadas', 'validar', 'errores', 'detalle');
    }

    private function importarContactos(): void
    {
        $filas = array_slice($this->filtrarFilasVacias($this->data[self::HOJA_CONTACTOS]), 1);

        $importadas = 0;
        $omitidas   = 0;
        $errores    = 0;
        $detalle    = [];

        foreach ($filas as $i => $row) {
            $fila   = $i + 2;
            $ruc    = trim((string) ($row[1] ?? ''));
            $nombre = trim((string) ($row[2] ?? ''));

            if (empty($ruc) || empty($nombre)) {
                $omitidas++;
                continue;
            }

            $clienteId = $this->rucIndex[$ruc] ?? null;
            if (!$clienteId) {
                $omitidas++;
                $detalle[] = "Fila {$fila}: RUC {$ruc} no encontrado en el sistema.";
                continue;
            }

            $esPrincipal = strtolower(trim((string) ($row[4] ?? ''))) === 'sí'
                        || strtolower(trim((string) ($row[4] ?? ''))) === 'si'
                        || $row[4] == 1;

            try {
                // Si va a ser principal, quitar el flag de los existentes
                if ($esPrincipal) {
                    ClienteContacto::where('cliente_id', $clienteId)->update(['es_principal' => false]);
                }

                ClienteContacto::create([
                    'cliente_id'      => $clienteId,
                    'nombre_contacto' => $nombre,
                    'cargo'           => trim((string) ($row[3] ?? '')) ?: null,
                    'es_principal'    => $esPrincipal,
                    'activo'          => true,
                ]);

                $importadas++;
            } catch (\Throwable $e) {
                $errores++;
                $detalle[] = "Fila {$fila}: Error — {$e->getMessage()}";
            }
        }

        $this->resultados['contactos'] = compact('importadas', 'omitidas', 'errores', 'detalle');
    }

    private function importarCorreos(): void
    {
        $filas = array_slice($this->filtrarFilasVacias($this->data[self::HOJA_CORREOS]), 1);

        $importadas = 0;
        $omitidas   = 0;
        $errores    = 0;
        $detalle    = [];

        foreach ($filas as $i => $row) {
            $fila   = $i + 2;
            $ruc    = trim((string) ($row[1] ?? ''));
            $email  = strtolower(trim((string) ($row[2] ?? '')));

            if (empty($ruc) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $omitidas++;
                continue;
            }

            $clienteId = $this->rucIndex[$ruc] ?? null;
            if (!$clienteId) {
                $omitidas++;
                continue;
            }

            // Evitar duplicado por cliente+email
            if (ClienteCorreo::where('cliente_id', $clienteId)->where('email', $email)->exists()) {
                $omitidas++;
                continue;
            }

            $estadoRaw = strtolower(trim((string) ($row[4] ?? 'activo')));
            $estado    = in_array($estadoRaw, ['activo', 'active']) ? 'activo' : 'inactivo';

            try {
                ClienteCorreo::create([
                    'cliente_id'  => $clienteId,
                    'email'       => $email,
                    'fuente'      => trim((string) ($row[3] ?? '')) ?: null,
                    'estado'      => $estado,
                    'es_principal'=> false,
                    'activo'      => true,
                ]);
                $importadas++;
            } catch (\Throwable $e) {
                $errores++;
                $detalle[] = "Fila {$fila}: Error — {$e->getMessage()}";
            }
        }

        // Marcar como principal el primer correo activo de cada cliente
        $clientesConCorreo = ClienteCorreo::where('activo', true)
            ->where('estado', 'activo')
            ->whereNotExists(function ($q) {
                $q->from('cliente_correos as cc2')
                  ->whereColumn('cc2.cliente_id', 'cliente_correos.cliente_id')
                  ->where('cc2.es_principal', true);
            })
            ->select('cliente_id', DB::raw('MIN(id) as primer_id'))
            ->groupBy('cliente_id')
            ->get();

        foreach ($clientesConCorreo as $item) {
            ClienteCorreo::where('id', $item->primer_id)->update(['es_principal' => true]);
        }

        $this->resultados['correos'] = compact('importadas', 'omitidas', 'errores', 'detalle');
    }

    private function importarTelefonos(): void
    {
        $filas = array_slice($this->filtrarFilasVacias($this->data[self::HOJA_TELEFONOS]), 1);

        $importadas = 0;
        $omitidas   = 0;
        $errores    = 0;
        $detalle    = [];

        foreach ($filas as $i => $row) {
            $fila   = $i + 2;
            $ruc    = trim((string) ($row[1] ?? ''));
            $numero = trim((string) ($row[2] ?? ''));

            if (empty($ruc) || empty($numero)) {
                $omitidas++;
                continue;
            }

            $clienteId = $this->rucIndex[$ruc] ?? null;
            if (!$clienteId) {
                $omitidas++;
                continue;
            }

            // Evitar duplicado por cliente+numero
            if (ClienteTelefono::where('cliente_id', $clienteId)->where('numero', $numero)->exists()) {
                $omitidas++;
                continue;
            }

            $tipoRaw = strtolower(trim((string) ($row[3] ?? 'celular')));
            $tipo    = match(true) {
                str_contains($tipoRaw, 'whatsapp')            => 'whatsapp',
                str_contains($tipoRaw, 'fijo'), $tipoRaw === 'fijo' => 'fijo',
                default                                        => 'celular',
            };

            try {
                ClienteTelefono::create([
                    'cliente_id'  => $clienteId,
                    'numero'      => $numero,
                    'tipo'        => $tipo,
                    'es_principal'=> false,
                    'activo'      => true,
                ]);
                $importadas++;
            } catch (\Throwable $e) {
                $errores++;
                $detalle[] = "Fila {$fila}: Error — {$e->getMessage()}";
            }
        }

        // Marcar como principal el primer teléfono de cada cliente
        $clientesConTel = ClienteTelefono::whereNotExists(function ($q) {
            $q->from('cliente_telefonos as ct2')
              ->whereColumn('ct2.cliente_id', 'cliente_telefonos.cliente_id')
              ->where('ct2.es_principal', true);
        })
        ->select('cliente_id', DB::raw('MIN(id) as primer_id'))
        ->groupBy('cliente_id')
        ->get();

        foreach ($clientesConTel as $item) {
            ClienteTelefono::where('id', $item->primer_id)->update(['es_principal' => true]);
        }

        $this->resultados['telefonos'] = compact('importadas', 'omitidas', 'errores', 'detalle');
    }

    private function importarObservaciones(int $usuarioId): void
    {
        $filas = array_slice($this->filtrarFilasVacias($this->data[self::HOJA_OBSERVACIONES]), 1);

        $importadas = 0;
        $omitidas   = 0;
        $errores    = 0;
        $detalle    = [];

        foreach ($filas as $i => $row) {
            $fila       = $i + 2;
            $ruc        = trim((string) ($row[1] ?? ''));
            $comentario = trim((string) ($row[8] ?? ''));
            $resultado  = trim((string) ($row[6] ?? ''));

            // Necesitamos al menos RUC y algo de contenido
            if (empty($ruc) || (empty($comentario) && empty($resultado))) {
                $omitidas++;
                continue;
            }

            $clienteId = $this->rucIndex[$ruc] ?? null;
            if (!$clienteId) {
                $omitidas++;
                continue;
            }

            // Intentar parsear la fecha
            $fechaRaw = trim((string) ($row[5] ?? ''));
            $fecha    = null;
            if (!empty($fechaRaw)) {
                try {
                    $fecha = Carbon::parse($fechaRaw);
                } catch (\Throwable) {
                    $fecha = null;
                }
            }

            $detalleSeguimiento = $comentario ?: $resultado;
            $accionPendiente    = trim((string) ($row[7] ?? '')) ?: null;

            try {
                Seguimiento::create([
                    'cliente_id'       => $clienteId,
                    'usuario_id'       => $usuarioId,
                    'tipo'             => TipoSeguimiento::Observacion->value,
                    'fecha_hora'       => $fecha ?? now(),
                    'detalle'          => $detalleSeguimiento,
                    'resultado'        => $resultado ?: null,
                    'proxima_accion'   => $accionPendiente,
                ]);
                $importadas++;
            } catch (\Throwable $e) {
                $errores++;
                $detalle[] = "Fila {$fila}: Error — {$e->getMessage()}";
            }
        }

        $this->resultados['observaciones'] = compact('importadas', 'omitidas', 'errores', 'detalle');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function filtrarFilasVacias(array $filas): array
    {
        return array_values(array_filter($filas, function ($fila) {
            return count(array_filter($fila, fn($v) => $v !== null && $v !== '')) > 0;
        }));
    }

    private function esCantidadSospechosa(mixed $valor): bool
    {
        if ($valor === null || $valor === '') {
            return false;
        }
        $num = (float) $valor;
        return $num > self::UMBRAL_CANTIDAD_SOSPECHOSA || fmod($num, 1) !== 0.0;
    }

    private function normalizarFuente(string $valor): ?string
    {
        $v = strtolower($valor);
        if (str_contains($v, 'lima'))      return 'lima';
        if (str_contains($v, 'provincia')) return 'provincia';
        return empty($valor) ? null : 'otro';
    }

    private function normalizarSegmento(string $valor): ?string
    {
        $v = strtolower($valor);
        if ($v === 'vip')    return 'vip';
        if ($v === 'normal') return 'normal';
        return empty($valor) ? null : 'otro';
    }
}
