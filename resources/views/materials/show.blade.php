@extends('layouts.app')

@section('title', $material->title)

@section('content')
<div class="container-fluid px-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Mata Kuliah</a></li>
            <li class="breadcrumb-item"><a href="{{ route('courses.show', $course) }}">{{ $course->name }}</a></li>
            <li class="breadcrumb-item active">{{ $material->title }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">{{ $material->title }}</h4>
                    <small>Mata Kuliah: {{ $course->name }} | Dosen: {{ $course->lecturer->name }}</small>
                </div>
                <div class="card-body">
                    <div id="material-content" data-material-id="{{ $material->id }}">
                        {!! nl2br(e($material->content)) !!}
                    </div>

                    @if($material->file_path)
                    <div class="mt-4">
                        <h6><i class="fas fa-paperclip"></i> File Lampiran:</h6>
                        <a href="{{ Storage::url($material->file_path) }}" class="btn btn-outline-primary btn-sm" target="_blank">
                            <i class="fas fa-download"></i> Download File
                        </a>
                    </div>
                    @endif
                </div>
                <div class="card-footer">
                    @if(auth()->user()->isMahasiswa())
                    <button class="btn btn-success" onclick="markAsCompleted({{ $material->id }})">
                        <i class="fas fa-check-circle"></i> Tandai Sudah Dibaca
                    </button>
                    @endif
                    
                    <small class="text-muted float-end">
                        Diperbarui: {{ $material->updated_at->format('d M Y H:i') }}
                    </small>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Progress Info -->
            @if(auth()->user()->isMahasiswa())
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-tasks"></i> Status Pembelajaran</h6>
                </div>
                <div class="card-body">
                    @php
                        $progress = \App\Models\Progress::where('user_id', auth()->id())
                            ->where('material_id', $material->id)
                            ->first();
                    @endphp
                    
                    @if($progress && $progress->completed)
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> 
                        <strong>Sudah diselesaikan</strong>
                        <br>
                        <small>Pada: {{ $progress->completed_at->format('d M Y H:i') }}</small>
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <i class="fas fa-clock"></i> 
                        <strong>Belum diselesaikan</strong>
                        <p class="mb-0 mt-2">Scroll sampai bawah untuk menandai sebagai selesai</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Course Materials List -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-list"></i> Daftar Materi Lainnya</h6>
                </div>
                <div class="card-body">
                    @foreach($course->materials as $mat)
                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 
                        {{ $mat->id == $material->id ? 'bg-light rounded' : '' }}">
                        <div>
                            <a href="{{ route('materials.show', $mat) }}" 
                               class="{{ $mat->id == $material->id ? 'fw-bold' : '' }}">
                                {{ $mat->title }}
                            </a>
                        </div>
                        @if(auth()->user()->isMahasiswa())
                            @php
                                $matProgress = \App\Models\Progress::where('user_id', auth()->id())
                                    ->where('material_id', $mat->id)
                                    ->where('completed', true)
                                    ->exists();
                            @endphp
                            @if($matProgress)
                            <span class="badge bg-success"><i class="fas fa-check"></i></span>
                            @endif
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection