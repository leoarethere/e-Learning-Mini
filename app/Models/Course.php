<?php

namespace App\Models;

use App\Models\User;
use App\Models\Material;
use App\Models\Discussion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'code', 'lecturer_id'];

    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    public function discussions()
    {
        return $this->hasMany(Discussion::class);
    }

    /**
     * GANTI metode lama 'enrolledStudents' dengan yang ini.
     * Ini adalah relasi Many-to-Many ke User,
     * tapi kita filter agar HANYA mengembalikan user dengan role 'mahasiswa'.
     */
    public function enrolledStudents()
    {
        return $this->belongsToMany(User::class, 'course_user')
                    ->where('role', 'mahasiswa');
    }
}