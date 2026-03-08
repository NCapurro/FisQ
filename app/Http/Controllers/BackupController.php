<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\Log; // <-- Agregamos Log para guardar errores de forma segura

class BackupController extends Controller
{
    // DEFINICIÓN DE LA CARPETA
    // Esto creará: /var/www/html/storage/app/backups en el contenedor Linux
    private $backupFolder = 'backups';

    // Función auxiliar para obtener la ruta completa del sistema
    private function getAbsolutePath()
    {
        return storage_path('app/' . $this->backupFolder);
    }

    public function index()
    {
        $folderPath = $this->getAbsolutePath();

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        $pattern = $folderPath . '/*.{sql}';
        $files = glob($pattern, GLOB_BRACE) ?: []; // Evitamos error si glob devuelve false

        $backups = [];

        foreach ($files as $file) {
            $backups[] = [
                'path' => $file,
                'name' => basename($file),
                'size' => $this->humanFileSize(filesize($file)),
                'date' => date('Y-m-d H:i:s', filemtime($file)),
            ];
        }

        usort($backups, function($a, $b) {
            return $b['date'] <=> $a['date'];
        });

        return view('backups.index', compact('backups'));
    }

    public function create()
    {
        // Usamos config() en lugar de env() por si las variables de entorno están cacheadas
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');
        $dbHost = config('database.connections.mysql.host'); // En Docker esto apuntará a 'fisq_db'

        $filename = 'backup-' . date('Y-m-d-H-i-s') . '.sql';
        
        $folderPath = $this->getAbsolutePath();
        if (!file_exists($folderPath)) mkdir($folderPath, 0777, true);
        
        $filePath = $folderPath . '/' . $filename;

        // En Linux/Docker, el ejecutable es simplemente 'mysqldump'
        $mysqldumpPath = 'mysqldump';

        // Armamos el comando (sin variables de entorno de Windows)
        $command = "{$mysqldumpPath} --user=\"{$dbUser}\" --password=\"{$dbPass}\" --host=\"{$dbHost}\" --column-statistics=0 \"{$dbName}\" --result-file=\"{$filePath}\"";

        try {
            $process = Process::fromShellCommandline($command);
            $process->setTimeout(300);
            $process->run();

            if (!$process->isSuccessful()) {
                if (file_exists($filePath)) unlink($filePath);
                
                // GUARDAMOS EL ERROR REAL EN LOS LOGS, NO EN LA VISTA
                Log::error('Fallo al ejecutar mysqldump: ' . $process->getErrorOutput());
                
                throw new ProcessFailedException($process);
            }

            return redirect()->back()->with('success', 'Backup generado correctamente: ' . $filename);

        } catch (\Exception $e) {
            // SI FALLA, LE MOSTRAMOS ESTO AL USUARIO (Sin exponer la clave)
            Log::error('Excepción creando backup: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al generar el backup. Los detalles técnicos han sido guardados en los registros del servidor por seguridad.');
        }
    }

    public function download($file_name)
    {
        $filePath = $this->getAbsolutePath() . '/' . $file_name;

        if (file_exists($filePath)) {
            return response()->download($filePath);
        }

        return redirect()->back()->with('error', 'El archivo de backup no existe.');
    }

    public function delete($file_name)
    {
        $filePath = $this->getAbsolutePath() . '/' . $file_name;

        if (file_exists($filePath)) {
            unlink($filePath);
            return redirect()->back()->with('success', 'Backup eliminado de forma permanente.');
        }

        return redirect()->back()->with('error', 'El archivo no existe o ya fue eliminado.');
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