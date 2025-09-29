<?php

namespace App\Imports;

use App\Models\Fiscalizacion;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;
use Throwable;

class FiscalizacionesImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure, WithChunkReading
{
    use Importable;

    private $duplicatesInFile = [];
    private $duplicatesInDB = [];
    private $failedRows = [];
    private $imported = 0;

    public function model(array $row)
    {
        $cedula = trim($row['cedula'] ?? '');

        // Verificar duplicados en el archivo
        if (isset($this->duplicatesInFile[$cedula])) {
            $this->failedRows[] = [
                'row' => $row,
                'errors' => ['La cédula ya está en el archivo.']
            ];
            return null;
        }

        // Verificar si ya existe en la base de datos
        if (Fiscalizacion::where('cedula', $cedula)->exists()) {
            $this->duplicatesInDB[$cedula] = true;
            $this->failedRows[] = [
                'row' => $row,
                'errors' => ['La cédula ya está registrada en la base de datos.']
            ];
            return null;
        }

        // Marcar cédula como procesada
        $this->duplicatesInFile[$cedula] = true;

        // Filtrar solo columnas válidas de la migración
        $data = array_intersect_key($row, array_flip([
            'cod_edo', 'estado', 'cod_mun', 'municipio', 'cod_parroquia', 'parroquia',
            'cod_centro', 'nombre_pto', 'direccion_pto', 'circuns', 'rectoria', 'cedula',
            'apellidos', 'nombres', 'telefono', 'correo', 'rol', 'disponibilidad',
            'incidencias', 'fecha_incidencia', 'hora_incidencia', 'observaciones', 'e_registro'
        ]));

        if (empty($cedula)) {
            $this->failedRows[] = [
                'row' => $row,
                'errors' => ['La cédula es obligatoria.']
            ];
            return null;
        }

        $fiscalizacion = new Fiscalizacion($data);
        $this->imported++;
        return $fiscalizacion;
    }

    public function rules(): array
    {
        return [
            'cedula' => 'required|string|max:20',
            'e_registro' => 'required|in:Activo,Inactivo',
        ];
    }

    public function onError(Throwable $error)
    {
        // Manejar errores de fila
        Log::error('Error en FiscalizacionesImport: ' . $error->getMessage(), ['trace' => $error->getTraceAsString()]);
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function getSummary()
    {
        return [
            'imported' => $this->imported,
            'failed' => count($this->failedRows),
        ];
    }

    public function getDuplicatesInFile()
    {
        return array_keys($this->duplicatesInFile);
    }

    public function getDuplicatesInDB()
    {
        return array_keys($this->duplicatesInDB);
    }

    public function getFailedRows()
    {
        return $this->failedRows;
    }
}
