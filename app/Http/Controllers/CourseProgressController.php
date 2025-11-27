<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\CourseProgress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

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
        // Asumimos que el modelo User tiene la relación 'enrollments'
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

        // 1. Verificar Inscripción (debe estar inscrito)
        if (! $user->enrollments()->where('course_id', $course->id)->exists()) {
            abort(403, 'Debes estar inscrito para obtener un certificado.');
        }

        // 2. Calcular Progreso
        $totalModules = $course->modules->count();
        if ($totalModules === 0) {
             return back()->with('error', 'El curso no tiene módulos definidos.');
        }

        $completedModules = $user->enrollments->where('course_id', $course->id)->first()->progress
            ->filter(fn($p) => $p->is_completed)->count();
        
        $progressPercent = round(($completedModules / $totalModules) * 100);

        // 3. Verificación de Finalización (100%)
        if ($progressPercent < 100) {
            return back()->with('error', "No has completado el curso al 100%. Progreso actual: {$progressPercent}%.");
        }

        // 4. Lógica de Certificado (Placeholder)
        // En una implementación real, aquí se generaría un PDF con DOMPDF o similar.
        return view('courses.certificate', compact('course', 'user'));
    }
}