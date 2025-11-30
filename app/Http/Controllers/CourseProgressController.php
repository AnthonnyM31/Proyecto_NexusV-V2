<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\CourseProgress;
use App\Models\Course; // Importación necesaria para el Type Hinting en certify
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Models\Enrollment; // Añadimos la importación de Enrollment para usarlo de forma explícita

class CourseProgressController extends Controller
{
    // Constructor con middleware 'auth' ya que es un endpoint de usuario
    public function __construct()
    {
        $this->middleware('auth'); 
    }

    /**
     * Registra o actualiza el progreso de un usuario en un módulo.
     */
    public function store(Module $module): RedirectResponse
    {
        $user = Auth::user();

        // 1. Autorización: Asegurar que el usuario esté inscrito en el curso antes de registrar progreso.
        $isEnrolled = $user->enrollments()->where('course_id', $module->course_id)->exists();
        
        if (! $isEnrolled) {
            return back()->with('error', 'Debes estar inscrito para registrar tu progreso.');
        }

        // 2. Lógica de Registro: Usar updateOrCreate para evitar duplicados
        CourseProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'module_id' => $module->id,
            ],
            [
                'is_completed' => true // Marcamos como completado
            ]
        );

        return back()->with('success', '¡Lección marcada como completada! Buen trabajo.');
    }


    /**
     * Verifica el progreso y genera el certificado si el curso está 100% completado.
     */
    public function certify(Course $course)
    {
        $user = Auth::user();

        // 1. Verificar Inscripción de forma segura (si no está inscrito, aborta)
        $isEnrolled = Enrollment::where('user_id', $user->id)
                            ->where('course_id', $course->id)
                            ->exists();

     if (! $isEnrolled) {
        abort(403, 'Debes estar inscrito para obtener un certificado.');
     }
    
     // 2. Cargar Módulos y el progreso del usuario
     $modules = $course->modules()
        ->with(['progress' => function ($query) use ($user) {
            // Carga solo el progreso relevante para el usuario actual.
            // Asumo que 'progress' es una relación en el modelo Module 
            // que apunta a la tabla course_progresses
            $query->where('user_id', $user->id); 
        }])
        ->get();

     // 3. Calcular Progreso (como en CourseController@content)
     $totalModules = $modules->count();
    
        if ($totalModules === 0) {
        return back()->with('error', 'El curso no tiene módulos definidos.');
     }

     $completedModules = $modules->filter(fn($m) => 
        $m->progress->isNotEmpty() && $m->progress->first()->is_completed
     )->count();
     
     $progressPercent = round(($completedModules / $totalModules) * 100);

     // 4. Verificación de Finalización (100%)
        if ($progressPercent < 100) {
        return back()->with('error', "No has completado el curso al 100%. Progreso actual: {$progressPercent}%.");
     }

        // 5. Lógica de Certificado (Ahora sí, si pasa el 100%)
     return view('courses.certificate', compact('course', 'user'));
    }
}