<?php

namespace App\Http\Controllers;

use App\Models\Feria;
use App\Imports\FeriasImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use OwenIt\Auditing\Models\Audit;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

use App\Exports\FeriasExport;
use App\Exports\TotalAgentesDeFeriasExport;

    class FeriaController extends Controller
    {
        public function __construct()
        {
            $this->middleware('permission:ver-ferias-all|crear-ferias-all|editar-ferias-all|borrar-ferias-all', ['only' => ['indexAll']]);
            $this->middleware('permission:crear-ferias-all', ['only' => ['create', 'store', 'storeMassive']]);
            $this->middleware('permission:editar-ferias-all', ['only' => ['edit', 'update']]);
            $this->middleware('permission:borrar-ferias-all', ['only' => ['destroy']]);
            $this->middleware('permission:ver-ferias|crear-ferias|editar-ferias|borrar-ferias', ['only' => ['index']]);
            $this->middleware('permission:crear-ferias', ['only' => ['create', 'store', 'storeMassive']]);
            $this->middleware('permission:editar-ferias', ['only' => ['edit', 'update']]);
            $this->middleware('permission:borrar-ferias', ['only' => ['destroy']]);
        }

        public function index()
        {
            $ferias = Feria::where('e_registro', 'Activo')->get();
            return view('ferias.index', compact('ferias'));
        }

        public function indexAll()
    {
        try {
            $ferias = Feria::all();
            return view('ferias.index-all', compact('ferias'));
        } catch (\Exception $e) {
            Log::error('FeriaController::indexAll error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Error al cargar la vista de todas las ferias', 'details' => $e->getMessage()], 500);
        }
    }

        public function datatables()
        {
            $query = Feria::where('e_registro', 'Activo')->select([
                'id', 'cod_edo', 'estado', 'cod_mun', 'municipio', 'cod_parroquia',
                'parroquia', 'cod_centro', 'nombre_pto', 'direccion_pto', 'rectoria',
                'cedula', 'apellidos', 'nombres', 'telefono', 'correo', 'rol',
                'status_contact1', 'fecha_hora1', 'status_contact2', 'fecha_hora2',
                'status_contact3', 'fecha_hora3', 'disponibilidad', 'incidencias',
                'fecha_incidencia', 'hora_incidencia', 'observaciones', 'e_registro'
            ]);

            return Datatables::of($query)
                ->addColumn('action', function ($feria) {
                    return '<button class="btn btn-sm btn-primary edit-btn" data-id="' . $feria->id . '">Editar</button>';
                })
                ->editColumn('correo', function ($feria) {
                    return $feria->correo ?? 'N/A';
                })
                ->editColumn('e_registro', function ($feria) {
                    return $feria->e_registro;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        public function datatablesAll()
    {
        try {
            $query = Feria::select([
                'id', 'cod_edo', 'estado', 'cod_mun', 'municipio', 'cod_parroquia',
                'parroquia', 'cod_centro', 'nombre_pto', 'direccion_pto', 'rectoria',
                'cedula', 'apellidos', 'nombres', 'telefono', 'correo', 'rol',
                'status_contact1', 'fecha_hora1', 'status_contact2', 'fecha_hora2',
                'status_contact3', 'fecha_hora3', 'disponibilidad', 'incidencias',
                'fecha_incidencia', 'hora_incidencia', 'observaciones', 'e_registro'
            ]);

            return Datatables::of($query)
                ->addColumn('action', function ($feria) {
                    return '<button class="btn btn-sm btn-primary edit-btn" data-id="' . $feria->id . '">Editar</button>';
                })
                ->editColumn('correo', function ($feria) {
                    return $feria->correo ?? 'N/A';
                })
                ->rawColumns(['action'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('FeriaController::datatablesAll error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Error al cargar todos los datos de ferias', 'details' => $e->getMessage()], 500);
        }
    }

        public function storeMassive(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:csv,txt,xls,xlsx|max:5120',
            ]);

            $file = $request->file('file');
            $import = new FeriasImport();
            Excel::import($import, $file);

            $summary = $import->getSummary();
            $duplicatesInFile = $import->getDuplicatesInFile();
            $duplicatesInDB = $import->getDuplicatesInDB();
            $failedRows = $import->getFailedRows();

            if (!empty($duplicatesInFile)) {
                $duplicateList = implode(', ', $duplicatesInFile);
                return response()->json([
                    'message' => 'Algunas cédulas están repetidas en el archivo y no se procesaron: ' . $duplicateList,
                    'summary' => $summary,
                ], 200);
            }

            if (!empty($duplicatesInDB)) {
                $duplicateList = implode(', ', $duplicatesInDB);
                return response()->json([
                    'message' => 'Algunas cédulas ya están registradas y no se procesaron: ' . $duplicateList,
                    'summary' => $summary,
                ], 200);
            }

            return response()->json([
                'message' => 'Importación completada. ' . $summary['imported'] . ' registros importados, ' . $summary['failed'] . ' fallidos.',
                'summary' => $summary,
                'success_rows' => array_map(function($row) use ($import) {
                    return $row->toArray();
                }, $import->getSuccessfulModels() ?? []),
                'failed_rows' => $failedRows,
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Errores de validación', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error in FeriaController::storeMassive: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Error al procesar la importación masiva', 'details' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $feria = Feria::findOrFail($id);
            return response()->json(['feria' => $feria]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Feria no encontrada', 'details' => $e->getMessage()], 404);
        }
    }

        public function create()
        {
            return view('ferias.create');
        }

        public function store(Request $request)
        {
            try {
                $validated = $request->validate([
                    'estado' => 'required|string|max:255',
                    'municipio' => 'required|string|max:255',
                    'parroquia' => 'required|string|max:255',
                    'nombre_pto' => 'required|string|max:255',
                    'cedula' => 'required|string|regex:/^\d{7,8}$/|max:20|unique:ferias,cedula',
                    'apellidos' => 'required|string|max:255',
                    'nombres' => 'required|string|max:255',
                    'telefono' => 'required|string|regex:/^\d{10,11}$/|max:20',
                    'correo' => 'nullable|email|max:255',
                    'rectoria' => 'required|string|max:50',
                    'e_registro' => 'required|in:Activo,Inactivo',
                ], [
                    'estado.required' => 'El estado es obligatorio.',
                    'municipio.required' => 'El municipio es obligatorio.',
                    'parroquia.required' => 'La parroquia es obligatoria.',
                    'nombre_pto.required' => 'El nombre del punto es obligatorio.',
                    'cedula.required' => 'La cédula es obligatoria.',
                    'cedula.regex' => 'La cédula debe ser un número de 7 u 8 dígitos.',
                    'cedula.unique' => 'La cédula ya está registrada.',
                    'apellidos.required' => 'Los apellidos son obligatorios.',
                    'nombres.required' => 'Los nombres son obligatorios.',
                    'telefono.required' => 'El teléfono es obligatorio.',
                    'telefono.regex' => 'El teléfono debe ser un número de 10 u 11 dígitos.',
                    'rectoria.required' => 'La rectoría es obligatoria.',
                    'e_registro.required' => 'El estado registro es obligatorio.',
                    'e_registro.in' => 'El estado registro debe ser Activo o Inactivo.',
                ]);

                Log::info('FeriaController::store validated data:', $validated);

                $feria = Feria::create(array_merge($validated, [
                    'rectoria' => $validated['rectoria'] ?? 'ACME NOGAL',
                    'e_registro' => $validated['e_registro'] ?? 'Activo',
                ]));

                if (class_exists(\OwenIt\Auditing\Models\Audit::class) && method_exists($feria, 'audits')) {
                    try {
                        Audit::create([
                            'user_id' => auth()->id() ?? null,
                            'event' => 'created',
                            'auditable_type' => Feria::class,
                            'auditable_id' => $feria->id,
                            'old_values' => [],
                            'new_values' => $feria->getAttributes(),
                            'url' => $request->fullUrl(),
                            'ip_address' => $request->ip(),
                            'user_agent' => $request->userAgent(),
                            'created_at' => now(),
                        ]);
                        Log::info('Audit created for feria:', ['id' => $feria->id]);
                    } catch (\Exception $auditException) {
                        Log::warning('Failed to create audit for feria: ' . $auditException->getMessage(), ['id' => $feria->id]);
                    }
                }

                Log::info('Feria created successfully:', ['id' => $feria->id]);

                return response()->json([
                    'message' => 'Feria creada exitosamente',
                    'details' => [
                        'cedula' => $feria->cedula,
                        'nombres' => $feria->nombres,
                        'apellidos' => $feria->apellidos,
                        'e_registro' => $feria->e_registro,
                    ]
                ], 201);
            } catch (\Illuminate\Validation\ValidationException $e) {
                Log::error('Validation error in FeriaController::store: ' . json_encode($e->errors()), ['trace' => $e->getTraceAsString()]);
                return response()->json(['error' => 'Errores de validación', 'details' => $e->errors()], 422);
            } catch (\Exception $e) {
                Log::error('Error creating feria: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
                return response()->json(['error' => 'Error al crear la feria', 'details' => $e->getMessage()], 500);
            }
        }



        public function edit($id)
        {
            try {
                $feria = Feria::findOrFail($id);
                return response()->json(['feria' => $feria]);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Feria no encontrada', 'details' => $e->getMessage()], 404);
            }
        }

        public function update(Request $request, $feria)
        {
            try {
                $feria = Feria::findOrFail($feria);

                $validated = $request->validate([
                    'cod_edo' => 'nullable|string|max:50',
                    'estado' => 'nullable|string|max:255',
                    'cod_mun' => 'nullable|string|max:50',
                    'municipio' => 'nullable|string|max:255',
                    'cod_parroquia' => 'nullable|string|max:50',
                    'parroquia' => 'nullable|string|max:255',
                    'cod_centro' => 'nullable|string|max:50',
                    'nombre_pto' => 'nullable|string|max:255',
                    'direccion_pto' => 'nullable|string|max:255',
                    'rectoria' => 'nullable|string|max:50',
                    'cedula' => 'nullable|string|regex:/^\d{7,8}$/|max:20|unique:ferias,cedula,' . $feria->id,
                    'apellidos' => 'nullable|string|max:255',
                    'nombres' => 'nullable|string|max:255',
                    'telefono' => 'nullable|string|regex:/^\d{10,11}$/|max:20',
                    'correo' => 'nullable|email|max:255',
                    'rol' => 'nullable|string|max:50',
                    'status_contact1' => 'nullable|string|max:50',
                    'fecha_hora1' => 'nullable|string|regex:/^\d{2}\/\d{2}\/\d{2} \d{2}:\d{2}$/',
                    'status_contact2' => 'nullable|string|max:50',
                    'fecha_hora2' => 'nullable|string|regex:/^\d{2}\/\d{2}\/\d{2} \d{2}:\d{2}$/',
                    'status_contact3' => 'nullable|string|max:50',
                    'fecha_hora3' => 'nullable|string|regex:/^\d{2}\/\d{2}\/\d{2} \d{2}:\d{2}$/',
                    'disponibilidad' => 'nullable|string|max:50',
                    'incidencias' => 'nullable|string|max:50',
                    'fecha_incidencia' => 'nullable|string|regex:/^\d{2}\/\d{2}\/\d{2}$/',
                    'hora_incidencia' => 'nullable|string|regex:/^\d{2}:\d{2}$/',
                    'observaciones' => 'nullable|string|max:500',
                    'e_registro' => 'nullable|in:Activo,Inactivo',
                ]);

                $feria->update(array_filter($validated));

                return response()->json([
                    'message' => 'Feria actualizada exitosamente',
                    'details' => $feria
                ], 200);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json(['error' => 'Errores de validación', 'details' => $e->errors()], 422);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al actualizar la feria', 'details' => $e->getMessage()], 500);
            }
        }

        public function destroy($id)
        {
            try {
                $feria = Feria::findOrFail($id);
                $feria->delete();

                if (class_exists(\OwenIt\Auditing\Models\Audit::class) && method_exists($feria, 'audits')) {
                    try {
                        Audit::create([
                            'user_id' => auth()->id() ?? null,
                            'event' => 'deleted',
                            'auditable_type' => Feria::class,
                            'auditable_id' => $feria->id,
                            'old_values' => $feria->getOriginal(),
                            'new_values' => [],
                            'url' => request()->fullUrl(),
                            'ip_address' => request()->ip(),
                            'user_agent' => request()->userAgent(),
                            'created_at' => now(),
                        ]);
                        Log::info('Audit created for deleted feria:', ['id' => $feria->id]);
                    } catch (\Exception $auditException) {
                        Log::warning('Failed to create audit for deleted feria: ' . $auditException->getMessage(), ['id' => $feria->id]);
                    }
                }

                Log::info('Feria deleted successfully:', ['id' => $id]);
                return response()->json(['message' => 'Feria eliminada exitosamente'], 200);
            } catch (\Exception $e) {
                Log::error('Error deleting feria: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
                return response()->json(['error' => 'Error al eliminar la feria', 'details' => $e->getMessage()], 500);
            }
        }

        public function checkCedula($cedula)
        {
            $exists = Feria::where('cedula', $cedula)->exists();
            return response()->json(['exists' => $exists]);
        }

        public function checkDuplicates(Request $request)
        {
            $cedulas = json_decode($request->input('cedulas'), true);
            if (!$cedulas || !is_array($cedulas)) {
                return response()->json(['error' => 'No se proporcionaron cédulas válidas'], 400);
            }

            $cedulas = array_filter($cedulas, function ($cedula) {
                return !empty(trim($cedula));
            });

            if (empty($cedulas)) {
                return response()->json(['already_loaded' => []]);
            }

            $existingCedulas = Feria::whereIn('cedula', $cedulas)->pluck('cedula')->toArray();
            $response = [
                'already_loaded' => array_map(function ($cedula) {
                    return ['cedula' => $cedula];
                }, $existingCedulas)
            ];

            return response()->json($response);
        }

public function export(Request $request)
{
    $format = $request->input('format');
    $timestamp = date('Ymd_His');

    if ($format === 'csv') {
        return Excel::download(new FeriasExport, "ferias_{$timestamp}.csv", \Maatwebsite\Excel\Excel::CSV);
    } elseif ($format === 'excel') {
        return Excel::download(new FeriasExport, "ferias_{$timestamp}.xlsx");
    } else {
        return redirect()->back()->with('error', 'Formato no válido');
    }
}

public function exportTodo(Request $request)
{
    $format = $request->input('format');
    $timestamp = date('Ymd_His');

    if ($format === 'csv') {
        return Excel::download(new TotalAgentesDeFeriasExport, "ferias_completas_{$timestamp}.csv", \Maatwebsite\Excel\Excel::CSV);
    } elseif ($format === 'excel') {
        return Excel::download(new TotalAgentesDeFeriasExport, "ferias_completas_{$timestamp}.xlsx");
    } else {
        return redirect()->back()->with('error', 'Formato no válido');
    }
}

}
