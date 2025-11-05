<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\Material;
use App\Models\Progress;
use App\Models\Discussion;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with(['lecturer', 'materials'])->get();
        return view('courses.index', compact('courses'));
    }

    /**
     * Menampilkan detail kursus.
     */
    public function show(Course $course)
    {
        /** @var User|null $user */
        $user = Auth::user();

        // Keamanan untuk Mahasiswa (dari langkah sebelumnya)
        if ($user && $user->isMahasiswa()) {
            $isEnrolled = $user->enrolledCourses()->where('course_id', $course->id)->exists();
            if (!$isEnrolled) {
                abort(403, 'ANDA TIDAK TERDAFTAR DI KURSUS INI.');
            }
        }

        // --- FLAG BARU UNTUK DOSEN/ADMIN ---
        // Periksa apakah user yang login adalah Dosen pemilik kursus ATAU Admin
        $isLecturer = false; // Default false
        if ($user) {
            if ($user->isAdmin() || ($user->isDosen() && $user->id === $course->lecturer_id)) {
                $isLecturer = true;
            }
        }
        // --- AKHIR FLAG ---

        $materials = $course->materials;
        $discussions = $course->discussions()->with('user')->latest()->get();   

        // Progress tracking untuk mahasiswa
        $progress = [];
        if ($user && $user->isMahasiswa()) {
            $progress = Progress::where('user_id', Auth::id())
                ->whereIn('material_id', $materials->pluck('id'))
                ->pluck('completed', 'material_id')
                ->toArray();
        }

        // TAMBAHKAN $isLecturer ke compact()
        return view('courses.show', compact('course', 'materials', 'discussions', 'progress', 'isLecturer'));                                                                 
    }
}