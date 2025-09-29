<?php

namespace App\Imports;

use App\Models\Feria;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;

class FeriasImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    protected $duplicatesInFile = [];
    protected $duplicatesInDB = [];
    protected $importedCount = 0;
    protected $failedRows = [];

    public function model(array $row)
    {
        $cedula = trim($row['cedula'] ?? '');

        // Check for duplicates within the file
        if (isset($this->duplicatesInFile[$cedula])) {
            $this->duplicatesInFile[$cedula][] = $this->importedCount + 2; // Row number (heading row is 1, data starts at 2)
            return null; // Skip duplicate within file
        } else {
            $this->duplicatesInFile[$cedula] = [$this->importedCount + 2];
        }

        // Check for duplicates in the database
        if (Feria::where('cedula', $cedula)->exists()) {
            $this->duplicatesInDB[] = $cedula;
            return null; // Skip duplicate in DB
        }

        // Validate and create model
        $validator = Validator::make($row, [
            'cedula' => 'required|regex:/^\d{7,8}$/|max:20',
            'estado' => 'required|string|max:255',
            'municipio' => 'required|string|max:255',
            'parroquia' => 'required|string|max:255',
            'nombre_pto' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'nombres' => 'required|string|max:255',
            'telefono' => 'required|regex:/^\d{10,11}$/|max:20',
            'correo' => 'nullable|email|max:255',
            'rectoria' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            $this->failedRows[] = [
                'row' => $row,
                'errors' => $validator->errors()->toArray(),
            ];
            return null; // Skip invalid rows
        }

        $this->importedCount++;
        return new Feria([
            'cod_edo' => $row['cod_edo'] ?? null,
            'estado' => $row['estado'],
            'cod_mun' => $row['cod_mun'] ?? null,
            'municipio' => $row['municipio'],
            'cod_parroquia' => $row['cod_parroquia'] ?? null,
            'parroquia' => $row['parroquia'],
            'cod_centro' => $row['cod_centro'] ?? null,
            'nombre_pto' => $row['nombre_pto'],
            'direccion_pto' => $row['direccion_pto'] ?? null,
            'rectoria' => $row['rectoria'] ?? 'ACME NOGAL',
            'cedula' => $cedula,
            'apellidos' => $row['apellidos'],
            'nombres' => $row['nombres'],
            'telefono' => $row['telefono'],
            'correo' => $row['correo'] ?? null,
            'e_registro' => 'Activo',
        ]);
    }

    public function getDuplicatesInFile()
    {
        return array_keys(array_filter($this->duplicatesInFile, fn($rows) => count($rows) > 1));
    }

    public function getDuplicatesInDB()
    {
        return array_unique($this->duplicatesInDB);
    }

    public function getSummary()
    {
        return [
            'imported' => $this->importedCount,
            'failed' => count($this->failedRows),
        ];
    }

    public function getFailedRows()
    {
        return $this->failedRows;
    }

    public function rules(): array
    {
        return [
            'cedula' => 'required|regex:/^\d{7,8}$/|max:20',
            'estado' => 'required|string|max:255',
            'municipio' => 'required|string|max:255',
            'parroquia' => 'required|string|max:255',
            'nombre_pto' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'nombres' => 'required|string|max:255',
            'telefono' => 'required|regex:/^\d{10,11}$/|max:20',
            'correo' => 'nullable|email|max:255',
            'rectoria' => 'required|string|max:50',
        ];
    }
}
