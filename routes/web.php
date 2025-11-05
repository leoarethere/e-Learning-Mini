<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    /** @var \App\Models\User|null $user */
    $user = Auth::user();
    if ($user && $user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Courses
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');

    // Materials
    Route::get('/materials/{material}', [MaterialController::class, 'show'])->name('materials.show');
    
    // Progress Tracking
    Route::post('/progress/{material}', [ProgressController::class, 'store'])->name('progress.store');
    Route::get('/progress/student/{student?}', [ProgressController::class, 'getStudentProgress'])->name('progress.student');

    // Discussions
    Route::post('/courses/{course}/discussions', [DiscussionController::class, 'store'])->name('discussions.store');
    Route::delete('/discussions/{discussion}', [DiscussionController::class, 'destroy'])->name('discussions.destroy');
});

// Dosen Routes
Route::middleware(['auth', 'role:dosen'])->group(function () {
    Route::post('/courses/{course}/materials', [MaterialController::class, 'store'])->name('materials.store');
    Route::delete('/materials/{material}', [MaterialController::class, 'destroy'])->name('materials.destroy');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/monitoring', [AdminDashboardController::class, 'activityMonitoring'])->name('monitoring');
    
    // User Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    
    // Course Management (uncomment when AdminCourseController is created)
    // Route::get('/courses', [AdminCourseController::class, 'index'])->name('courses.index');
    // Route::get('/courses/create', [AdminCourseController::class, 'create'])->name('courses.create');
    // Route::post('/courses', [AdminCourseController::class, 'store'])->name('courses.store');
    // Route::get('/courses/{course}/edit', [AdminCourseController::class, 'edit'])->name('courses.edit');
    // Route::put('/courses/{course}', [AdminCourseController::class, 'update'])->name('courses.update');
    // Route::delete('/courses/{course}', [AdminCourseController::class, 'destroy'])->name('courses.destroy');
});

require __DIR__.'/auth.php';