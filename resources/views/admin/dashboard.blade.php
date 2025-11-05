@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Admin Dashboard</h1>
    
    <!-- Statistics Cards -->
    <div class="row mt-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_users'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Mata Kuliah</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_courses'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Materi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_materials'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Diskusi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_discussions'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Materials -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Materi Terbaru</h6>
                </div>
                <div class="card-body">
                    @foreach($recentMaterials as $material)
                    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                        <div>
                            <h6 class="mb-1">{{ $material->title }}</h6>
                            <small class="text-muted">{{ $material->course->name }}</small>
                        </div>
                        <small class="text-muted">{{ $material->created_at->diffForHumans() }}</small>
                    </div>
                    @endforeach
                    @if($recentMaterials->isEmpty())
                    <p class="text-muted">Belum ada materi</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Discussions -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Diskusi Terbaru</h6>
                </div>
                <div class="card-body">
                    @foreach($recentDiscussions as $discussion)
                    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                        <div>
                            <h6 class="mb-1">{{ Str::limit($discussion->title, 40) }}</h6>
                            <small class="text-muted">Oleh: {{ $discussion->user->name }} - {{ $discussion->course->name }}</small>
                        </div>
                        <small class="text-muted">{{ $discussion->created_at->diffForHumans() }}</small>
                    </div>
                    @endforeach
                    @if($recentDiscussions->isEmpty())
                    <p class="text-muted">Belum ada diskusi</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Course Statistics -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistik Mata Kuliah</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Mata Kuliah</th>
                                    <th>Dosen</th>
                                    <th>Jumlah Materi</th>
                                    <th>Jumlah Diskusi</th>
                                    <th>Tanggal Dibuat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($courseStats as $course)
                                <tr>
                                    <td>{{ $course->name }}</td>
                                    <td>{{ $course->lecturer->name }}</td>
                                    <td>{{ $course->materials_count }}</td>
                                    <td>{{ $course->discussions_count }}</td>
                                    <td>{{ $course->created_at->format('d M Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary { border-left: 0.25rem solid #4e73df !important; }
.border-left-success { border-left: 0.25rem solid #1cc88a !important; }
.border-left-info { border-left: 0.25rem solid #36b9cc !important; }
.border-left-warning { border-left: 0.25rem solid #f6c23e !important; }
</style>
@endsection