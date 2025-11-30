<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
// Ya no necesitamos HasMany aquí si eliminamos la relación 'progress'
// use Illuminate\Database\Eloquent\Relations\HasMany; 

class Enrollment extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'course_id',
        'status', // 'paid', 'pending', etc.
    ];

    /**
     * Get the user (buyer) associated with the enrollment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course associated with the enrollment.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
    
    // NOTA: Se ha eliminado la relación 'progress()' de este modelo, 
    // ya que la lógica para calcular el progreso completo se movió al 
    // CourseProgressController para una consulta más eficiente y precisa.
}