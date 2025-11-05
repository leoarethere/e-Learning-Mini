<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content', 'file_path', 'course_id'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function progress()
    {
        return $this->hasMany(Progress::class);
    }

    public function getCompletionRateAttribute()
    {
        $totalStudents = User::where('role', 'mahasiswa')->count();
        $completed = $this->progress()->where('completed', true)->count();
        
        return $totalStudents > 0 ? ($completed / $totalStudents) * 100 : 0;
    }
}