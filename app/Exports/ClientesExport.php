<?php

namespace App\Exports;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ClientesExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    public function __construct(
        private readonly string $busqueda = '',
        private readonly string $filtroEstado = '',
        private readonly string $filtroTipo = '',
        private readonly string $filtroVendedor = '',
    ) {}

    public function title(): string
    {
        return 'Clientes';
    }

    public function query(): Builder
    {
        return Cliente::with('vendedor')
            ->where('activo', true)
            ->when($this->busqueda,       fn($q) => $q->buscar($this->busqueda))
            ->when($this->filtroEstado,   fn($q) => $q->where('estado_comercial', $this->filtroEstado))
            ->when($this->filtroTipo,     fn($q) => $q->where('tipo_cliente', $this->filtroTipo))
            ->when($this->filtroVendedor, fn($q) => $q->where('vendedor_asignado_id', $this->filtroVendedor))
            ->orderByDesc('updated_at');
    }

    public function headings(): array
    {
        return [
            'Código',
            'Razón social',
            'Nombre comercial',
            'RUC',
            'Tipo',
            'Sector',
            'Contacto principal',
            'Cargo',
            'Teléfono',
            'WhatsApp',
            'Correo',
            'Correo secundario',
            'Departamento',
            'Provincia',
            'Distrito',
            'Dirección',
            'Vendedor asignado',
            'Estado comercial',
            'Prioridad',
            'Cant. habitual (unid.)',
            'Mes de contacto',
            'Precio año anterior (S/.)',
            'Último contacto',
            'Próximo contacto',
            'Observaciones',
        ];
    }

    public function map($cliente): array
    {
        return [
            $cliente->codigo,
            $cliente->razon_social,
            $cliente->nombre_comercial,
            $cliente->ruc,
            $cliente->tipo_cliente->label(),
            $cliente->sector,
            $cliente->contacto_principal,
            $cliente->cargo_contacto,
            $cliente->telefono,
            $cliente->whatsapp,
            $cliente->correo,
            $cliente->correo_secundario,
            $cliente->departamento,
            $cliente->provincia,
            $cliente->distrito,
            $cliente->direccion,
            $cliente->vendedor?->name,
            $cliente->estado_comercial->label(),
            ucfirst($cliente->prioridad),
            $cliente->cantidad_compra,
            $cliente->mes_contacto ? ucfirst($cliente->mes_contacto) : null,
            $cliente->precio_ano_anterior,
            $cliente->fecha_ultimo_contacto?->format('d/m/Y'),
            $cliente->fecha_proximo_contacto?->format('d/m/Y'),
            $cliente->observaciones,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:Y1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4F46E5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(22);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,  // Código
            'B' => 35,  // Razón social
            'C' => 25,  // Nombre comercial
            'D' => 14,  // RUC
            'E' => 18,  // Tipo
            'F' => 18,  // Sector
            'G' => 25,  // Contacto
            'H' => 22,  // Cargo
            'I' => 14,  // Teléfono
            'J' => 14,  // WhatsApp
            'K' => 30,  // Correo
            'L' => 30,  // Correo secundario
            'M' => 16,  // Departamento
            'N' => 16,  // Provincia
            'O' => 16,  // Distrito
            'P' => 30,  // Dirección
            'Q' => 22,  // Vendedor
            'R' => 18,  // Estado
            'S' => 12,  // Prioridad
            'T' => 20,  // Cant. habitual
            'U' => 16,  // Mes contacto
            'V' => 22,  // Precio año anterior
            'W' => 14,  // Último contacto
            'X' => 14,  // Próximo contacto
            'Y' => 40,  // Observaciones
        ];
    }
}
