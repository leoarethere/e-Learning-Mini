<?php

namespace App\Models;

use App\Models\Course;
use App\Models\Progress;
use App\Models\Discussion;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Kursus yang diajar oleh user ini (jika user adalah Dosen).
     */
    public function taughtCourses()
    {
        // Ganti nama relasi 'courses()' lama Anda menjadi ini
        return $this->hasMany(Course::class, 'lecturer_id');
    }

    /**
     * Kursus yang diikuti oleh user ini (jika user adalah Mahasiswa).
     */
    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'course_user');
    }

    /**
     * Relasi lama 'courses()' sudah diganti dengan 'taughtCourses()'
     * Kita hapus/ganti nama 'public function courses()'
     */

    public function discussions()
    {
        return $this->hasMany(Discussion::class);
    }

    public function progress()
    {
        return $this->hasMany(Progress::class);
    }

    // Helpers
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isDosen()
    {
        return $this->role === 'dosen';
    }

    public function isMahasiswa()
    {
        return $this->role === 'mahasiswa';
    }
}