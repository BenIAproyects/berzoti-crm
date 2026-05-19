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
            'reportes.ver',
            'dashboard.ver', 'dashboard.global',
        ]);
    }
}
