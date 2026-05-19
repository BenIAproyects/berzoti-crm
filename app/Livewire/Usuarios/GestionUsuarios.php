<?php

namespace App\Livewire\Usuarios;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class GestionUsuarios extends Component
{
    use WithPagination;

    public string $busqueda = '';

    // Form
    public ?int $editandoId = null;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $rol = 'vendedor';
    public bool $mostrarModal = false;

    public string $confirmandoEliminarId = '';

    public function updatingBusqueda(): void { $this->resetPage(); }

    public function abrirCrear(): void
    {
        $this->reset(['editandoId', 'name', 'email', 'password', 'rol']);
        $this->rol = 'vendedor';
        $this->mostrarModal = true;
    }

    public function abrirEditar(int $id): void
    {
        $user = User::findOrFail($id);
        $this->editandoId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->rol = $user->getRoleNames()->first() ?? 'vendedor';
        $this->mostrarModal = true;
    }

    public function guardar(): void
    {
        $rules = [
            'name'  => 'required|string|max:100',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->editandoId)],
            'rol'   => 'required|in:administrador,vendedor,supervisor',
        ];

        if (!$this->editandoId) {
            $rules['password'] = 'required|min:8';
        } elseif ($this->password !== '') {
            $rules['password'] = 'min:8';
        }

        $this->validate($rules, [
            'name.required'     => 'El nombre es obligatorio.',
            'email.required'    => 'El correo es obligatorio.',
            'email.unique'      => 'Este correo ya está en uso.',
            'password.required' => 'La contraseña es obligatoria para nuevos usuarios.',
            'password.min'      => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        if ($this->editandoId) {
            $user = User::findOrFail($this->editandoId);
            $user->name  = $this->name;
            $user->email = $this->email;
            if ($this->password !== '') {
                $user->password = Hash::make($this->password);
            }
            $user->save();
            $user->syncRoles([$this->rol]);
        } else {
            $user = User::create([
                'name'     => $this->name,
                'email'    => $this->email,
                'password' => Hash::make($this->password),
            ]);
            $user->assignRole($this->rol);
        }

        $this->mostrarModal = false;
        session()->flash('success', $this->editandoId ? 'Usuario actualizado.' : 'Usuario creado.');
        $this->reset(['editandoId', 'name', 'email', 'password', 'rol']);
    }

    public function eliminar(int $id): void
    {
        if ($id === auth()->id()) {
            session()->flash('error', 'No puedes eliminar tu propia cuenta.');
            return;
        }
        User::findOrFail($id)->delete();
        session()->flash('success', 'Usuario eliminado.');
    }

    public function render()
    {
        $usuarios = User::with('roles')
            ->when($this->busqueda, fn($q) =>
                $q->where('name', 'like', "%{$this->busqueda}%")
                  ->orWhere('email', 'like', "%{$this->busqueda}%")
            )
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.usuarios.gestion-usuarios', [
            'usuarios' => $usuarios,
            'roles'    => Role::orderBy('name')->get(),
        ]);
    }
}
