@extends('layouts.app')

@section('title', 'Daftar Mata Kuliah')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Daftar Mata Kuliah</h1>
    
    <div class="row mt-4">
        @foreach($courses as $course)
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">{{ $course->name }}</h5>
                    <h6 class="card-subtitle mb-2 text-muted">{{ $course->code }}</h6>
                    <p class="card-text">{{ Str::limit($course->description, 100) }}</p>
                    <p class="card-text"><small class="text-muted">Dosen: {{ $course->lecturer->name }}</small></p>
                </div>
                <div class="card-footer">
                    <a href="{{ route('courses.show', $course) }}" class="btn btn-primary btn-sm">Lihat Detail</a>
                    @if(auth()->user()->isMahasiswa())
                        <span class="badge bg-secondary float-end">
                            {{ $course->materials->count() }} Materi
                        </span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection