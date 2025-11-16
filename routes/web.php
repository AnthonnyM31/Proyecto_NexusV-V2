<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Seller\CourseController as SellerCourseController; // Controlador del Vendedor
use App\Http\Controllers\CourseController as PublicCourseController; // Controlador Público (Compradores)
use App\Http\Controllers\EnrollmentController; // Controlador de Inscripciones
use App\Http\Controllers\Admin\UserController; // Importación para gestión de usuarios Admin
use App\Http\Controllers\Admin\AdminController; // Importación para gestión global de cursos/inscripciones
use Illuminate\Support\Facades\Route;
use App\Models\Enrollment;

// --------------------------------------------------------------------------------------
// 1. RUTAS PÚBLICAS Y REDIRECCIÓN DE INICIO (/)
// --------------------------------------------------------------------------------------

// Redirección de la ruta principal:
Route::get('/', function () {
    // Si el usuario está autenticado, enviarlo al dashboard (que redirigirá por rol).
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    // Si no está autenticado, lo enviamos a la vista pública de cursos.
    return redirect()->route('courses.index'); 
});

// Rutas Públicas de Cursos (Listado y Detalle)
Route::get('/courses', [PublicCourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{course}', [PublicCourseController::class, 'show'])->name('courses.show');


// --------------------------------------------------------------------------------------
// 2. GRUPO DE RUTAS PROTEGIDAS POR AUTENTICACIÓN
// --------------------------------------------------------------------------------------
Route::middleware('auth')->group(function () {
    
    // Rutas de Perfil (ProfileController)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Rutas de Inscripción (Enrollment)
    Route::post('/enroll/{course}', [EnrollmentController::class, 'store'])
        ->name('enroll.store');

    // =======================================================
    // 5. NUEVAS RUTAS DE ADMINISTRACIÓN (Panel de Control Total)
    // Protegidas por el Gate 'manage-system'
    // =======================================================
    
    Route::middleware('can:manage-system')->prefix('admin')->group(function () {
        
        // Gestión de Usuarios (CRUD - Listar, Crear, Editar, Eliminar)
        Route::resource('users', UserController::class)
            ->names('admin.users') 
            ->only(['index', 'create', 'edit', 'update', 'destroy']);
        
        // Ruta para crear un nuevo Administrador Secundario (POST)
        Route::post('users/create-admin', [UserController::class, 'storeAdmin'])
            ->name('admin.users.store-admin');

        // GESTIÓN GLOBAL DE CURSOS
        Route::get('courses', [AdminController::class, 'indexCourses'])->name('admin.courses.index');
        Route::delete('courses/{course}', [AdminController::class, 'destroyCourse'])->name('admin.courses.destroy'); // 👈 RUTA DELETE AÑADIDA
        
        // GESTIÓN GLOBAL DE INSCRIPCIONES
        Route::get('enrollments', [AdminController::class, 'indexEnrollments'])->name('admin.enrollments.index');
        Route::delete('enrollments/{enrollment}', [AdminController::class, 'destroyEnrollment'])->name('admin.enrollments.destroy'); // 👈 RUTA DELETE AÑADIDA
    });

    // 4. RUTAS PARA VENDEDORES (Gestión de Cursos)
    Route::resource('seller/courses', 'App\Http\Controllers\Seller\CourseController')
    ->names('seller.courses')
    ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
    ->middleware('can:is-seller');
    
    // Ruta Dashboard (con Lógica de Redirección por Rol ACTUALIZADA)
    Route::get('/dashboard', function () {
        $user = auth()->user();

        // 1. Redirección del Administrador
        if ($user->isAdmin()) {
            return redirect()->route('admin.users.index'); // <-- Redirigir al panel de control de usuarios
        }

        // 2. Redirección del Vendedor (a su gestión de cursos)
        if ($user->isSeller()) {
            // Usamos la URL directa para evitar el fallo persistente del alias 'seller.courses.index'
            return redirect('/seller/courses'); 
        }

        // 3. Lógica del Comprador (Dashboard)
        // Cargar los cursos en los que el usuario está inscrito
        $enrollments = Enrollment::with('course.user') // Incluir el curso y el instructor
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('dashboard', compact('enrollments'));
    })->middleware(['verified'])->name('dashboard');

});
// --------------------------------------------------------------------------------------


// 3. RUTAS DE AUTENTICACIÓN DE BREEZE (Login, Register, etc.)
require __DIR__.'/auth.php';