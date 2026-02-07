# FisQ - Sistema de Gestión y Escrutinio Electoral

![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)

## Descripción del Proyecto

**FisQ** es una solución integral desarrollada para la administración, fiscalización y visualización de procesos electorales en tiempo real. 

El sistema permite gestionar la estructura electoral completa (Escuelas, Mesas, Partidos, Candidatos) y realizar el escrutinio de votos de manera digital, segura y auditable. Diseñado para ofrecer transparencia y agilidad, cuenta con módulos de visualización gráfica de resultados, pistas de auditoría (Logs) y sistemas de respaldo automatizados.

Este proyecto fue desarrollado como trabajo final para la carrera de **Analista de Sistemas**.

---


## Características Principales

### 📊 Dashboard de Resultados en Vivo
- Visualización gráfica interactiva (**Chart.js**) de la distribución de votos.
- **Filtros dinámicos** por Departamento.
- **Barras de progreso** en tiempo real que indican el porcentaje de mesas escrutadas.
- Cálculo automático de porcentajes sobre votos válidos.

### 🗳️ Gestión Electoral
- ABM (Alta, Baja, Modificación) de Escuelas, Mesas y Partidos Políticos.
- Asignación de Fiscales a Mesas específicas (Individual y Masiva).
- Buscador inteligente de fiscales con **Select2**.
- Carga de actas de escrutinio con validación de datos.

### 🛡️ Seguridad y Auditoría
- **Roles y Permisos:** Diferenciación estricta entre Administradores y Fiscales.
- **Módulo de Logs:** Registro inmutable de acciones ("Quién hizo qué y cuándo") con diferenciación visual de eventos.
- **Backup System:** Generación manual de copias de seguridad (`.sql`) utilizando procesos nativos del sistema, con descarga directa desde el panel administrativo.

---

## 🛠️ Tecnologías Utilizadas

* **Backend:** PHP 8.3, Laravel 11.
* **Base de Datos:** MySQL 8.0+.
* **Frontend:** Blade Templates, Bootstrap 5.
* **Librerías Clave:**
    * `chart.js`: Gráficos estadísticos.
    * `select2`: Búsquedas asíncronas en listas desplegables.
    * `fontawesome`: Iconografía.

---

## ⚙️ Instalación y Configuración

Sigue estos pasos para levantar el proyecto en un entorno local:

### Prerrequisitos
- PHP >= 8.3 (Extensiones requeridas: `zip`, `fileinfo`, `pdo_mysql`, `mbstring`).
- Composer.
- **MySQL Server 8.0** o superior (Requerido para el módulo de Backups).

### Pasos
1.  **Clonar el repositorio:**
    ```bash
    git clone [https://github.com/NCapurro/FisQ.git](https://github.com/NCapurro/FisQ.git)
    cd fisq
    ```

2.  **Instalar dependencias:**
    ```bash
    composer install
    npm install && npm run build
    ```

3.  **Configurar entorno:**
    - Copiar el archivo de entorno: `cp .env.example .env`
    - Configurar la base de datos en el archivo `.env`:
    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1  <-- Importante: Usar IP numérica
    DB_PORT=3306
    DB_DATABASE=fisq
    DB_USERNAME=root
    DB_PASSWORD=
    ```

4.  **Generar clave de aplicación:**
    ```bash
    php artisan key:generate
    ```

5.  **Migrar y Sembrar base de datos:**
    ```bash
    php artisan migrate --seed
    ```

6.  **Configurar Backups (Windows):**
    El sistema utiliza `mysqldump` nativo. Asegúrate de que MySQL Server 8.0 esté instalado en la ruta por defecto (`C:\Program Files\MySQL\MySQL Server 8.0\bin\mysqldump.exe`).
    *Nota: Si tu instalación está en otra ruta, deberás ajustar la variable `$mysqldumpPath` en `BackupController.php`.*

7.  **Ejecutar servidor:**
    ```bash
    php artisan serve
    ```

### 🔐 Credenciales por Defecto (Seeders)
* **Admin:** `admin@admin.com` / `admin123`
* **Fiscal:** `fiscal@fiscal.com` / `fiscal123`

---

## 🎓 Información Académica

* **Institución:** Facultad de Ciencia y Tecnología - UADER
* **Carrera:** Analista de Sistemas
* **Cátedra:** Taller de Integración
* **Alumno:** Nicolás Estanislao Capurro
* **Año:** 2026

---

## 📄 Licencia

Este proyecto es de código abierto y está bajo la licencia [MIT](https://opensource.org/licenses/MIT).