<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesPermisosSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permisos = [
            // Usuarios
            'usuarios.ver', 'usuarios.crear', 'usuarios.editar', 'usuarios.eliminar',
            // Clientes
            'clientes.ver', 'clientes.crear', 'clientes.editar', 'clientes.eliminar',
            'clientes.importar', 'clientes.asignar', 'clientes.exportar',
            // Campañas
            'campanas.ver', 'campanas.crear', 'campanas.editar', 'campanas.eliminar',
            // Plantillas
            'plantillas.ver', 'plantillas.crear', 'plantillas.editar', 'plantillas.eliminar',
            // Correos
            'correos.ver', 'correos.enviar',
            // Seguimientos
            'seguimientos.ver', 'seguimientos.crear',
            // Tareas
            'tareas.ver', 'tareas.crear', 'tareas.completar',
            // Cotizaciones
            'cotizaciones.ver', 'cotizaciones.crear', 'cotizaciones.editar',
            // Órdenes de Compra
            'ordenes.ver', 'ordenes.crear', 'ordenes.editar', 'ordenes.eliminar',
            // Facturas
            'facturas.ver', 'facturas.crear', 'facturas.editar', 'facturas.eliminar',
            // Guías de Remisión
            'guias.ver', 'guias.crear', 'guias.editar', 'guias.eliminar',
            // Pagos
            'pagos.ver', 'pagos.crear', 'pagos.editar', 'pagos.eliminar',
            // Reportes
            'reportes.ver',
            // Dashboard
            'dashboard.ver', 'dashboard.global',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        $administrador = Role::firstOrCreate(['name' => 'administrador']);
        $administrador->syncPermissions(Permission::all());

        $vendedor = Role::firstOrCreate(['name' => 'vendedor']);
        $vendedor->syncPermissions([
            'clientes.ver', 'clientes.crear', 'clientes.editar',
            'campanas.ver',
            'plantillas.ver',
            'correos.ver', 'correos.enviar',
            'seguimientos.ver', 'seguimientos.crear',
            'tareas.ver', 'tareas.crear', 'tareas.completar',
            'cotizaciones.ver', 'cotizaciones.crear', 'cotizaciones.editar',
            'ordenes.ver', 'ordenes.crear', 'ordenes.editar',
            'facturas.ver', 'facturas.crear', 'facturas.editar',
            'guias.ver', 'guias.crear', 'guias.editar',
            'pagos.ver', 'pagos.crear', 'pagos.editar',
            'dashboard.ver',
        ]);

        $supervisor = Role::firstOrCreate(['name' => 'supervisor']);
        $supervisor->syncPermissions([
            'clientes.ver', 'clientes.exportar',
            'campanas.ver',
            'correos.ver',
            'seguimientos.ver',
            'tareas.ver',
            'cotizaciones.ver',
            'ordenes.ver',
            'facturas.ver',
            'guias.ver',
            'pagos.ver',
            'reportes.ver',
            'dashboard.ver', 'dashboard.global',
        ]);
    }
}
