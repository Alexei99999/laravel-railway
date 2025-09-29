<?php

namespace App\Http\Controllers;

use App\Models\Trabajadores;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class TrabajadoresController extends Controller
{
    public function index()
    {
        $statustrabs = [
            (object) ['name' => 'Activo'],
            (object) ['name' => 'Inactivo']
        ];
        return view('trabajadores.index', compact('statustrabs'));
    }

    public function datatables()
    {
        try {
            $trabajadores = Trabajadores::select([
                'id',
                'cedula',
                'telefono',
                'e_mail',
                'rol',
                'e_registro',
            ])
            ->selectRaw("CONCAT(nombre1, ' ', COALESCE(nombre2, '')) as nombres")
            ->selectRaw("CONCAT(apellido1, ' ', COALESCE(apellido2, '')) as apellidos");

            return DataTables::of($trabajadores)->toJson();
        } catch (\Exception $e) {
            \Log::error('DataTables error: ' . $e->getMessage());
            return response()->json(['error' => 'Error al cargar datos: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'cedula' => 'required|string|max:8|unique:trabajadores',
                'telefono' => 'nullable|string|max:11',
                'apellido1' => 'required|string|max:20',
                'apellido2' => 'nullable|string|max:20',
                'nombre1' => 'required|string|max:15',
                'nombre2' => 'nullable|string|max:15',
                'e_mail' => 'required|email|max:50',
                'rol' => 'required|string|max:21',
                'e_registro' => 'required|string|max:8|in:Activo,Inactivo',
            ]);

            Trabajadores::create($request->all());

            return response()->json(['success' => true, 'message' => 'Trabajador creado exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        try {
            $trabajador = Trabajadores::findOrFail($id);
            return response()->json($trabajador);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Trabajador no encontrado'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'cedula' => 'required|string|max:8|unique:trabajadores,cedula,' . $id,
                'telefono' => 'nullable|string|max:11',
                'apellido1' => 'required|string|max:20',
                'apellido2' => 'nullable|string|max:20',
                'nombre1' => 'required|string|max:15',
                'nombre2' => 'nullable|string|max:15',
                'e_mail' => 'required|email|max:50',
                'rol' => 'required|string|max:21',
                'e_registro' => 'required|string|max:8|in:Activo,Inactivo',
            ]);

            $trabajador = Trabajadores::findOrFail($id);
            $trabajador->update($request->all());

            return response()->json(['success' => true, 'message' => 'Trabajador actualizado exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $trabajador = Trabajadores::findOrFail($id);
            $trabajador->update(['e_registro' => 'Inactivo']);

            return response()->json(['success' => true, 'message' => 'Trabajador inactivado exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
