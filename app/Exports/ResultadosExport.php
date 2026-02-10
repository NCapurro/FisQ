<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ResultadosExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $data;
    protected $parties;

    // Recibimos el array completo que generó la función calcularMatriz()
    public function __construct($data)
    {
        $this->data = $data['reporte'];
        $this->parties = $data['parties'];
    }

    /**
    * Convertimos el array de reporte en una Colección para Excel
    */
    public function collection()
    {
        return collect($this->data);
    }

    /**
    * Definimos los Encabezados de las columnas (La fila 1)
    */
    public function headings(): array
    {
        // Columnas fijas iniciales
        $headers = [
            'Departamento',
            'Electores Habilitados',
            'Votantes Reales'
        ];

        // Columnas dinámicas (Nombres de Partidos)
        foreach ($this->parties as $party) {
            $headers[] = $party->name;
        }

        // Columnas fijas finales
        $headers[] = 'Total Votos Válidos';
        $headers[] = '% Participación';

        return $headers;
    }

    /**
    * Mapeamos cada fila de datos a las columnas correspondientes
    */
    public function map($row): array
    {
        // 1. Datos fijos iniciales
        $columns = [
            $row['departamento'],
            $row['electores'],
            $row['votantes'] ?? 0, // Si agregaste este campo en el controller
        ];

        // 2. Datos dinámicos (Votos por partido)
        // Es CRUCIAL recorrer los partidos en el mismo orden que en headings()
        foreach ($this->parties as $party) {
            // Usamos el operador null coalescing (??) por si no hay votos
            $columns[] = $row['votos_partidos'][$party->id] ?? 0;
        }

        // 3. Datos fijos finales
        $columns[] = $row['total_valido'];
        $columns[] = $row['participacion'] . '%'; // Agregamos el símbolo %

        return $columns;
    }

    /**
    * Estilo opcional: Poner la cabecera en Negrita
    */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}