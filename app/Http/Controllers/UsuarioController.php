<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;

class UsuarioController extends Controller
{
    public function index()
    {
        $roles = Role::pluck('name')->toArray();
        $usuarios = User::paginate(10);
        return view('usuarios.index', [
            'roles' => $roles,
            'usuarios' => $usuarios,
        ]);
    }

    public function create()
    {
        $roles = Role::pluck('name')->toArray();
        return response()->json(['roles' => $roles]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name'
        ]);

        $input = $request->all();
        $input['name'] = strtoupper($input['name']);
        $input['password'] = Hash::make($input['password']);

        $user = User::create($input);
        if ($request->has('roles')) {
            $user->assignRole($request->input('roles'));
        }

        return response()->json(['success' => true, 'message' => 'Usuario creado exitosamente']);
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::pluck('name')->toArray();
        $userRoles = $user->roles->pluck('name')->toArray();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $userRoles
            ],
            'available_roles' => $roles
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:8|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name'
        ]);

        $input = $request->all();
        $input['name'] = strtoupper($input['name']);
        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, ['password']);
        }

        $user = User::findOrFail($id);
        $user->update($input);
        DB::table('model_has_roles')->where('model_id', $id)->delete();
        if ($request->has('roles')) {
            $user->assignRole($request->input('roles'));
        }

        return response()->json(['success' => true, 'message' => 'Usuario actualizado exitosamente']);
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Usuario eliminado exitosamente']);
    }
}
