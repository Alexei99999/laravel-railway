<?php

namespace App\Http\Controllers;

use App\Models\Fiscalizacion;
use App\Models\IncidenciasFiscalizacion;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

use App\Exports\IncidenciasFiscalizacionesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class IncidenciasFiscalizacionController extends Controller
{
    public function index()
    {
        return view('incidencias_fiscalizaciones.index');
    }

    public function datatables()
    {
        $incidencias = IncidenciasFiscalizacion::with('fiscalizacion')->select('incidencias_fiscalizacions.*');

        return Datatables::of($incidencias)
            ->addColumn('cedula', function ($incidencia) {
                return $incidencia->cedula ?: 'N/A';
            })
            ->addColumn('trabajador', function ($incidencia) {
                return $incidencia->fiscalizacion ? $incidencia->fiscalizacion->nombres . ' ' . $incidencia->fiscalizacion->apellidos : 'N/A';
            })
            ->addColumn('ubicacion', function ($incidencia) {
                return $incidencia->ubicacion ?: 'N/A';
            })
            ->addColumn('contacto', function ($incidencia) {
                return $incidencia->contacto ?: 'N/A';
            })
            ->addColumn('fecha_reporte', function ($incidencia) {
                return $incidencia->fecha_incidencia ? Carbon::parse($incidencia->fecha_incidencia)->format('d/m/y') : 'N/A';
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

    public function create()
    {
        return response()->json(['success' => true]);
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'cedula' => [
                    'required',
                    'regex:/^\d{7,8}$/',
                    function ($attribute, $value, $fail) {
                        if (!Fiscalizacion::where('cedula', $value)->where('e_registro', 'Activo')->exists()) {
                            $fail('La cédula seleccionada no corresponde a un trabajador activo.');
                        }
                    }
                ],
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

            $fiscalizacion = Fiscalizacion::where('cedula', $request->input('cedula'))->where('e_registro', 'Activo')->firstOrFail();
            $incidencia = new IncidenciasFiscalizacion();
            $incidencia->fiscalizacion_id = $fiscalizacion->id;
            $incidencia->cedula = $fiscalizacion->cedula;
            $incidencia->trabajador = $fiscalizacion->nombres . ' ' . $fiscalizacion->apellidos;
            $incidencia->ubicacion = $fiscalizacion->nombre_pto ?? 'N/A';
            $incidencia->contacto = $fiscalizacion->telefono ?? 'N/A';
            $incidencia->incidencia = $request->input('incidencia');
            $incidencia->fecha_incidencia = Carbon::createFromFormat('d/m/y', $request->input('fecha_incidencia'))->toDateString();
            $incidencia->hora_incidencia = $request->input('hora_incidencia');
            $incidencia->save();

            $latestIncidencia = IncidenciasFiscalizacion::where('fiscalizacion_id', $fiscalizacion->id)
                ->orderBy('created_at', 'desc')
                ->first();
            $fiscalizacion->update([
                'incidencia' => $latestIncidencia ? $latestIncidencia->incidencia : null,
                'fecha_incidencia' => $latestIncidencia ? $latestIncidencia->fecha_incidencia : null,
                'hora_incidencia' => $latestIncidencia ? $latestIncidencia->hora_incidencia : null,
            ]);

            return response()->json([
                'message' => 'Incidencia creada con éxito.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating incidencia: ' + $e->getMessage());
            return response()->json([
                'error' => 'Error al crear la incidencia.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(IncidenciasFiscalizacion $incidenciasFiscalizacion)
    {
        try {
            $data = [
                'id' => $incidenciasFiscalizacion->id,
                'cedula' => $incidenciasFiscalizacion->cedula ?? 'N/A',
                'trabajador' => $incidenciasFiscalizacion->trabajador ?? 'N/A',
                'ubicacion' => $incidenciasFiscalizacion->ubicacion ?? 'N/A',
                'contacto' => $incidenciasFiscalizacion->contacto ?? 'N/A',
                'incidencia' => $incidenciasFiscalizacion->incidencia ?? 'N/A',
                'fecha_incidencia' => $incidenciasFiscalizacion->fecha_incidencia ? Carbon::parse($incidenciasFiscalizacion->fecha_incidencia)->format('d/m/y') : 'N/A',
                'hora_incidencia' => $incidenciasFiscalizacion->hora_incidencia ?? 'N/A',
            ];

            return response()->json(['data' => $data]);
        } catch (\Exception $e) {
            Log::error('Error showing incidencia: ' + $e->getMessage());
            return response()->json([
                'error' => 'Error al cargar los datos de la incidencia.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function edit(IncidenciasFiscalizacion $incidenciasFiscalizacion)
    {
        return response()->json([
            'data' => $incidenciasFiscalizacion,
        ]);
    }

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

            $incidencia = IncidenciasFiscalizacion::findOrFail($id);
            $incidencia->incidencia = $request->input('incidencia');
            $incidencia->fecha_incidencia = Carbon::createFromFormat('d/m/y', $request->input('fecha_incidencia'))->toDateString();
            $incidencia->hora_incidencia = $request->input('hora_incidencia');
            $incidencia->save();

            $latestIncidencia = IncidenciasFiscalizacion::where('fiscalizacion_id', $incidencia->fiscalizacion_id)
                ->orderBy('created_at', 'desc')
                ->first();
            $fiscalizacion = Fiscalizacion::find($incidencia->fiscalizacion_id);
            if ($fiscalizacion) {
                $fiscalizacion->update([
                    'incidencia' => $latestIncidencia ? $latestIncidencia->incidencia : null,
                    'fecha_incidencia' => $latestIncidencia ? $latestIncidencia->fecha_incidencia : null,
                    'hora_incidencia' => $latestIncidencia ? $latestIncidencia->hora_incidencia : null,
                ]);
            }

            return response()->json([
                'message' => 'Incidencia actualizada con éxito.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating incidencia: ' + $e->getMessage());
            return response()->json([
                'error' => 'Error al actualizar la incidencia.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(IncidenciasFiscalizacion $incidenciasFiscalizacion)
    {
        try {
            $fiscalizacion_id = $incidenciasFiscalizacion->fiscalizacion_id;
            $incidenciasFiscalizacion->delete();

            $latestIncidencia = IncidenciasFiscalizacion::where('fiscalizacion_id', $fiscalizacion_id)
                ->orderBy('created_at', 'desc')
                ->first();
            $fiscalizacion = Fiscalizacion::find($fiscalizacion_id);
            if ($fiscalizacion) {
                $fiscalizacion->update([
                    'incidencia' => $latestIncidencia ? $latestIncidencia->incidencia : null,
                    'fecha_incidencia' => $latestIncidencia ? $latestIncidencia->fecha_incidencia : null,
                    'hora_incidencia' => $latestIncidencia ? $latestIncidencia->hora_incidencia : null,
                ]);
            }

            return response()->json([
                'message' => 'Incidencia eliminada exitosamente.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting incidencia: ' + $e->getMessage());
            return response()->json([
                'error' => 'Error al eliminar la incidencia.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function getFiscalizacionByCedula(Request $request)
    {
        $request->validate([
            'cedula' => 'required|string|regex:/^\d{7,8}$/',
        ]);

        $fiscalizacion = Fiscalizacion::where('cedula', $request->cedula)->first();

        if (!$fiscalizacion) {
            return response()->json([
                'exists' => false,
                'message' => 'No se encontró un registro con esta cédula.',
            ], 404);
        }

        return response()->json([
            'exists' => true,
            'data' => [
                'cedula' => $fiscalizacion->cedula,
                'trabajador' => $fiscalizacion->nombres . ' ' . $fiscalizacion->apellidos,
                'ubicacion' => $fiscalizacion->nombre_pto ?? 'N/A',
                'contacto' => $fiscalizacion->telefono ?? 'N/A',
            ],
            'message' => 'Registro encontrado.',
        ]);
    }

    public function getActiveWorkers()
    {
        try {
            $workers = Fiscalizacion::where('e_registro', 'Activo')
                ->select('id', 'cedula', 'nombres', 'apellidos', 'nombre_pto', 'telefono', 'e_registro')
                ->get();

            if ($workers->isEmpty()) {
                return response()->json([
                    'results' => [],
                    'pagination' => ['more' => false],
                ]);
            }

            $formattedWorkers = $workers->map(function ($fiscalizacion) {
                $ubicacion = $fiscalizacion->nombre_pto ?? 'Sin ubicación';
                return [
                    'id' => $fiscalizacion->cedula,
                    'text' => $fiscalizacion->cedula . ' - ' . $fiscalizacion->nombres . ' ' . $fiscalizacion->apellidos . ' - ' . $ubicacion,
                    'cedula' => $fiscalizacion->cedula,
                    'trabajador' => $fiscalizacion->nombres . ' ' . $fiscalizacion->apellidos,
                    'ubicacion' => $ubicacion,
                    'contacto' => $fiscalizacion->telefono ?? 'Sin contacto',
                ];
            });

            return response()->json([
                'results' => $formattedWorkers,
                'pagination' => ['more' => false],
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching active workers: ' + $e->getMessage());
            return response()->json([
                'error' => 'Error al cargar los trabajadores activos.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

public function exportTodo(Request $request)
{
    $format = $request->input('format');
    $timestamp = date('Ymd_His');
    $extension = $format === 'csv' ? 'csv' : 'xlsx';
    $fileName = 'incidencias_fiscalizaciones_' . $timestamp . '.' . $extension;

    $export = new IncidenciasFiscalizacionesExport();

    $type = $format === 'csv' ? \Maatwebsite\Excel\Excel::CSV : \Maatwebsite\Excel\Excel::XLSX;

    return Excel::download($export, $fileName, $type);
}
}
