<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('lecturer')->latest()->get();
        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        $lecturers = User::where('role', 'dosen')->get();
        return view('admin.courses.create', compact('lecturers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'code' => 'required|string|unique:courses,code',
            'lecturer_id' => 'required|exists:users,id',
        ]);

        Course::create($request->all());

        return redirect()->route('admin.courses.index')
            ->with('success', 'Mata kuliah berhasil dibuat');
    }

    public function edit(Course $course)
    {
        $lecturers = User::where('role', 'dosen')->get();
        return view('admin.courses.edit', compact('course', 'lecturers'));
    }

    public function update(Request $request, Course $course)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'code' => 'required|string|unique:courses,code,' . $course->id,
            'lecturer_id' => 'required|exists:users,id',
        ]);

        $course->update($request->all());

        return redirect()->route('admin.courses.index')
            ->with('success', 'Mata kuliah berhasil diupdate');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('admin.courses.index')
            ->with('success', 'Mata kuliah berhasil dihapus');
    }
}