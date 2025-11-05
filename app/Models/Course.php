<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function enrolledStudents()
    {
        // Ini contoh sederhana, bisa dikembangkan dengan tabel enrollment
        return User::where('role', 'mahasiswa')->get();
    }
}