<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $cotizacion->codigo }} – Cotización</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #1a1a1a; background: #fff; padding: 32px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 28px; }
        .logo-area h1 { font-size: 20px; font-weight: 700; color: #4338ca; letter-spacing: -0.5px; }
        .logo-area p { font-size: 11px; color: #6b7280; margin-top: 2px; }
        .doc-info { text-align: right; }
        .doc-info .code { font-size: 18px; font-weight: 700; color: #1a1a1a; }
        .doc-info .date { font-size: 11px; color: #6b7280; margin-top: 3px; }
        .divider { border: none; border-top: 2px solid #e5e7eb; margin: 20px 0; }
        .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px; }
        .section-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #9ca3af; margin-bottom: 6px; }
        .client-name { font-size: 14px; font-weight: 700; color: #1a1a1a; }
        .client-detail { font-size: 11px; color: #4b5563; margin-top: 2px; }
        .meta-row { display: flex; justify-content: space-between; font-size: 11px; color: #6b7280; margin-bottom: 4px; }
        .meta-row span:last-child { font-weight: 600; color: #374151; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        thead th { background: #f9fafb; border-bottom: 2px solid #e5e7eb; padding: 8px 10px; text-align: left; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; color: #6b7280; }
        thead th.right { text-align: right; }
        thead th.center { text-align: center; }
        tbody td { padding: 9px 10px; border-bottom: 1px solid #f3f4f6; font-size: 12px; color: #374151; }
        tbody td.right { text-align: right; }
        tbody td.center { text-align: center; }
        tfoot td { padding: 10px; font-size: 13px; }
        .total-label { text-align: right; font-weight: 600; color: #374151; }
        .total-value { text-align: right; font-weight: 700; color: #4338ca; font-size: 15px; width: 120px; }
        .observations { background: #f9fafb; border-left: 3px solid #e5e7eb; padding: 10px 14px; margin-bottom: 20px; border-radius: 4px; }
        .observations p { font-size: 11px; color: #4b5563; line-height: 1.5; }
        .footer { margin-top: 40px; display: flex; justify-content: space-between; align-items: flex-end; }
        .footer-note { font-size: 10px; color: #9ca3af; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px; }
        .badge-borrador { background: #f3f4f6; color: #6b7280; }
        .badge-enviada { background: #eff6ff; color: #1d4ed8; }
        .badge-aprobada { background: #f0fdf4; color: #16a34a; }
        .badge-rechazada { background: #fef2f2; color: #dc2626; }
        .badge-vencida { background: #fff7ed; color: #ea580c; }
        @media print {
            body { padding: 16px; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    {{-- Botón imprimir (oculto al imprimir) --}}
    <div class="no-print" style="margin-bottom: 20px; display: flex; gap: 10px;">
        <button onclick="window.print()" style="padding: 8px 18px; background: #4338ca; color: #fff; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer;">
            Imprimir / Guardar PDF
        </button>
        <button onclick="window.close()" style="padding: 8px 16px; background: #f3f4f6; color: #374151; border: none; border-radius: 6px; font-size: 13px; cursor: pointer;">
            Cerrar
        </button>
    </div>

    {{-- Encabezado --}}
    <div class="header">
        <div class="logo-area">
            <h1>{{ config('app.name', 'Berzoti CRM') }}</h1>
            <p>Sistema Comercial de Panetones</p>
        </div>
        <div class="doc-info">
            <div class="code">{{ $cotizacion->codigo }}</div>
            <div class="date">Fecha: {{ $cotizacion->fecha->format('d/m/Y') }}</div>
            <div style="margin-top: 6px;">
                <span class="badge badge-{{ $cotizacion->estado->value }}">{{ $cotizacion->estado->label() }}</span>
            </div>
        </div>
    </div>

    <hr class="divider">

    {{-- Datos del cliente y cotización --}}
    <div class="two-col">
        <div>
            <p class="section-label">Cliente</p>
            <p class="client-name">{{ $cotizacion->cliente->razon_social }}</p>
            @if($cotizacion->cliente->nombre_comercial && $cotizacion->cliente->nombre_comercial !== $cotizacion->cliente->razon_social)
            <p class="client-detail">{{ $cotizacion->cliente->nombre_comercial }}</p>
            @endif
            @if($cotizacion->cliente->ruc)
            <p class="client-detail">RUC: {{ $cotizacion->cliente->ruc }}</p>
            @endif
            @if($cotizacion->cliente->contacto_principal)
            <p class="client-detail">Attn.: {{ $cotizacion->cliente->contacto_principal }}</p>
            @endif
            @if($cotizacion->cliente->correo)
            <p class="client-detail">{{ $cotizacion->cliente->correo }}</p>
            @endif
        </div>
        <div>
            <p class="section-label">Detalle de cotización</p>
            @if($cotizacion->campana)
            <div class="meta-row"><span>Campaña</span><span>{{ $cotizacion->campana->nombre }}</span></div>
            @endif
            <div class="meta-row"><span>Elaborado por</span><span>{{ $cotizacion->usuario->name }}</span></div>
            @if($cotizacion->fecha_envio)
            <div class="meta-row"><span>Fecha de envío</span><span>{{ $cotizacion->fecha_envio->format('d/m/Y') }}</span></div>
            @endif
            @if($cotizacion->fecha_respuesta)
            <div class="meta-row"><span>Fecha de respuesta</span><span>{{ $cotizacion->fecha_respuesta->format('d/m/Y') }}</span></div>
            @endif
        </div>
    </div>

    {{-- Tabla de ítems --}}
    <table>
        <thead>
            <tr>
                <th style="width: 30px;">#</th>
                <th>Descripción</th>
                <th class="center" style="width: 80px;">Cantidad</th>
                <th class="right" style="width: 110px;">Precio unit.</th>
                <th class="right" style="width: 110px;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cotizacion->items as $i => $item)
            <tr>
                <td style="color: #9ca3af;">{{ $i + 1 }}</td>
                <td>{{ $item->descripcion }}</td>
                <td class="center">{{ number_format($item->cantidad, 2) }}</td>
                <td class="right">S/ {{ number_format($item->precio_unitario, 2) }}</td>
                <td class="right" style="font-weight: 600;">S/ {{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; color: #9ca3af; padding: 20px;">Sin ítems registrados.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3"></td>
                <td class="total-label">TOTAL:</td>
                <td class="total-value">S/ {{ number_format($cotizacion->monto_total, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- Observaciones --}}
    @if($cotizacion->observaciones)
    <div class="observations">
        <p class="section-label" style="margin-bottom: 4px;">Observaciones</p>
        <p>{{ $cotizacion->observaciones }}</p>
    </div>
    @endif

    {{-- Pie de página --}}
    <div class="footer">
        <div class="footer-note">
            Generado el {{ now()->format('d/m/Y H:i') }}
        </div>
        <div style="text-align: right;">
            <div style="width: 160px; border-top: 1px solid #d1d5db; padding-top: 6px; text-align: center; font-size: 10px; color: #6b7280;">
                Firma y sello del cliente
            </div>
        </div>
    </div>

</body>
</html>
