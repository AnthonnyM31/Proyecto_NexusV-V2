<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/AnthonnyM31/Proyecto_NexusV-V2"><img src="https://img.shields.io/badge/Status-Development-blue" alt="Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/badge/Framework-Laravel%2011%2B-red" alt="Laravel Version"></a>
<a href="https://github.com/AnthonnyM31/Proyecto_NexusV-V2/blob/main/LICENSE"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# 🚀 Proyecto NexusV-V2: Plataforma de Cursos con Roles

NexusV-V2 es una plataforma de ingeniería web desarrollada en **Laravel** que simula un sistema de venta y gestión de cursos en tiempo real, caracterizada por la implementación de **roles diferenciados** de **Vendedor** y **Comprador** para la administración de contenido y la inscripción a cursos.

---

## 💻 Stack Tecnológico

| Componente | Versión | Propósito |
| :--- | :--- | :--- |
| **Framework** | Laravel 11+ | Backend PHP (Lógica de negocio y API) |
| **Frontend** | Blade + Vite + Tailwind CSS | Interfaz de Usuario, estilos y compilación de assets. |
| **Base de Datos** | SQLite (Desarrollo) / PostgreSQL (Producción) | Almacenamiento de datos. |
| **Autenticación** | Laravel Breeze | Sistema de login y registro multi-rol. |

---

## 💡 Problemas Críticos Enfrentados (Lecciones Aprendidas)

El desarrollo inicial se encontró con desafíos significativos relacionados con la estabilidad del entorno y la configuración de Laravel Breeze:

* **Errores Cíclicos en Entorno Windows:** Constantes `BindingResolutionException` y `BadMethodCallException` debido a la inestabilidad del entorno de desarrollo (Windows/Composer).
* **Integridad de Clases de Breeze:** Controladores esenciales de autenticación (`ProfileController`, `AuthenticatedSessionController`) fallaron durante la instalación de Breeze, bloqueando las rutas de autenticación.
* **Reestructuración de Rutas:** Fue necesario reestructurar el *middleware* y **eliminar los alias de rutas** en las vistas (e.g., de `route('seller.courses.index')` a `/seller/courses`) para forzar la carga correcta del sistema.
* **Corrupción de Base de Datos:** Fallos ambientales causaron que la tabla `enrollments` se creara sin las claves foráneas necesarias (`course_id`, `user_id`), requiriendo el uso de `php artisan migrate:fresh`.

---

## 🛠️ Guía Rápida de Instalación Local

Esta guía asume que tienes **PHP (8.2+)**, **Composer**, y **Node.js (con NPM)** instalados en tu máquina.

### Paso 1: Clonar el Repositorio

Abre tu terminal y descarga el proyecto:

```bash
git clone [https://github.com/AnthonnyM31/Proyecto_NexusV-V2.git](https://github.com/AnthonnyM31/Proyecto_NexusV-V2.git)
cd Proyecto_NexusV-V2



Paso 2: Configuración del Entorno
Copia el archivo de entorno y genera la clave de la aplicación:

copy .env.example .env
php artisan key:generate



Paso 3: Instalación de Dependencias
Instala las bibliotecas de PHP y JavaScript:

composer install
npm install





Paso 4: Configuración y Migración de la Base de Datos
El proyecto utiliza SQLite para el desarrollo local. Crea el archivo de base de datos y aplica las migraciones:

touch database/database.sqlite
php artisan migrate



⚠️ Paso de Estabilización (Si hay errores)
Si encuentras errores de "clase no existe" o "ruta no definida", ejecuta la siguiente secuencia para limpiar cachés corruptas:

php artisan optimize:clear
composer dump-autoload -o




Paso 5: Ejecutar la Aplicación (Dos Terminales)
Necesitas abrir dos terminales en la raíz del proyecto (NexusV-V2):

Terminal 1 (Backend - PHP),Terminal 2 (Frontend - Vite)
php artisan serve,npm run dev


URL de Acceso Local: http://127.0.0.1:8000



🧪 Flujo de Pruebas Funcionales
Verifica la funcionalidad clave siguiendo estos pasos:

Registro y Login: Accede a /register y verifica que el dropdown "Registrarse como" funcione.

Rol Vendedor:

Regístrate como Vendedor.

Sube un curso.

Confirma que el curso aparezca como Publicado.

Rol Comprador:

Regístrate como Comprador.

Ve a Explorar Cursos y haz clic en el curso del vendedor.

Haz clic en INSCRIBIRSE AHORA.

Verificación: El Comprador debe ver el curso en su Dashboard bajo la sección "Mis Cursos Inscritos".



🌎 Despliegue y Repositorio
El proyecto está configurado para desplegar en Render.

Repositorio del Proyecto: https://github.com/AnthonnyM31/Proyecto_NexusV-V2

Link del Deploy (Ejemplo): https://nexusv-web-service.onrender.com/
