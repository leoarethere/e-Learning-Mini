<?php

namespace App\Http\Controllers;

use App\Models\Progress;
use App\Models\Material;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgressController extends Controller
{
    public function store(Request $request, Material $material)
    {
        $request->validate([
            'completed' => 'required|boolean',
        ]);

        $progress = Progress::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'material_id' => $material->id
            ],
            [
                'completed' => $request->completed,
                'completed_at' => $request->completed ? now() : null
            ]
        );

        return response()->json([
            'success' => true,
            'progress' => $progress
        ]);
    }

    public function getStudentProgress($studentId = null)
    {
        $userId = $studentId ?? Auth::id();

        $progress = Progress::where('user_id', $userId)
            ->with('material.course')
            ->get()
            ->groupBy('material.course.name');

        $stats = [
            'total_materials' => Material::count(),
            'completed_materials' => Progress::where('user_id', $userId)        
                ->where('completed', true)
                ->count(),
            'completion_rate' => 0
        ];

        if ($stats['total_materials'] > 0) {
            $stats['completion_rate'] = ($stats['completed_materials'] / $stats['total_materials']) * 100;                                                      
        }

        return response()->json([
            'progress' => $progress,
            'stats' => $stats
        ]);
    }
}