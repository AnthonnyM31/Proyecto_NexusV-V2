<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModuleController extends Controller
{
    // Usaremos solo create, store, edit y destroy para esta fase.

    /**
     * Muestra el formulario para crear un nuevo módulo para un curso.
     */
    public function create(Course $course)
    {
        // Autorización: Asegura que el vendedor sea el dueño del curso.
        if ($course->user_id !== Auth::id()) {
            abort(403, 'No autorizado para gestionar este curso.');
        }

        return view('courses.modules.create', compact('course'));
    }

    /**
     * Almacena un módulo recién creado.
     */
    public function store(Request $request, Course $course)
    {
        if ($course->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content_url' => 'required|url', 
            'sequence_order' => 'required|integer|min:1',
        ]);

        $course->modules()->create($validated);

        return redirect()->route('seller.courses.edit', $course)->with('success', 'Módulo creado exitosamente.');
    }
    
    /**
     * Muestra el formulario para editar un módulo existente.
     */
    public function edit(Course $course, Module $module): View
    {
        // Autorización: Asegura que el vendedor sea el dueño del curso al que pertenece el módulo.
        if ($course->user_id !== Auth::id()) {
            abort(403, 'No autorizado para gestionar este módulo.');
        }

        // Asegura que el módulo pertenece al curso correcto
        if ($module->course_id !== $course->id) {
             abort(404);
        }

        return view('courses.modules.edit', compact('course', 'module'));
    }

    /**
     * Actualiza un módulo existente.
     */
    public function update(Request $request, Course $course, Module $module): RedirectResponse
    {
        // Autorización: Asegura que el vendedor sea el dueño.
        if ($course->user_id !== Auth::id() || $module->course_id !== $course->id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content_url' => 'required|url', 
            'sequence_order' => 'required|integer|min:1',
        ]);

        $module->update($validated);

        return redirect()->route('seller.courses.edit', $course)->with('success', 'Módulo actualizado exitosamente.');
    }

    /**
     * Elimina un módulo y sus registros de progreso asociados.
     */
    public function destroy(Course $course, Module $module): RedirectResponse
    {
        // Autorización: Asegura que el vendedor sea el dueño.
        if ($course->user_id !== Auth::id() || $module->course_id !== $course->id) {
            abort(403);
        }

        // Elimina el módulo (y los registros de progreso asociados, gracias al onDelete('cascade') en la migración).
        $module->delete();

        return redirect()->route('seller.courses.edit', $course)->with('success', 'Módulo eliminado exitosamente.');
    }
}