<?php

namespace App\Http\Controllers;

use App\Models\Fiscalizacion;
use App\Models\IncidenciasFiscalizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use OwenIt\Auditing\Models\Audit;
use Yajra\DataTables\DataTables;

use App\Exports\FiscalizacionesExport;
use App\Exports\TotalFiscalizacionesExport;
use Maatwebsite\Excel\Facades\Excel;

class FiscalizacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:crear-fiscalizacion|crear-fiscalizacion-all', ['only' => ['storeMassive']]);
        $this->middleware('permission:ver-fiscalizacion', ['only' => ['index', 'datatables']]);
        $this->middleware('permission:crear-fiscalizacion', ['only' => ['create', 'store']]);
        $this->middleware('permission:editar-fiscalizacion', ['only' => ['edit', 'update']]);
        $this->middleware('permission:borrar-fiscalizacion', ['only' => ['destroy']]);
        $this->middleware('permission:ver-fiscalizacion-all', ['only' => ['indexAll', 'datatablesAll']]);
        $this->middleware('permission:crear-fiscalizacion-all', ['only' => ['createAll', 'storeAll']]);
        $this->middleware('permission:editar-fiscalizacion-all', ['only' => ['editAll', 'updateAll']]);
        $this->middleware('permission:borrar-fiscalizacion-all', ['only' => ['destroyAll']]);
    }

    public function index()
    {
        $fiscalizacions = Fiscalizacion::where('e_registro', 'Activo')->with('incidencias')->get();
        return view('fiscalizacions.index', compact('fiscalizacions'));
    }

    public function datatables(Request $request)
    {
        $query = Fiscalizacion::where('e_registro', 'Activo')->with('incidencias')->select([
            'id', 'estado', 'municipio', 'parroquia', 'nombre_pto', 'cedula',
            'apellidos', 'nombres', 'telefono', 'correo', 'e_registro'
        ]);

        return DataTables::of($query)
            ->addColumn('incidencia', function ($fiscalizacion) {
                $latestIncidence = $fiscalizacion->incidencias->sortByDesc('fecha_incidencia')->sortByDesc('hora_incidencia')->first();
                \Log::info('Latest incidence for ID ' . $fiscalizacion->id . ' in index: ', ['incidencia' => $latestIncidence ? $latestIncidence->toArray() : null]);
                return $latestIncidence ? $latestIncidence->incidencia : 'Sin incidencias';
            })
            ->addColumn('fecha_incidencia', function ($fiscalizacion) {
                $latestIncidence = $fiscalizacion->incidencias->sortByDesc('fecha_incidencia')->sortByDesc('hora_incidencia')->first();
                return $latestIncidence ? $latestIncidence->fecha_incidencia : 'N/A';
            })
            ->addColumn('hora_incidencia', function ($fiscalizacion) {
                $latestIncidence = $fiscalizacion->incidencias->sortByDesc('fecha_incidencia')->sortByDesc('hora_incidencia')->first();
                return $latestIncidence ? $latestIncidence->hora_incidencia : 'N/A';
            })
            ->rawColumns([])
            ->make(true);
    }

    public function datatablesAll(Request $request)
    {
        $query = Fiscalizacion::with('incidencias')->select([
            'id', 'estado', 'municipio', 'parroquia', 'nombre_pto', 'cedula',
            'apellidos', 'nombres', 'telefono', 'correo', 'e_registro'
        ]);

        return DataTables::of($query)
            ->addColumn('incidencia', function ($fiscalizacion) {
                $latestIncidence = $fiscalizacion->incidencias->sortByDesc('fecha_incidencia')->sortByDesc('hora_incidencia')->first();
                \Log::info('Latest incidence for ID ' . $fiscalizacion->id . ': ', ['incidencia' => $latestIncidence ? $latestIncidence->toArray() : null]);
                return $latestIncidence ? $latestIncidence->incidencia : 'Sin incidencias';
            })
            ->addColumn('fecha_incidencia', function ($fiscalizacion) {
                $latestIncidence = $fiscalizacion->incidencias->sortByDesc('fecha_incidencia')->sortByDesc('hora_incidencia')->first();
                return $latestIncidence ? $latestIncidence->fecha_incidencia : 'N/A';
            })
            ->addColumn('hora_incidencia', function ($fiscalizacion) {
                $latestIncidence = $fiscalizacion->incidencias->sortByDesc('fecha_incidencia')->sortByDesc('hora_incidencia')->first();
                return $latestIncidence ? $latestIncidence->hora_incidencia : 'N/A';
            })
            ->rawColumns([])
            ->make(true);
    }

    public function datatablesIncidencias(Request $request)
    {
        $query = IncidenciasFiscalizacion::with('fiscalizacion')->select(['id', 'fiscalizacion_id', 'incidencia', 'fecha_incidencia', 'hora_incidencia']);
        return DataTables::of($query)
            ->addColumn('action', function ($incidencia) {
                return '<button class="btn btn-sm btn-primary edit-btn" data-id="' . $incidencia->fiscalizacion_id . '">Ver</button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function create()
    {
        return view('fiscalizacions.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);
        $validated['e_registro'] = 'Activo';
        $validated['incidencias'] = $request->input('incidencias', '');
        $validated['fecha_incidencia'] = $request->input('fecha_incidencia', '');
        $validated['hora_incidencia'] = $request->input('hora_incidencia', '');

        $fiscalizacion = Fiscalizacion::create($validated);

        if ($request->filled('incidencias')) {
            IncidenciasFiscalizacion::create([
                'fiscalizacion_id' => $fiscalizacion->id,
                'incidencia' => $validated['incidencias'],
                'fecha_incidencia' => $validated['fecha_incidencia'],
                'hora_incidencia' => $validated['hora_incidencia'],
            ]);
        }

        if (class_exists(Audit::class) && method_exists($fiscalizacion, 'audits')) {
            Audit::create([
                'user_id' => auth()->id() ?? null,
                'event' => 'created',
                'auditable_type' => Fiscalizacion::class,
                'auditable_id' => $fiscalizacion->id,
                'old_values' => [],
                'new_values' => $fiscalizacion->getAttributes(),
                'url' => $request->fullUrl(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);
        }

        return response()->json([
            'message' => 'Fiscalización creada exitosamente',
            'details' => $fiscalizacion
        ], 201);
    }

    public function edit($id)
    {
        try {
            $fiscalizacion = Fiscalizacion::with('incidencias')->findOrFail($id);
            return response()->json(['fiscalizacion' => $fiscalizacion]);
        } catch (\Exception $e) {
            \Log::error('Error al cargar fiscalización para edición: ' . $e->getMessage(), ['id' => $id, 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'No se pudo cargar la fiscalización.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $fiscalizacion = Fiscalizacion::findOrFail($id);

        $request->validate([
            'cedula' => ['required', 'string', 'regex:/^\d{7,8}$/', Rule::unique('fiscalizacions')->ignore($id)],
            'estado' => 'nullable|string|max:255',
            'municipio' => 'nullable|string|max:255',
            'parroquia' => 'nullable|string|max:255',
            'nombre_pto' => 'nullable|string|max:255',
            'apellidos' => 'nullable|string|max:255',
            'nombres' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|regex:/^\d{10,11}$/',
            'correo' => 'nullable|email|max:255',
            'e_registro' => 'in:Activo', // Fijo como Activo en index
            'incidencias' => 'nullable|string|max:50',
            'fecha_incidencia' => 'nullable|string|regex:/^\d{2}\/\d{2}\/\d{2}$/',
            'hora_incidencia' => 'nullable|string|regex:/^\d{2}:\d{2}$/',
        ]);

        $fiscalizacion->update($request->only([
            'cedula', 'estado', 'municipio', 'parroquia', 'nombre_pto', 'apellidos',
            'nombres', 'telefono', 'correo', 'e_registro'
        ]));

        if ($request->filled('incidencias')) {
            IncidenciasFiscalizacion::create([
                'fiscalizacion_id' => $fiscalizacion->id,
                'incidencia' => $request->input('incidencias'),
                'fecha_incidencia' => $request->input('fecha_incidencia'),
                'hora_incidencia' => $request->input('hora_incidencia'),
            ]);
        }

        if (class_exists(Audit::class) && method_exists($fiscalizacion, 'audits')) {
            Audit::create([
                'user_id' => auth()->id() ?? null,
                'event' => 'updated',
                'auditable_type' => Fiscalizacion::class,
                'auditable_id' => $fiscalizacion->id,
                'old_values' => $fiscalizacion->getOriginal(),
                'new_values' => $fiscalizacion->getAttributes(),
                'url' => $request->fullUrl(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);
        }

        return response()->json(['message' => 'Fiscalización actualizada exitosamente']);
    }

    public function destroy($id)
    {
        $fiscalizacion = Fiscalizacion::findOrFail($id);
        $fiscalizacion->delete();

        if (class_exists(Audit::class) && method_exists($fiscalizacion, 'audits')) {
            Audit::create([
                'user_id' => auth()->id() ?? null,
                'event' => 'deleted',
                'auditable_type' => Fiscalizacion::class,
                'auditable_id' => $fiscalizacion->id,
                'old_values' => $fiscalizacion->getOriginal(),
                'new_values' => [],
                'url' => request()->fullUrl(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);
        }

        return response()->json(['message' => 'Fiscalización eliminada exitosamente']);
    }

    public function indexAll()
    {
        return view('fiscalizacions.index-all');
    }

    public function createAll()
    {
        return view('fiscalizacions.create-all');
    }

    public function storeAll(Request $request)
    {
        $validated = $this->validateRequest($request);
        $validated['incidencias'] = $request->input('incidencias', '');
        $validated['fecha_incidencia'] = $request->input('fecha_incidencia', '');
        $validated['hora_incidencia'] = $request->input('hora_incidencia', '');

        $fiscalizacion = Fiscalizacion::create($validated);

        if ($request->filled('incidencias')) {
            IncidenciasFiscalizacion::create([
                'fiscalizacion_id' => $fiscalizacion->id,
                'incidencia' => $validated['incidencias'],
                'fecha_incidencia' => $validated['fecha_incidencia'],
                'hora_incidencia' => $validated['hora_incidencia'],
            ]);
        }

        if (class_exists(Audit::class) && method_exists($fiscalizacion, 'audits')) {
            Audit::create([
                'user_id' => auth()->id() ?? null,
                'event' => 'created',
                'auditable_type' => Fiscalizacion::class,
                'auditable_id' => $fiscalizacion->id,
                'old_values' => [],
                'new_values' => $fiscalizacion->getAttributes(),
                'url' => $request->fullUrl(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);
        }

        return response()->json([
            'message' => 'Fiscalización creada exitosamente',
            'details' => $fiscalizacion
        ], 201);
    }

    public function editAll($id)
    {
        try {
            $fiscalizacion = Fiscalizacion::with('incidencias')->findOrFail($id);
            return response()->json(['fiscalizacion' => $fiscalizacion]);
        } catch (\Exception $e) {
            \Log::error('Error al cargar fiscalización para edición en index-all: ' . $e->getMessage(), ['id' => $id, 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'No se pudo cargar la fiscalización.'], 500);
        }
    }

    public function updateAll(Request $request, $id)
    {
        $fiscalizacion = Fiscalizacion::findOrFail($id);

        $request->validate([
            'cedula' => ['required', 'string', 'regex:/^\d{7,8}$/', Rule::unique('fiscalizacions')->ignore($id)],
            'estado' => 'nullable|string|max:255',
            'municipio' => 'nullable|string|max:255',
            'parroquia' => 'nullable|string|max:255',
            'nombre_pto' => 'nullable|string|max:255',
            'apellidos' => 'nullable|string|max:255',
            'nombres' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|regex:/^\d{10,11}$/',
            'correo' => 'nullable|email|max:255',
            'e_registro' => 'nullable|in:Activo,Inactivo',
            'incidencias' => 'nullable|string|max:50',
            'fecha_incidencia' => 'nullable|string|regex:/^\d{2}\/\d{2}\/\d{2}$/',
            'hora_incidencia' => 'nullable|string|regex:/^\d{2}:\d{2}$/',
        ]);

        $fiscalizacion->update($request->only([
            'cedula', 'estado', 'municipio', 'parroquia', 'nombre_pto', 'apellidos',
            'nombres', 'telefono', 'correo', 'e_registro'
        ]));

        if ($request->filled('incidencias')) {
            IncidenciasFiscalizacion::create([
                'fiscalizacion_id' => $fiscalizacion->id,
                'incidencia' => $request->input('incidencias'),
                'fecha_incidencia' => $request->input('fecha_incidencia'),
                'hora_incidencia' => $request->input('hora_incidencia'),
            ]);
        }

        if (class_exists(Audit::class) && method_exists($fiscalizacion, 'audits')) {
            Audit::create([
                'user_id' => auth()->id() ?? null,
                'event' => 'updated',
                'auditable_type' => Fiscalizacion::class,
                'auditable_id' => $fiscalizacion->id,
                'old_values' => $fiscalizacion->getOriginal(),
                'new_values' => $fiscalizacion->getAttributes(),
                'url' => $request->fullUrl(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);
        }

        return response()->json([
            'message' => 'Fiscalización actualizada exitosamente',
            'details' => $fiscalizacion
        ]);
    }

    public function destroyAll($id)
    {
        $fiscalizacion = Fiscalizacion::findOrFail($id);
        $fiscalizacion->delete();

        if (class_exists(Audit::class) && method_exists($fiscalizacion, 'audits')) {
            Audit::create([
                'user_id' => auth()->id() ?? null,
                'event' => 'deleted',
                'auditable_type' => Fiscalizacion::class,
                'auditable_id' => $fiscalizacion->id,
                'old_values' => $fiscalizacion->getOriginal(),
                'new_values' => [],
                'url' => request()->fullUrl(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);
        }

        return response()->json(['message' => 'Fiscalización eliminada exitosamente']);
    }

    public function storeMassive(Request $request)
    {
        try {
            $file = $request->file('file');
            $cedulas = json_decode($request->input('cedulas'), true) ?? [];
            $isFiscalizacionesTable = $request->input('is_fiscalizaciones_table', '0');

            if (!$file || empty($cedulas)) {
                return response()->json([
                    'message' => 'No se proporcionó archivo o cédulas válidas.',
                    'imported' => 0,
                    'failed_rows' => [],
                    'already_loaded' => []
                ], 400);
            }

            if ($file->getSize() > 5 * 1024 * 1024) {
                return response()->json([
                    'message' => 'El archivo excede el tamaño máximo de 5MB.',
                    'imported' => 0,
                    'failed_rows' => [],
                    'already_loaded' => []
                ], 400);
            }

            $extension = $file->getClientOriginalExtension();
            if (!in_array(strtolower($extension), ['csv', 'txt', 'xls', 'xlsx'])) {
                return response()->json([
                    'message' => 'Formato de archivo no soportado. Usa CSV, TXT, XLS o XLSX.',
                    'imported' => 0,
                    'failed_rows' => [],
                    'already_loaded' => []
                ], 400);
            }

            $csvData = file_get_contents($file->getRealPath());
            $rows = array_map('str_getcsv', array_filter(explode("\n", $csvData))); // Filtrar filas vacías
            $headers = array_shift($rows) ?: [];
            \Log::info('Headers encontrados: ' . json_encode($headers));
            \Log::info('Filas procesadas: ' . count($rows));

            $alreadyLoaded = Fiscalizacion::whereIn('cedula', $cedulas)->get()->map(function ($item) {
                return ['cedula' => $item->cedula];
            })->toArray();

            $imported = 0;
            $failedRows = [];
            $successRows = [];

            foreach ($rows as $index => $row) {
                if (empty($row) || count($row) !== count($headers)) {
                    $failedRows[] = ['row' => $row, 'errors' => ['general' => ['Fila vacía o con columnas faltantes']]];
                    continue;
                }

                $data = array_combine($headers, $row);
                $cedula = trim($data['cedula'] ?? '');

                if (!in_array($cedula, $cedulas) || in_array(['cedula' => $cedula], $alreadyLoaded) || !preg_match('/^\d{7,8}$/', $cedula)) {
                    $failedRows[] = ['row' => $data, 'errors' => ['cedula' => ['Cédula no válida, duplicada o ya registrada']]];
                    continue;
                }

                $fiscalizacionData = [
                    'estado' => $data['estado'] ?? '',
                    'municipio' => $data['municipio'] ?? '',
                    'parroquia' => $data['parroquia'] ?? '',
                    'nombre_pto' => $data['nombre_pto'] ?? '',
                    'cedula' => $cedula,
                    'apellidos' => $data['apellidos'] ?? '',
                    'nombres' => $data['nombres'] ?? '',
                    'telefono' => $data['telefono'] ?? '',
                    'correo' => $data['correo'] ?? '',
                    'fecha_incidencia' => $data['fecha_incidencia'] ?? '',
                    'hora_incidencia' => $data['hora_incidencia'] ?? '',
                    'e_registro' => $isFiscalizacionesTable === '1' ? 'Activo' : ($data['e_registro'] ?? 'Activo'),
                ];

                $validator = Validator::make($fiscalizacionData, [
                    'cedula' => ['required', 'string', 'regex:/^\d{7,8}$/', Rule::unique('fiscalizacions')->ignore($cedula, 'cedula')],
                    'telefono' => ['nullable', 'string', 'regex:/^\d{10,11}$/'],
                    'correo' => ['nullable', 'email'],
                    'fecha_incidencia' => ['nullable', 'string', 'regex:/^\d{2}\/\d{2}\/\d{2}$/'],
                    'hora_incidencia' => ['nullable', 'string', 'regex:/^\d{2}:\d{2}$/'],
                ]);

                if ($validator->fails()) {
                    $failedRows[] = ['row' => $data, 'errors' => $validator->errors()->toArray()];
                    continue;
                }

                $fiscalizacion = Fiscalizacion::firstOrCreate(['cedula' => $cedula], $fiscalizacionData);
                if ($fiscalizacion) {
                    if (!empty($data['incidencias'])) {
                        IncidenciasFiscalizacion::create([
                            'fiscalizacion_id' => $fiscalizacion->id,
                            'incidencia' => $data['incidencias'],
                            'fecha_incidencia' => $data['fecha_incidencia'],
                            'hora_incidencia' => $data['hora_incidencia'],
                        ]);
                    }
                    $imported++;
                    $successRows[] = $data;
                } else {
                    $failedRows[] = ['row' => $data, 'errors' => ['general' => ['Error al guardar el registro']]];
                    \Log::error('Error al guardar fila ' . ($index + 2) . ': ' . json_encode($fiscalizacion->getErrors() ?? []));
                }
            }

            return response()->json([
                'message' => 'Carga procesada.',
                'imported' => $imported,
                'failed_rows' => $failedRows,
                'already_loaded' => $alreadyLoaded,
                'success_rows' => $successRows
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en storeMassive: ' . $e->getMessage(), ['exception' => $e->getTraceAsString()]);
            return response()->json([
                'message' => 'Error interno del servidor. Consulta los logs para más detalles.',
                'imported' => 0,
                'failed_rows' => [],
                'already_loaded' => []
            ], 500);
        }
    }

    protected function parseDate($date)
    {
        if (!$date) return null;
        try {
            return Carbon::createFromFormat('d/m/y', $date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function parseTime($time)
    {
        if (!$time) return null;
        try {
            return Carbon::createFromFormat('H:i', $time)->format('H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function validateRequest(Request $request, $currentCedula = null)
    {
        return $request->validate([
            'cedula' => [
                'required',
                'string',
                'max:20',
                'regex:/^\d{7,8}$/',
                Rule::unique('fiscalizacions', 'cedula')->ignore($currentCedula)
            ],
            'estado' => 'nullable|string|max:255',
            'municipio' => 'nullable|string|max:255',
            'parroquia' => 'nullable|string|max:255',
            'nombre_pto' => 'nullable|string|max:255',
            'apellidos' => 'nullable|string|max:255',
            'nombres' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20|regex:/^\d{10,11}$/',
            'correo' => 'nullable|email|max:255',
            'incidencias' => 'nullable|string|max:50',
            'fecha_incidencia' => 'nullable|string|regex:/^\d{2}\/\d{2}\/\d{2}$/',
            'hora_incidencia' => 'nullable|string|regex:/^\d{2}:\d{2}$/',
            'e_registro' => 'nullable|in:Activo,Inactivo'
        ], [
            'cedula.required' => 'La cédula es obligatoria.',
            'cedula.unique' => 'La cédula ya está registrada.',
            'cedula.regex' => 'La cédula debe ser un número de 7 u 8 dígitos.',
            'telefono.regex' => 'El teléfono debe ser un número de 10 u 11 dígitos.',
            'fecha_incidencia.regex' => 'La fecha de incidencia debe estar en formato DD/MM/YY.',
            'hora_incidencia.regex' => 'La hora de incidencia debe estar en formato HH:MM.',
        ]);
    }

    public function checkDuplicates(Request $request)
    {
        $cedulas = json_decode($request->input('cedulas'), true) ?? [];
        if (empty($cedulas)) {
            return response()->json(['already_loaded' => []], 400);
        }

        $alreadyLoaded = Fiscalizacion::whereIn('cedula', $cedulas)->get()->map(function ($item) {
            return ['cedula' => $item->cedula];
        })->toArray();

        return response()->json(['already_loaded' => $alreadyLoaded]);
    }

public function exportFiltrado(Request $request)
{
    $format = $request->input('format');
    $timestamp = date('Ymd_His');
    $extension = $format === 'csv' ? 'csv' : 'xlsx';
    $fileName = 'fiscalizaciones_' . $timestamp . '.' . $extension;

    $export = new FiscalizacionesExport;

    $type = $format === 'csv' ? \Maatwebsite\Excel\Excel::CSV : \Maatwebsite\Excel\Excel::XLSX;

    return Excel::download($export, $fileName, $type);
}

public function exportTodo(Request $request)
{
    $format = $request->input('format');
    $timestamp = date('Ymd_His');
    $extension = $format === 'csv' ? 'csv' : 'xlsx';
    $fileName = 'fiscalizaciones_todas_' . $timestamp . '.' . $extension;

    $export = new TotalFiscalizacionesExport;

    $type = $format === 'csv' ? \Maatwebsite\Excel\Excel::CSV : \Maatwebsite\Excel\Excel::XLSX;

    return Excel::download($export, $fileName, $type);
}


}
