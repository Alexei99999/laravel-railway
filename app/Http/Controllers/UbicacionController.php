<?php

namespace App\Http\Controllers;

use App\Models\Ubicacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class UbicacionController extends Controller
{
    public function index()
    {
        Log::info('UbicacionController::index called');
        $statusubs = [
            (object) ['name' => 'Activo'],
            (object) ['name' => 'Inactivo']
        ];
        return view('ubicaciones.index', compact('statusubs'));
    }

    public function datatables()
    {
        try {
            Log::info('UbicacionController::datatables started');
            Log::info('Checking database connection');
            \DB::connection()->getPdo();
            Log::info('Database connection successful');
            Log::info('Querying ubicacions table');
            $ubicaciones = Ubicacion::select(['id', 'estado', 'cod_est', 'municipio', 'cod_mun', 'parroquia', 'cod_parroq', 'circuns', 'e_registro'])->get();
            Log::info('Ubicaciones fetched: ' . $ubicaciones->toJson());
            return response()->json(['data' => $ubicaciones]);
        } catch (\Exception $e) {
            Log::error('DataTables error: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine() . ' | Trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Error al cargar datos: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            Log::info('UbicacionController::show called for ID: ' . $id);
            $ubicacion = Ubicacion::findOrFail($id);
            Log::info('Ubicacion found: ' . $ubicacion->toJson());
            return response()->json($ubicacion);
        } catch (\Exception $e) {
            Log::error('Show error: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine() . ' | Trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Ubicación no encontrada'], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            Log::info('UbicacionController::store called with data: ' . json_encode($request->all()));
            $validator = Validator::make($request->all(), [
                'estado' => 'required|string|max:20',
                'cod_est' => 'nullable|string|max:2',
                'municipio' => 'required|string|max:100',
                'cod_mun' => 'nullable|string|max:2',
                'parroquia' => 'required|string|max:100',
                'cod_parroq' => 'nullable|string|max:2',
                'circuns' => 'required|string|max:2',
                'e_registro' => 'required|in:Activo,Inactivo'
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed: ' . json_encode($validator->errors()));
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $ubicacion = Ubicacion::create($request->all());
            Log::info('Ubicacion created: ' . $ubicacion->toJson());
            return response()->json(['message' => 'Ubicación creada exitosamente', 'ubicacion' => $ubicacion], 201);
        } catch (\Exception $e) {
            Log::error('Store error: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine() . ' | Trace: ' . $e->getTraceAsString());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        try {
            Log::info('UbicacionController::edit called for ID: ' . $id);
            $ubicacion = Ubicacion::findOrFail($id);
            Log::info('Ubicacion found: ' . $ubicacion->toJson());
            return response()->json($ubicacion);
        } catch (\Exception $e) {
            Log::error('Edit error: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine() . ' | Trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Ubicación no encontrada'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            Log::info('UbicacionController::update called for ID: ' . $id . ' with data: ' . json_encode($request->all()));
            $validator = Validator::make($request->all(), [
                'estado' => 'required|string|max:20',
                'cod_est' => 'nullable|string|max:2',
                'municipio' => 'required|string|max:100',
                'cod_mun' => 'nullable|string|max:2',
                'parroquia' => 'required|string|max:100',
                'cod_parroq' => 'nullable|string|max:2',
                'circuns' => 'required|string|max:2',
                'e_registro' => 'required|in:Activo,Inactivo'
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed: ' . json_encode($validator->errors()));
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $ubicacion = Ubicacion::findOrFail($id);
            $ubicacion->update($request->all());
            Log::info('Ubicacion updated: ' . $ubicacion->toJson());
            return response()->json(['message' => 'Ubicación actualizada exitosamente']);
        } catch (\Exception $e) {
            Log::error('Update error: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine() . ' | Trace: ' . $e->getTraceAsString());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            Log::info('UbicacionController::destroy called for ID: ' . $id);
            $ubicacion = Ubicacion::findOrFail($id);
            $ubicacion->update(['e_registro' => 'Inactivo']);
            Log::info('Ubicacion inactivated: ' . $ubicacion->toJson());
            return response()->json(['message' => 'Ubicación inactivada exitosamente']);
        } catch (\Exception $e) {
            Log::error('Destroy error: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine() . ' | Trace: ' . $e->getTraceAsString());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
