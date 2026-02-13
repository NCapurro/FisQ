<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ActivityLogsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $logs;

    public function __construct($logs) {
        $this->logs = $logs;
    }

    public function collection() {
        return $this->logs;
    }

    public function headings(): array {
        return ['Fecha y Hora', 'Usuario', 'Módulo', 'Acción', 'Descripción'];
    }

    public function map($log): array {
        return [
            $log->created_at->format('d/m/Y H:i'),
            $log->user->name ?? 'Sistema',
            ucfirst($log->module),
            ucfirst($log->action),
            $log->description,
        ];
    }
}
