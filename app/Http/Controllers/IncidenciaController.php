<?php

namespace App\Http\Controllers;

use App\Models\Incidencia;
use App\Models\Trabajadores;
use App\Models\Ubicacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class IncidenciaController extends Controller
{
    public function index()
    {
        return view('incidencias.index');
    }

    public function datatables()
    {
        Log::info('IncidenciaController::datatables started');
        Log::info('Checking database connection');
        try {
            DB::connection()->getPdo();
            Log::info('Database connection successful');
        } catch (\Exception $e) {
            Log::error('Database connection failed: ' . $e->getMessage());
            return response()->json(['error' => 'Database connection failed'], 500);
        }

        Log::info('Querying incidencias table');
        try {
            $incidencias = Incidencia::select(['id', 'trabajador', 'ubicacion', 'e_contact', 'fecha_rep', 'e_disponib', 'fecha_e_disponib'])
                ->get()
                ->map(function ($incidencia) {
                    return [
                        'id' => $incidencia->id,
                        'trabajador' => $incidencia->trabajador,
                        'ubicacion' => $incidencia->ubicacion,
                        'e_contact' => $incidencia->e_contact,
                        'fecha_rep' => $incidencia->fecha_rep ? Carbon::parse($incidencia->fecha_rep)->format('d/m/y') : null,
                        'e_disponib' => $incidencia->e_disponib,
                        'fecha_e_disponib' => $incidencia->fecha_e_disponib ? Carbon::parse($incidencia->fecha_e_disponib)->format('d/m/y') : null
                    ];
                });
            Log::info('Incidencias fetched: ' . json_encode($incidencias));
            return response()->json(['data' => $incidencias]);
        } catch (\Exception $e) {
            Log::error('Error querying incidencias: ' . $e->getMessage());
            return response()->json(['error' => 'Error querying incidencias'], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'trabajador' => 'required|string|max:100',
            'ubicacion' => 'required|string|max:150',
            'e_contact' => 'required|in:Contactado,No contactado',
            'fecha_rep' => 'required|date_format:d/m/y',
            'e_disponib' => 'required|in:Trabaja,No trabaja,Sin información',
            'fecha_e_disponib' => 'nullable|date_format:d/m/y'
        ]);

        try {
            // Convert DD/MM/YY to YYYY-MM-DD for storage
            $validated['fecha_rep'] = Carbon::createFromFormat('d/m/y', $validated['fecha_rep'])->format('Y-m-d');
            if ($validated['fecha_e_disponib']) {
                $validated['fecha_e_disponib'] = Carbon::createFromFormat('d/m/y', $validated['fecha_e_disponib'])->format('Y-m-d');
            }
            $incidencia = Incidencia::create($validated);
            return response()->json(['message' => 'Incidencia creada exitosamente', 'data' => $incidencia], 201);
        } catch (\Exception $e) {
            Log::error('Error creating incidencia: ' . $e->getMessage());
            return response()->json(['error' => 'Error creating incidencia'], 500);
        }
    }

    public function edit($id)
    {
        Log::info('IncidenciaController::edit called with ID: ' . $id);
        try {
            $incidencia = Incidencia::findOrFail($id);
            $trabajadores = Trabajadores::select('id', 'cedula', 'nombre1', 'nombre2', 'apellido1', 'apellido2')
                ->get()
                ->map(function ($trabajador) {
                    $nombreCompleto = trim("{$trabajador->nombre1} {$trabajador->nombre2} {$trabajador->apellido1} {$trabajador->apellido2} - {$trabajador->cedula}");
                    return ['id' => $trabajador->id, 'nombre' => $nombreCompleto];
                });
            $ubicaciones = Ubicacion::select('id', 'estado')->get()->map(function ($ubicacion) {
                return ['id' => $ubicacion->id, 'nombre' => $ubicacion->estado];
            });
            return response()->json([
                'incidencia' => [
                    'id' => $incidencia->id,
                    'trabajador' => $incidencia->trabajador,
                    'ubicacion' => $incidencia->ubicacion,
                    'e_contact' => $incidencia->e_contact,
                    'fecha_rep' => $incidencia->fecha_rep ? Carbon::parse($incidencia->fecha_rep)->format('d/m/y') : null,
                    'e_disponib' => $incidencia->e_disponib,
                    'fecha_e_disponib' => $incidencia->fecha_e_disponib ? Carbon::parse($incidencia->fecha_e_disponib)->format('d/m/y') : null
                ],
                'trabajadores' => $trabajadores,
                'ubicaciones' => $ubicaciones
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching incidencia: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching incidencia'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $incidencia = Incidencia::findOrFail($id);
            $validated = $request->validate([
                'trabajador' => 'required|string|max:100',
                'ubicacion' => 'required|string|max:150',
                'e_contact' => 'required|in:Contactado,No contactado',
                'fecha_rep' => 'required|date_format:d/m/y',
                'e_disponib' => 'required|in:Trabaja,No trabaja,Sin información',
                'fecha_e_disponib' => 'nullable|date_format:d/m/y'
            ]);

            // Convert DD/MM/YY to YYYY-MM-DD for storage
            $validated['fecha_rep'] = Carbon::createFromFormat('d/m/y', $validated['fecha_rep'])->format('Y-m-d');
            if ($validated['fecha_e_disponib']) {
                $validated['fecha_e_disponib'] = Carbon::createFromFormat('d/m/y', $validated['fecha_e_disponib'])->format('Y-m-d');
            }

            $incidencia->update($validated);
            return response()->json(['message' => 'Incidencia actualizada exitosamente', 'data' => $incidencia]);
        } catch (\Exception $e) {
            Log::error('Error updating incidencia: ' . $e->getMessage());
            return response()->json(['error' => 'Error updating incidencia'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $incidencia = Incidencia::findOrFail($id);
            $incidencia->update(['e_disponib' => 'No trabaja']);
            return response()->json(['message' => 'Incidencia inactivada exitosamente']);
        } catch (\Exception $e) {
            Log::error('Error deleting incidencia: ' . $e->getMessage());
            return response()->json(['error' => 'Error deleting incidencia'], 500);
        }
    }

    public function show($id)
    {
        Log::info('IncidenciaController::show called with ID: ' . $id);
        try {
            $incidencia = Incidencia::findOrFail($id);
            return response()->json([
                'id' => $incidencia->id,
                'trabajador' => $incidencia->trabajador,
                'ubicacion' => $incidencia->ubicacion,
                'e_contact' => $incidencia->e_contact,
                'fecha_rep' => $incidencia->fecha_rep ? Carbon::parse($incidencia->fecha_rep)->format('d/m/y') : null,
                'e_disponib' => $incidencia->e_disponib,
                'fecha_e_disponib' => $incidencia->fecha_e_disponib ? Carbon::parse($incidencia->fecha_e_disponib)->format('d/m/y') : null
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching incidencia: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching incidencia'], 404);
        }
    }

    public function getOptions()
    {
        Log::info('IncidenciaController::getOptions called');
        try {
            $trabajadores = Trabajadores::select('id', 'cedula', 'nombre1', 'nombre2', 'apellido1', 'apellido2')
                ->get()
                ->map(function ($trabajador) {
                    $nombreCompleto = trim("{$trabajador->nombre1} {$trabajador->nombre2} {$trabajador->apellido1} {$trabajador->apellido2} - {$trabajador->cedula}");
                    return ['id' => $trabajador->id, 'nombre' => $nombreCompleto];
                });
            $ubicaciones = Ubicacion::select('id', 'estado')->get()->map(function ($ubicacion) {
                return ['id' => $ubicacion->id, 'nombre' => $ubicacion->estado];
            });
            return response()->json([
                'trabajadores' => $trabajadores,
                'ubicaciones' => $ubicaciones
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching options: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching options'], 500);
        }
    }
}
