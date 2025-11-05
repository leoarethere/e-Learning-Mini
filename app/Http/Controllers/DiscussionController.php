<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Discussion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscussionController extends Controller
{
    public function store(Request $request, Course $course)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $course->discussions()->create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('courses.show', $course)
            ->with('success', 'Diskusi berhasil dibuat');
    }

    public function destroy(Discussion $discussion)
    {
        $discussion->delete();
        return back()->with('success', 'Diskusi berhasil dihapus');
    }
}