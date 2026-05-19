<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TemplateClientesExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    public function title(): string
    {
        return 'Clientes';
    }

    public function headings(): array
    {
        return [
            'razon_social',
            'nombre_comercial',
            'ruc',
            'tipo_cliente',
            'sector',
            'contacto_principal',
            'cargo_contacto',
            'telefono',
            'whatsapp',
            'correo',
            'correo_secundario',
            'pais',
            'departamento',
            'provincia',
            'distrito',
            'direccion',
            'referencia',
            'prioridad',
            'origen',
            'cantidad_compra',
            'mes_contacto',
            'precio_ano_anterior',
            'observaciones',
        ];
    }

    public function array(): array
    {
        // Filas de ejemplo para que el usuario entienda el formato
        return [
            [
                'Corporación Ejemplo S.A.C.',
                'Ejemplo Corp',
                '20123456789',
                'corporacion',
                'Alimentaria',
                'Juan Pérez',
                'Gerente de Compras',
                '01-2345678',
                '987654321',
                'juan.perez@ejemplo.com',
                '',
                'Perú',
                'Lima',
                'Lima',
                'Miraflores',
                'Av. Ejemplo 123',
                'Frente al parque',
                'alta',
                'Feria',
                5000,
                'mayo',
                8.50,
                'Cliente interesado en pedidos grandes',
            ],
            [
                'Distribuidora Norte E.I.R.L.',
                '',
                '20987654321',
                'distribuidor',
                'Retail',
                'María García',
                'Administradora',
                '044-234567',
                '912345678',
                'mgarcia@norte.com',
                '',
                'Perú',
                'La Libertad',
                'Trujillo',
                'Trujillo',
                'Jr. Comercio 456',
                '',
                'media',
                'Referido',
                2000,
                'julio',
                7.00,
                '',
            ],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Estilo del encabezado
        $sheet->getStyle('A1:W1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4F46E5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Estilo de las filas de ejemplo
        $sheet->getStyle('A2:W3')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF5F3FF']],
            'font' => ['color' => ['argb' => 'FF6B7280']],
        ]);

        // Borde en encabezado
        $sheet->getStyle('A1:W1')->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $sheet->getRowDimension(1)->setRowHeight(22);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 35, // razon_social
            'B' => 25, // nombre_comercial
            'C' => 16, // ruc
            'D' => 18, // tipo_cliente
            'E' => 18, // sector
            'F' => 25, // contacto_principal
            'G' => 22, // cargo_contacto
            'H' => 16, // telefono
            'I' => 16, // whatsapp
            'J' => 30, // correo
            'K' => 30, // correo_secundario
            'L' => 12, // pais
            'M' => 16, // departamento
            'N' => 16, // provincia
            'O' => 16, // distrito
            'P' => 30, // direccion
            'Q' => 25, // referencia
            'R' => 12, // prioridad
            'S' => 18, // origen
            'T' => 18, // cantidad_compra
            'U' => 16, // mes_contacto
            'V' => 20, // precio_ano_anterior
            'W' => 35, // observaciones
        ];
    }
}
