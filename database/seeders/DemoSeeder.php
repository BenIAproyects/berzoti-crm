<?php

namespace Database\Seeders;

use App\Models\Campana;
use App\Models\Cliente;
use App\Models\PlantillaCorreo;
use App\Models\Seguimiento;
use App\Models\Tarea;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Usuarios demo
        $vendedor1 = User::firstOrCreate(
            ['email' => 'vendedor@berzoti.com'],
            ['name' => 'Carlos Vendedor', 'password' => Hash::make('vendedor123')]
        );
        $vendedor1->assignRole('vendedor');

        $vendedor2 = User::firstOrCreate(
            ['email' => 'maria@berzoti.com'],
            ['name' => 'María García', 'password' => Hash::make('vendedor123')]
        );
        $vendedor2->assignRole('vendedor');

        $supervisor = User::firstOrCreate(
            ['email' => 'supervisor@berzoti.com'],
            ['name' => 'Luis Supervisor', 'password' => Hash::make('supervisor123')]
        );
        $supervisor->assignRole('supervisor');

        $admin = User::where('email', 'admin@berzoti.com')->first();

        // Clientes demo
        $clientes = [
            ['razon_social' => 'Corporación Andina S.A.C.', 'tipo_cliente' => 'corporacion', 'sector' => 'Retail', 'contacto_principal' => 'Roberto Quispe', 'cargo_contacto' => 'Gerente de Compras', 'correo' => 'rquispe@andina.com', 'telefono' => '987654321', 'estado_comercial' => 'interesado', 'prioridad' => 'alta', 'vendedor_asignado_id' => $vendedor1->id],
            ['razon_social' => 'Distribuidora Lima Norte S.R.L.', 'tipo_cliente' => 'distribuidor', 'sector' => 'Distribución', 'contacto_principal' => 'Ana Torres', 'cargo_contacto' => 'Administradora', 'correo' => 'atorres@limanorte.com', 'telefono' => '976543210', 'estado_comercial' => 'cotizacion_enviada', 'prioridad' => 'alta', 'vendedor_asignado_id' => $vendedor1->id],
            ['razon_social' => 'Minimarket El Sol E.I.R.L.', 'tipo_cliente' => 'minorista', 'sector' => 'Comercio', 'contacto_principal' => 'Pedro Mamani', 'correo' => 'pmamani@elsol.com', 'telefono' => '965432109', 'estado_comercial' => 'en_seguimiento', 'prioridad' => 'media', 'vendedor_asignado_id' => $vendedor2->id],
            ['razon_social' => 'Supermercados Familiar S.A.', 'tipo_cliente' => 'corporacion', 'sector' => 'Supermercados', 'contacto_principal' => 'Carmen Vega', 'cargo_contacto' => 'Jefa de Compras', 'correo' => 'cvega@familiar.com', 'telefono' => '954321098', 'estado_comercial' => 'negociacion', 'prioridad' => 'alta', 'vendedor_asignado_id' => $vendedor2->id],
            ['razon_social' => 'Comercial Huanca Hnos.', 'tipo_cliente' => 'comercializadora', 'sector' => 'Mayorista', 'contacto_principal' => 'José Huanca', 'correo' => 'jhuanca@huanca.com', 'telefono' => '943210987', 'estado_comercial' => 'nuevo', 'prioridad' => 'baja', 'vendedor_asignado_id' => $vendedor1->id],
            ['razon_social' => 'Tiendas Plaza Sur S.A.C.', 'tipo_cliente' => 'minorista', 'sector' => 'Retail', 'contacto_principal' => 'Lucía Paredes', 'correo' => 'lparedes@plazasur.com', 'telefono' => '932109876', 'estado_comercial' => 'correo_enviado', 'prioridad' => 'media', 'vendedor_asignado_id' => $vendedor2->id],
            ['razon_social' => 'Grupo Empresarial Norte E.I.R.L.', 'tipo_cliente' => 'corporacion', 'sector' => 'Servicios', 'contacto_principal' => 'Miguel Flores', 'correo' => 'mflores@grupanorte.com', 'telefono' => '921098765', 'estado_comercial' => 'cerrado_ganado', 'prioridad' => 'alta', 'vendedor_asignado_id' => $vendedor1->id],
            ['razon_social' => 'Bodega Santa Rosa', 'tipo_cliente' => 'minorista', 'sector' => 'Comercio', 'contacto_principal' => 'Rosa Ccoa', 'correo' => 'rccoa@santarosa.com', 'telefono' => '910987654', 'estado_comercial' => 'no_responde', 'prioridad' => 'baja', 'vendedor_asignado_id' => $vendedor2->id],
        ];

        $clienteModelos = [];
        foreach ($clientes as $datos) {
            $datos['pais'] = 'Perú';
            $datos['departamento'] = 'Lima';
            $datos['fecha_ultimo_contacto'] = now()->subDays(rand(1, 15));
            $cliente = Cliente::firstOrCreate(['razon_social' => $datos['razon_social']], $datos);
            $clienteModelos[] = $cliente;
        }

        // Campaña demo
        $campana = Campana::firstOrCreate(
            ['nombre' => 'Campaña Panetones Navidad 2026'],
            [
                'descripcion'       => 'Campaña principal de ventas de panetones para la temporada navideña 2026.',
                'fecha_inicio'      => now()->subDays(10),
                'fecha_fin'         => now()->addMonths(2),
                'estado'            => 'activa',
                'objetivo_comercial' => 'Alcanzar S/ 150,000 en ventas de panetones corporativos.',
                'created_by'        => $admin?->id ?? 1,
            ]
        );

        // Asignar clientes a campaña
        foreach ($clienteModelos as $c) {
            if (!$campana->clientes()->where('cliente_id', $c->id)->exists()) {
                $campana->clientes()->attach($c->id, ['estado_en_campana' => 'nuevo']);
            }
        }

        // Plantilla demo
        PlantillaCorreo::firstOrCreate(
            ['nombre' => 'Presentación panetones 2026'],
            [
                'asunto'     => 'Propuesta comercial panetones Berzoti - {{razon_social}}',
                'cuerpo_html' => "Estimado(a) {{contacto_principal}},\n\nMi nombre es {{vendedor_nombre}} y me comunico de parte de Berzoti para presentarle nuestra línea exclusiva de panetones para esta temporada navideña 2026.\n\nOfrecemos productos de alta calidad con precios especiales para clientes corporativos como {{razon_social}}.\n\nNos gustaría coordinar una reunión para presentarle nuestro catálogo completo.\n\n¿Tendría disponibilidad esta semana?\n\nQuedo atento a su respuesta.\n\nSaludos cordiales,\n{{vendedor_nombre}}",
                'activo'     => true,
                'created_by' => $admin?->id ?? 1,
            ]
        );

        // Seguimientos demo
        if ($clienteModelos[0]->seguimientos()->count() === 0) {
            $seg = Seguimiento::create([
                'cliente_id'  => $clienteModelos[0]->id,
                'campana_id'  => $campana->id,
                'usuario_id'  => $vendedor1->id,
                'tipo'        => 'llamada',
                'fecha_hora'  => now()->subDays(5),
                'detalle'     => 'Llamada inicial para presentar propuesta de panetones.',
                'resultado'   => 'Cliente mostró interés. Solicitó cotización por 500 unidades.',
                'estado_comercial_nuevo' => 'interesado',
                'proxima_accion'         => 'Enviar cotización detallada',
                'fecha_proxima_accion'   => now()->addDays(2)->format('Y-m-d'),
            ]);

            // Tarea automática del seguimiento
            Tarea::create([
                'cliente_id'        => $clienteModelos[0]->id,
                'campana_id'        => $campana->id,
                'usuario_id'        => $vendedor1->id,
                'seguimiento_id'    => $seg->id,
                'titulo'            => 'Enviar cotización detallada',
                'tipo'              => 'enviar_cotizacion',
                'fecha_vencimiento' => now()->addDays(2)->format('Y-m-d'),
                'estado'            => 'pendiente',
                'prioridad'         => 'alta',
            ]);
        }

        // Tarea vencida demo (para mostrar alerta en dashboard)
        if (Tarea::where('estado', 'pendiente')->where('fecha_vencimiento', '<', today())->count() === 0) {
            Tarea::create([
                'cliente_id'        => $clienteModelos[2]->id,
                'campana_id'        => $campana->id,
                'usuario_id'        => $vendedor2->id,
                'titulo'            => 'Llamar para hacer seguimiento',
                'tipo'              => 'llamar',
                'fecha_vencimiento' => now()->subDays(3)->format('Y-m-d'),
                'estado'            => 'pendiente',
                'prioridad'         => 'media',
            ]);
        }

        $this->command->info('✅ Datos demo creados correctamente.');
        $this->command->line('   Usuarios: vendedor@berzoti.com / vendedor123');
        $this->command->line('             maria@berzoti.com / vendedor123');
        $this->command->line('             supervisor@berzoti.com / supervisor123');
    }
}
