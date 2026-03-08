<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Province;
use App\Models\Department;
use App\Models\School;
use App\Models\Mesa;
use App\Models\PoliticalParty;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // -------------------------------------------------------
        // 1. GEOGRAFÍA (Entre Ríos)
        // -------------------------------------------------------
        $entreRios = Province::create(['name' => 'Entre Ríos']);

        $nombresDeptos = ['Paraná', 'Concordia', 'Gualeguaychú', 'Uruguay', 'Federación', 'La Paz', 'Nogoyá', 'Diamante', 'Colón', 'Villaguay', 'Islas del Ibicuy', 'Tala', 'Victoria', 'Gualeguay', 'Federal', 'Feliciano', 'San Salvador' ];
        
        // Guardamos los objetos departamentos creados para usarlos después
        $deptosCreados = []; 

        foreach ($nombresDeptos as $nombre) {
            // Crear Departamento
            $depto = Department::create([
                'name' => $nombre,
                'province_id' => $entreRios->id
            ]);
            
            $deptosCreados[$nombre] = $depto;

            // Crear 1 Escuela en cada Departamento
            $escuela = School::create([
                'name' => 'Escuela Normal de ' . $nombre,
                'address' => 'Calle San Martín 100',
                'department_id' => $depto->id, // Conexión directa
            ]);

            // Crear 2 Mesas en esa Escuela
            Mesa::create([
                'number' => 100 + $depto->id, // Ej: 101
                'school_id' => $escuela->id,
                'status' => 'created'
            ]);
            
            Mesa::create([
                'number' => 200 + $depto->id, // Ej: 201
                'school_id' => $escuela->id,
                'status' => 'created'
            ]);
        }

        // -------------------------------------------------------
        // 2. PARTIDOS POLÍTICOS
        // -------------------------------------------------------
        PoliticalParty::create([
            'name' => 'Impugnado',
            'abbreviation' => 'VIMP',
            'color_hex' => '#727272', 
        ]);


         PoliticalParty::create([
            'name' => 'Nulo',
            'abbreviation' => 'VN',
            'color_hex' => '#1A1A1A', 
        ]);

        PoliticalParty::create([
            'name' => 'Blanco',
            'abbreviation' => 'VB',
            'color_hex' => '#D3D3D3', 
        ]);

                        
        PoliticalParty::create([
            'name' => 'Unión por la Patria',
            'abbreviation' => 'UXP',
            'color_hex' => '#0099FF', // Celeste
        ]);

        PoliticalParty::create([
            'name' => 'Juntos por el Cambio',
            'abbreviation' => 'JXC',
            'color_hex' => '#FFCC00', // Amarillo
        ]);

        PoliticalParty::create([
            'name' => 'La Libertad Avanza',
            'abbreviation' => 'LLA',
            'color_hex' => '#660099', // Violeta
        ]);

        // -------------------------------------------------------
        // 3. USUARIOS (Tus credenciales)
        // -------------------------------------------------------
        
        // USUARIO ADMIN (TÚ)
        User::create([
            'dni' => '40774466',
            'name' => 'Nicolas',
            'lastname' => 'Capurro',
            'email' => 'admin@fisq.com',
            'password' => Hash::make('admin2398!'), // CLAVE: admin2398!
            'phone' => '3434531520',
            'address' => 'Oficina Central',
            'role' => 'admin',
            'department_id' => $deptosCreados['Paraná']->id // Asignado a Paraná
        ]);

        // USUARIO FISCAL (Para pruebas)
        User::create([
            'dni' => '87654321',
            'name' => 'Juan',
            'lastname' => 'Fiscal',
            'email' => 'fiscal@fisq.com',
            'password' => Hash::make('fiscal2398!'), // CLAVE: fiscal2398!
            'phone' => '345333444',
            'address' => 'Calle 12',
            'role' => 'user',
            'department_id' => $deptosCreados['Concordia']->id // Asignado a Concordia
        ]);
    }
}