<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolController extends Controller
{
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
        return response()->json(['permissions' => $permission->pluck('name')]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required'
        ]);

        $role = Role::create(['name' => strtoupper($request->input('name'))]);
        $role->syncPermissions($request->input('permission'));

        return response()->json(['success' => true, 'message' => 'Rol creado exitosamente']);
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permission = Permission::get();
        $rolePermissions = DB::table('role_has_permissions')
            ->where('role_has_permissions.role_id', $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();

        return response()->json([
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->pluck('name')
            ],
            'available_permissions' => $permission->pluck('name')
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name,' . $id,
            'permission' => 'required'
        ]);

        $role = Role::findOrFail($id);
        $role->name = strtoupper($request->input('name'));
        $role->save();
        $role->syncPermissions($request->input('permission'));

        return response()->json(['success' => true, 'message' => 'Rol actualizado exitosamente']);
    }

    public function destroy($id)
    {
        DB::table('roles')->where('id', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Rol eliminado exitosamente']);
    }
}
