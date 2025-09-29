<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RolController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:ver-rol|crear-rol|editar-rol|borrar-rol', ['only' => ['index']]);
        $this->middleware('permission:crear-rol', ['only' => ['create', 'store']]);
        $this->middleware('permission:editar-rol', ['only' => ['edit', 'update']]);
        $this->middleware('permission:borrar-rol', ['only' => ['destroy']]);
    }

    public function index()
    {
        $permission = Permission::get();
        $roles = Role::all();
        return view('roles.index', [
            'roles' => $roles,
            'permission' => $permission
        ]);
    }

    public function create()
    {
        $permission = Permission::get();
        return view('roles.crear', compact('permission'));
    }

    public function store(Request $request)
    {
        $allowedEmails = ['argenis@mail.com', 'yasnier@mail.com'];
        $roleName = strtoupper($request->input('name'));

        // Solo Argenis y Yasnier (con SUPERADMIN) pueden crear el rol SUPERADMIN
        if ($roleName === 'SUPERADMIN' && !in_array(auth()->user()->email, $allowedEmails)) {
            flash()->error('No tienes permiso para crear el rol SUPERADMIN.');
            return redirect()->route('roles.index');
        }

        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required|array',
            'permission.*' => 'exists:permissions,name'
        ]);

        $permissions = $request->input('permission');

        // Validar permisos conflictivos solo si el rol no es SUPERADMIN
        if ($roleName !== 'SUPERADMIN') {
            $hasFiscalizacion = array_intersect($permissions, ['ver-fiscalizacion', 'crear-fiscalizacion', 'editar-fiscalizacion', 'borrar-fiscalizacion']);
            $hasFiscalizacionAll = array_intersect($permissions, ['ver-fiscalizacion-all', 'crear-fiscalizacion-all', 'editar-fiscalizacion-all', 'borrar-fiscalizacion-all']);
            if ($hasFiscalizacion && $hasFiscalizacionAll) {
                return redirect()->back()->withInput()->withErrors(['permission' => 'No se pueden asignar permisos de Fiscalización (Activos) y Fiscalización (Todos) al mismo rol.']);
            }
        }

        $role = Role::create(['name' => $roleName]);
        $role->syncPermissions($permissions);

        Log::info('Rol creado', [
            'role' => $role->name,
            'permissions' => $permissions,
            'user_id' => auth()->user()->id
        ]);

        flash()->success('Rol creado exitosamente');

        return redirect()->route('roles.index');
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $allowedEmails = ['argenis@mail.com', 'yasnier@mail.com'];

        // Solo Argenis y Yasnier (con SUPERADMIN) pueden editar el rol SUPERADMIN
        if ($role->name === 'SUPERADMIN' && !in_array(auth()->user()->email, $allowedEmails)) {
            flash()->error('No tienes permiso para editar el rol SUPERADMIN.');
            return redirect()->route('roles.index');
        }

        $permission = Permission::get();
        $rolePermissions = DB::table('role_has_permissions')
            ->where('role_has_permissions.role_id', $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();
        return view('roles.editar', compact('role', 'permission', 'rolePermissions'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $allowedEmails = ['argenis@mail.com', 'yasnier@mail.com'];

        // Solo Argenis y Yasnier (con SUPERADMIN) pueden editar el rol SUPERADMIN
        if ($role->name === 'SUPERADMIN' && !in_array(auth()->user()->email, $allowedEmails)) {
            flash()->error('No tienes permiso para editar el rol SUPERADMIN.');
            return redirect()->route('roles.index');
        }

        $roleName = strtoupper($request->input('name'));
        if ($roleName === 'SUPERADMIN' && $role->name !== 'SUPERADMIN' && !in_array(auth()->user()->email, $allowedEmails)) {
            flash()->error('No tienes permiso para renombrar un rol a SUPERADMIN.');
            return redirect()->route('roles.index');
        }

        $this->validate($request, [
            'name' => 'required|unique:roles,name,' . $id,
            'permission' => 'required|array',
            'permission.*' => 'exists:permissions,name'
        ]);

        $permissions = $request->input('permission');

        // Validar permisos conflictivos solo si el rol no es SUPERADMIN
        if ($roleName !== 'SUPERADMIN') {
            $hasFiscalizacion = array_intersect($permissions, ['ver-fiscalizacion', 'crear-fiscalizacion', 'editar-fiscalizacion', 'borrar-fiscalizacion']);
            $hasFiscalizacionAll = array_intersect($permissions, ['ver-fiscalizacion-all', 'crear-fiscalizacion-all', 'editar-fiscalizacion-all', 'borrar-fiscalizacion-all']);
            if ($hasFiscalizacion && $hasFiscalizacionAll) {
                return redirect()->back()->withInput()->withErrors(['permission' => 'No se pueden asignar permisos de Fiscalización (Activos) y Fiscalización (Todos) al mismo rol.']);
            }
        }

        $role->name = $roleName;
        $role->save();
        $role->syncPermissions($permissions);

        Log::info('Rol actualizado', [
            'role' => $role->name,
            'permissions' => $permissions,
            'user_id' => auth()->user()->id
        ]);

        flash()->success('Rol actualizado exitosamente');

        return redirect()->route('roles.index');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $allowedEmails = ['argenis@mail.com', 'yasnier@mail.com'];

        // Solo Argenis y Yasnier (con SUPERADMIN) pueden eliminar el rol SUPERADMIN
        if ($role->name === 'SUPERADMIN' && !in_array(auth()->user()->email, $allowedEmails)) {
            flash()->error('No tienes permiso para eliminar el rol SUPERADMIN.');
            return redirect()->route('roles.index');
        }

        $role->delete();

        flash()->success('Rol eliminado exitosamente');

        return redirect()->route('roles.index');
    }
}
