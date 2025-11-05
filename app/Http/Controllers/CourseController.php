<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Material;
use App\Models\Progress;
use App\Models\Discussion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with(['lecturer', 'materials'])->get();
        return view('courses.index', compact('courses'));
    }

    public function show(Course $course)
    {
        $materials = $course->materials;
        $discussions = $course->discussions()->with('user')->latest()->get();   

        // Progress tracking untuk mahasiswa
        $progress = [];
        /** @var User|null $user */
        $user = Auth::user();
        if ($user && $user->isMahasiswa()) {
            $progress = Progress::where('user_id', Auth::id())
                ->whereIn('material_id', $materials->pluck('id'))
                ->pluck('completed', 'material_id')
                ->toArray();
        }

        return view('courses.show', compact('course', 'materials', 'discussions', 'progress'));                                                                 
    }
}