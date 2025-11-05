<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Material;
use App\Models\Discussion;
use App\Models\Progress;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::where('role', '!=', 'admin')->count(),
            'total_dosen' => User::where('role', 'dosen')->count(),
            'total_mahasiswa' => User::where('role', 'mahasiswa')->count(),
            'total_courses' => Course::count(),
            'total_materials' => Material::count(),
            'total_discussions' => Discussion::count(),
        ];

        // Recent activities
        $recentMaterials = Material::with('course')->latest()->take(5)->get();
        $recentDiscussions = Discussion::with(['user', 'course'])->latest()->take(5)->get();
        
        // Course statistics
        $courseStats = Course::withCount(['materials', 'discussions'])->get();

        return view('admin.dashboard', compact('stats', 'recentMaterials', 'recentDiscussions', 'courseStats'));
    }

    public function activityMonitoring()
    {
        $students = User::where('role', 'mahasiswa')->withCount(['progress as completed_materials' => function($query) {
            $query->where('completed', true);
        }])->get();

        $materialsCount = Material::count();

        foreach ($students as $student) {
            $student->completion_rate = $materialsCount > 0 
                ? ($student->completed_materials / $materialsCount) * 100 
                : 0;
        }

        return view('admin.monitoring', compact('students', 'materialsCount'));
    }
}