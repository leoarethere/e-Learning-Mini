<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\Material;
use App\Models\Progress;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Pastikan ini ada

class MaterialController extends Controller
{
    public function show(Material $material)
    {
        $course = $material->course;

        // Update progress jika mahasiswa
        /** @var User|null $user */
        $user = Auth::user();
        if ($user && $user->isMahasiswa()) {
            Progress::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'material_id' => $material->id
                ],
                [
                    'completed' => true,
                    'completed_at' => now()
                ]
            );
        }

        return view('materials.show', compact('material', 'course'));
    }

    /**
     * Menyimpan materi baru ke kursus.
     */
    public function store(Request $request, Course $course)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // --- PEMERIKSAAN OTORISASI ---
        // Periksa apakah user adalah Dosen DAN ID-nya sama dengan pemilik kursus
        // Kita juga izinkan Admin (opsional, tapi praktik yang baik)
        $isLecturer = ($user && $user->id === $course->lecturer_id);
        $isAdmin = ($user && $user->isAdmin());

        if (!$isLecturer && !$isAdmin) {
            abort(403, 'ANDA TIDAK BERHAK MENAMBAHKAN MATERI KE KURSUS INI.');
        }
        // --- AKHIR PEMERIKSAAN ---

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'file' => 'nullable|file|max:10240',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('materials', 'public');
        }

        $course->materials()->create([
            'title' => $request->title,
            'content' => $request->content,
            'file_path' => $filePath,
        ]);

        return redirect()->route('courses.show', $course)
            ->with('success', 'Materi berhasil ditambahkan');
    }

    /**
     * Menghapus materi.
     */
    public function destroy(Material $material)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $course = $material->course; // Ambil kursus terkait

        // --- PEMERIKSAAN OTORISASI ---
        // Periksa apakah user adalah Dosen pemilik kursus ATAU seorang Admin
        $isLecturer = ($user && $user->id === $course->lecturer_id);
        $isAdmin = ($user && $user->isAdmin());
        
        if (!$isLecturer && !$isAdmin) {
            abort(403, 'ANDA TIDAK BERHAK MENGHAPUS MATERI INI.');
        }
        // --- AKHIR PEMERIKSAAN ---

        if ($material->file_path) {
            Storage::disk('public')->delete($material->file_path);
        }

        $material->delete();

        return back()->with('success', 'Materi berhasil dihapus');
    }
}