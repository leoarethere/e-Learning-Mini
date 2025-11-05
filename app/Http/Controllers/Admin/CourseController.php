<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Course;
use App\Models\Progress;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * Menampilkan daftar semua kursus.
     */
    public function index()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $courses = collect(); // Default koleksi kosong

        if ($user && $user->isMahasiswa()) {
            // Mahasiswa HANYA lihat kursus yang diikuti
            $courses = $user->enrolledCourses()->with('lecturer')->latest()->get();
        } else {
            // Admin & Dosen melihat SEMUA kursus
            $courses = Course::with(['lecturer', 'materials'])->get();
        }

        return view('courses.index', compact('courses'));
    }

    public function show(Course $course)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // --- PEMERIKSAAN KEAMANAN BARU ---
        if ($user && $user->isMahasiswa()) {
            // Periksa apakah user terdaftar di kursus ini
            $isEnrolled = $user->enrolledCourses()->where('course_id', $course->id)->exists();
            
            if (!$isEnrolled) {
                // Jika tidak terdaftar, lempar error 403 (Forbidden)
                abort(403, 'ANDA TIDAK TERDAFTAR DI KURSUS INI.');
            }
        }
        // Admin dan Dosen (pemilik kursus) bisa lolos

        // --- Sisa logika sama seperti sebelumnya ---
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

        return view('courses.show', compact('course', 'materials', 'discussions', 'progress'));                                                                 
    }

    /**
     * Menampilkan formulir untuk membuat kursus baru.
     */
    public function create()
    {
        // Kita perlu daftar Dosen untuk dropdown
        $dosens = User::where('role', 'dosen')->orderBy('name')->get();
        return view('admin.course.create', compact('dosens'));
    }

    /**
     * Menyimpan kursus baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:courses',
            'description' => 'required|string',
            'lecturer_id' => 'required|exists:users,id',
        ]);

        Course::create($validated);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Kursus berhasil dibuat.');
    }

    /**
     * Menampilkan formulir untuk mengedit kursus.
     */
    public function edit(Course $course)
    {
        // Kita juga perlu daftar Dosen di sini
        $dosens = User::where('role', 'dosen')->orderBy('name')->get();
        return view('admin.course.edit', compact('course', 'dosens'));
    }

    /**
     * Memperbarui kursus di database.
     */
    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // Pastikan kode unik, KECUALI untuk kursus ini sendiri
            'code' => ['required', 'string', 'max:50', Rule::unique('courses')->ignore($course->id)],
            'description' => 'required|string',
            'lecturer_id' => 'required|exists:users,id',
        ]);

        $course->update($validated);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Kursus berhasil diperbarui.');
    }

    /**
     * Menghapus kursus dari database.
     */
    public function destroy(Course $course)
    {
        // Asumsi: Migrasi Anda memiliki onDelete('cascade') untuk materi.
        // Jika ya, menghapus kursus juga akan menghapus semua materinya.
        $course->delete();

        return back()->with('success', 'Kursus berhasil dihapus.');
    }

    /**
     * Menampilkan halaman untuk mengelola pendaftaran mahasiswa.
     */
    public function manageEnrollments(Course $course)
    {
        // Ambil semua user yang merupakan mahasiswa
        $allStudents = User::where('role', 'mahasiswa')->orderBy('name')->get();
        
        // Ambil ID mahasiswa yang SUDAH terdaftar di kursus ini
        $enrolledStudentIds = $course->enrolledStudents()->pluck('users.id')->toArray();

        return view('admin.course.enrollments', compact('course', 'allStudents', 'enrolledStudentIds'));
    }

    /**
     * Memperbarui daftar mahasiswa yang terdaftar.
     */
    public function updateEnrollments(Request $request, Course $course)
    {
        $request->validate([
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:users,id', // Pastikan semua ID valid
        ]);

        // 'sync' adalah metode Eloquent yang sempurna untuk pivot table.
        // Ini akan menghapus pendaftaran lama dan menambahkan yang baru
        // berdasarkan array ID yang diberikan.
        $studentIds = $request->input('student_ids', []);
        $course->enrolledStudents()->sync($studentIds);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Pendaftaran mahasiswa berhasil diperbarui.');
    }
}