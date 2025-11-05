<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Material;
use App\Models\Progress;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

    public function store(Request $request, Course $course)
    {
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

    public function destroy(Material $material)
    {
        if ($material->file_path) {
            Storage::disk('public')->delete($material->file_path);
        }

        $material->delete();

        return back()->with('success', 'Materi berhasil dihapus');
    }
}