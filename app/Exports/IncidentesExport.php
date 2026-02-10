<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class IncidentesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $incidentes;

    public function __construct($incidentes)
    {
        $this->incidentes = $incidentes;
    }

    public function collection()
    {
        return $this->incidentes;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Fecha y Hora',
            'Prioridad',
            'Estado',
            'Reportado Por',
            'Ubicación (Mesa/Escuela)',
            'Descripción del Problema'
        ];
    }

    public function map($incidente): array
    {
        return [
            $incidente->id,
            // Formateamos la fecha para que sea legible en Excel
            $incidente->created_at->format('d/m/Y H:i:s'), 
            strtoupper($incidente->priority),
            $incidente->is_resolved ? 'RESUELTO' : 'PENDIENTE',
            // Accedemos a la relación del usuario
            $incidente->user->name . ' ' . $incidente->user->lastname,
            // Formateamos la ubicación de forma segura
            $incidente->mesa 
                ? "Mesa {$incidente->mesa->number} - {$incidente->mesa->school->name}" 
                : 'General / Sin Mesa',
            $incidente->description,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']]],
            // Ejemplo: Pintar el fondo de la cabecera de Azul
            1 => ['fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF4B5563'], // Un gris oscuro
                ],
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']] // Letra blanca
            ],
        ];
    }
}