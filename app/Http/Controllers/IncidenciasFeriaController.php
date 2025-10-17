<?php

namespace App\Http\Controllers;

use App\Models\Feria;
use App\Models\IncidenciasFeria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables; // Importar la clase Datatables
use Carbon\Carbon;

use App\Exports\IncidenciasFeriasExport;
use App\Exports\TotalIncidenciasFeriasExport;
use Maatwebsite\Excel\Facades\Excel;

class IncidenciasFeriaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('incidencias_ferias.index');
    }

    /**
     * Return data for DataTables.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function datatables()
    {
        $incidencias = IncidenciasFeria::with('feria')->select('incidencias_ferias.*');

        return Datatables::of($incidencias)
            ->addColumn('cedula', function ($incidencia) {
                return $incidencia->cedula ?: 'N/A';
            })
            ->addColumn('trabajador', function ($incidencia) {
                return $incidencia->feria ? $incidencia->feria->nombres . ' ' . $incidencia->feria->apellidos : 'N/A';
            })
            ->addColumn('ubicacion', function ($incidencia) {
                return $incidencia->ubicacion ?: 'N/A';
            })
            ->addColumn('contacto', function ($incidencia) {
                return $incidencia->feria ? $incidencia->feria->telefono : 'N/A';
            })
            ->addColumn('fecha_reporte', function ($incidencia) {
                return $incidencia->fecha_incidencia ?: 'N/A';
            })
            ->addColumn('estado', function ($incidencia) {
                return $incidencia->incidencia ?: 'N/A';
            })
            ->addColumn('fecha_estado', function ($incidencia) {
                return $incidencia->hora_incidencia ?: 'N/A';
            })
            ->rawColumns(['cedula', 'trabajador', 'ubicacion', 'contacto', 'fecha_reporte', 'estado', 'fecha_estado'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return response()->json(['success' => true]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'cedula' => ['required', 'regex:/^\d{7,8}$/'],
                'incidencia' => ['required', 'string', 'max:300'],
                'fecha_incidencia' => ['required', 'date_format:d/m/y'],
                'hora_incidencia' => ['required', 'date_format:H:i'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Errores de validación',
                    'details' => $validator->errors()->toArray(),
                ], 422);
            }

            $feria = Feria::where('cedula', $request->input('cedula'))->firstOrFail();
            $incidencia = new IncidenciasFeria();
            $incidencia->feria_id = $feria->id;
            $incidencia->cedula = $request->input('cedula');
            $incidencia->incidencia = $request->input('incidencia');
            $incidencia->fecha_incidencia = Carbon::createFromFormat('d/m/y', $request->input('fecha_incidencia'))->format('Y-m-d');
            $incidencia->hora_incidencia = $request->input('hora_incidencia');
            $incidencia->save();

            // Actualizar la tabla ferias con la última incidencia
            $latestIncidencia = IncidenciasFeria::where('feria_id', $feria->id)
                ->orderBy('created_at', 'desc')
                ->first();
            $feria->update([
                'incidencias' => $latestIncidencia ? $latestIncidencia->incidencia : null,
                'fecha_incidencia' => $latestIncidencia ? $latestIncidencia->fecha_incidencia : null,
                'hora_incidencia' => $latestIncidencia ? $latestIncidencia->hora_incidencia : null,
            ]);

            return response()->json([
                'message' => 'Incidencia creada con éxito.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating incidencia: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al crear la incidencia.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\IncidenciasFeria  $incidenciasFeria
     * @return \Illuminate\Http\Response
     */
    public function edit(IncidenciasFeria $incidenciasFeria)
    {
        return response()->json([
            'data' => $incidenciasFeria,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\IncidenciasFeria  $incidenciasFeria
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'incidencia' => ['required', 'string', 'max:300'],
                'fecha_incidencia' => ['required', 'date_format:d/m/y'],
                'hora_incidencia' => ['required', 'date_format:H:i'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Errores de validación',
                    'details' => $validator->errors()->toArray(),
                ], 422);
            }

            $incidencia = IncidenciasFeria::findOrFail($id);
            $incidencia->incidencia = $request->input('incidencia');
            $incidencia->fecha_incidencia = Carbon::createFromFormat('d/m/y', $request->input('fecha_incidencia'))->format('Y-m-d');
            $incidencia->hora_incidencia = $request->input('hora_incidencia');
            $incidencia->save();

            // Actualizar la tabla ferias con la última incidencia
            $latestIncidencia = IncidenciasFeria::where('feria_id', $incidencia->feria_id)
                ->orderBy('created_at', 'desc')
                ->first();
            $feria = Feria::find($incidencia->feria_id);
            if ($feria) {
                $feria->update([
                    'incidencias' => $latestIncidencia ? $latestIncidencia->incidencia : null,
                    'fecha_incidencia' => $latestIncidencia ? $latestIncidencia->fecha_incidencia : null,
                    'hora_incidencia' => $latestIncidencia ? $latestIncidencia->hora_incidencia : null,
                ]);
            }

            return response()->json([
                'message' => 'Incidencia actualizada con éxito.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating incidencia: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al actualizar la incidencia.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(IncidenciasFeria $incidenciasFeria)
    {
        try {
            $data = [
                'id' => $incidenciasFeria->id,
                'cedula' => $incidenciasFeria->feria ? $incidenciasFeria->feria->cedula : 'N/A',
                'trabajador' => $incidenciasFeria->trabajador ?: 'N/A',
                'ubicacion' => $incidenciasFeria->ubicacion ?: 'N/A',
                'contacto' => $incidenciasFeria->feria ? $incidenciasFeria->feria->telefono : 'N/A',
                'incidencia' => $incidenciasFeria->incidencia ?: 'N/A',
                'fecha_incidencia' => $incidenciasFeria->fecha_incidencia ?: 'N/A',
                'hora_incidencia' => $incidenciasFeria->hora_incidencia ?: 'N/A',
            ];

            return response()->json(['data' => $data]);
        } catch (\Exception $e) {
            Log::error('Error showing incidencia: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al cargar los datos de la incidencia.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\IncidenciasFeria  $incidenciasFeria
     * @return \Illuminate\Http\Response
     */
    public function destroy(IncidenciasFeria $incidenciasFeria)
    {
        try {
            $feria_id = $incidenciasFeria->feria_id;
            $incidenciasFeria->delete();

            // Update ferias table with the latest incident
            $latestIncidencia = IncidenciasFeria::where('feria_id', $feria_id)
                ->orderBy('created_at', 'desc')
                ->first();

            $feria = Feria::find($feria_id);
            if ($feria) {
                $feria->update([
                    'incidencias' => $latestIncidencia ? $latestIncidencia->incidencia : null,
                    'fecha_incidencia' => $latestIncidencia ? $latestIncidencia->fecha_incidencia : null,
                    'hora_incidencia' => $latestIncidencia ? $latestIncidencia->hora_incidencia : null,
                ]);
            }

            return response()->json([
                'message' => 'Incidencia eliminada exitosamente.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting incidencia: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al eliminar la incidencia.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Fetch Feria data by cedula.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFeriaByCedula(Request $request)
    {
        $request->validate([
            'cedula' => 'required|string|regex:/^\d{7,8}$/',
        ]);

        $feria = Feria::where('cedula', $request->cedula)->first();

        if (!$feria) {
            return response()->json([
                'exists' => false,
                'message' => 'No se encontró un registro con esta cédula.',
            ], 404);
        }

        return response()->json([
            'exists' => true,
            'data' => [
                'cedula' => $feria->cedula,
                'trabajador' => $feria->nombres . ' ' . $feria->apellidos,
                'ubicacion' => $feria->nombre_pto ?? $feria->rectoria ?? 'Sin ubicación',
                'contacto' => $feria->telefono,
            ],
            'message' => 'Registro encontrado.',
        ]);
    }

    /**
     * Fetch active workers for select dropdown.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActiveWorkers()
    {
        try {
            Log::info('Fetching active workers with e_registro = Activo');
            $workers = Feria::where('e_registro', 'Activo') // Match the enum value
                ->select('id', 'cedula', 'nombres', 'apellidos', 'nombre_pto', 'rectoria', 'telefono', 'e_registro')
                ->get();

            Log::info('Found ' . $workers->count() . ' active workers');
            foreach ($workers as $worker) {
                Log::debug('Worker: ' . $worker->cedula . ', e_registro: ' . $worker->e_registro); // Log each worker's status
            }

            if ($workers->isEmpty()) {
                Log::warning('No active workers found with e_registro = Activo');
                return response()->json([
                    'results' => [],
                    'pagination' => ['more' => false],
                ]);
            }

            $formattedWorkers = $workers->map(function ($feria) {
                $ubicacion = $feria->nombre_pto ?? $feria->rectoria ?? 'Sin ubicación';
                Log::debug('Formatting worker: ' . $feria->cedula . ' - ' . $feria->nombres . ' ' . $feria->apellidos . ' - ' . $ubicacion);
                return [
                    'id' => $feria->cedula,
                    'text' => $feria->cedula . ' - ' . $feria->nombres . ' ' . $feria->apellidos . ' - ' . $ubicacion,
                    'trabajador' => $feria->nombres . ' ' . $feria->apellidos,
                    'ubicacion' => $ubicacion,
                    'contacto' => $feria->telefono,
                ];
            });

            Log::info('Returning ' . $formattedWorkers->count() . ' formatted workers');
            return response()->json([
                'results' => $formattedWorkers,
                'pagination' => ['more' => false],
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching active workers: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al cargar los trabajadores activos.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }


 public function exportTodo()
    {
        $fileName = 'incidencias_ferias_totales_' . date('Ymd') . '.xlsx';
        return Excel::download(new TotalIncidenciasFeriasExport, $fileName);
    }
}
