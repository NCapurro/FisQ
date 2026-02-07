<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BackupController extends Controller
{
    // DEFINICIÓN DE LA CARPETA
    // Esto creará: C:\xampp\htdocs\fisq\storage\app\backups
    private $backupFolder = 'backups';

    // Función auxiliar para obtener la ruta completa del sistema
    private function getAbsolutePath()
    {
        return storage_path('app/' . $this->backupFolder);
    }

    public function index()
    {
        $folderPath = $this->getAbsolutePath();

        // 1. Si la carpeta no existe, la crea
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        // 2. GLOB (Nativo de PHP) para buscar archivos .sql
        // Esto busca FÍSICAMENTE en la carpeta, sin intermediarios de Laravel
        $pattern = $folderPath . '/*.{sql}';
        $files = glob($pattern, GLOB_BRACE);

        $backups = [];

        foreach ($files as $file) {
            // $file aquí es la ruta completa (C:\xampp\...)
            $backups[] = [
                'path' => $file,
                'name' => basename($file), // Solo el nombre: backup.sql
                'size' => $this->humanFileSize(filesize($file)),
                'date' => date('Y-m-d H:i:s', filemtime($file)), // Fecha real del archivo
            ];
        }

        // El más nuevo primero
        // Uso usort porque $files de glob no siempre viene ordenado por fecha
        usort($backups, function($a, $b) {
            return $b['date'] <=> $a['date'];
        });

        return view('backups.index', compact('backups'));
    }

    public function create()
    {
        $dbName = env('DB_DATABASE');
        $dbUser = env('DB_USERNAME');
        $dbPass = env('DB_PASSWORD');
        $dbHost = '127.0.0.1';

        $filename = 'backup-' . date('Y-m-d-H-i-s') . '.sql';
        
        // Uso la misma función de ruta que el index
        $folderPath = $this->getAbsolutePath();
        if (!file_exists($folderPath)) mkdir($folderPath, 0777, true);
        
        $filePath = $folderPath . '/' . $filename;

        // RUTA MYSQLDUMP (Ajustar si es necesario)
        $mysqldumpPath = '"C:/Program Files/MySQL/MySQL Server 8.0/bin/mysqldump.exe"';

        $command = "{$mysqldumpPath} --user=\"{$dbUser}\" --password=\"{$dbPass}\" --host={$dbHost} --protocol=tcp --column-statistics=0 \"{$dbName}\" --result-file=\"{$filePath}\"";

        try {
            $process = Process::fromShellCommandline($command);
            $process->setEnv([
                'SystemRoot' => 'C:\\Windows',
                'windir'     => 'C:\\Windows',
                'TMP'        => 'C:\\Windows\\Temp',
                'TEMP'       => 'C:\\Windows\\Temp',
                'PATH'       => getenv('PATH')
            ]);
            $process->setTimeout(300);
            $process->run();

            if (!$process->isSuccessful()) {
                if (file_exists($filePath)) unlink($filePath);
                throw new ProcessFailedException($process);
            }

            return redirect()->back()->with('success', 'Backup generado: ' . $filename);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function download($file_name)
    {
        // Construir ruta absoluta
        $filePath = $this->getAbsolutePath() . '/' . $file_name;

        // Verificar con file_exists nativo
        if (file_exists($filePath)) {
            // Uso response()->download para forzar la descarga desde ruta absoluta
            return response()->download($filePath);
        }

        return redirect()->back()->with('error', 'El archivo no existe.');
    }

    public function delete($file_name)
    {
        $filePath = $this->getAbsolutePath() . '/' . $file_name;

        if (file_exists($filePath)) {
            unlink($filePath); // Borrado nativo de PHP
            return redirect()->back()->with('success', 'Backup eliminado.');
        }

        return redirect()->back()->with('error', 'El archivo no existe.');
    }

    // AUXILIAR
    private function humanFileSize($size, $unit = "") {
        if ((!$unit && $size >= 1<<30) || $unit == "GB")
            return number_format($size / (1<<30), 2) . " GB";
        if ((!$unit && $size >= 1<<20) || $unit == "MB")
            return number_format($size / (1<<20), 2) . " MB";
        if ((!$unit && $size >= 1<<10) || $unit == "KB")
            return number_format($size / (1<<10), 2) . " KB";
        return number_format($size) . " bytes";
    }
}