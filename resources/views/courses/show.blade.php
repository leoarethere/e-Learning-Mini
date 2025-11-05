@extends('layouts.app')

@section('title', $course->name)

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">{{ $course->name }}</h1>
    
    <div class="row mt-4">
        <div class="col-lg-8">
            <!-- Materials Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Materi Kuliah</h5>
                    @if(auth()->user()->isDosen() || auth()->user()->isAdmin())
                    <button class="btn btn-primary btn-sm float-end" data-bs-toggle="modal" data-bs-target="#addMaterialModal">
                        Tambah Materi
                    </button>
                    @endif
                </div>
                <div class="card-body">
                    @foreach($materials as $material)
                    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                        <div>
                            <h6 class="mb-1">{{ $material->title }}</h6>
                            <small class="text-muted">{{ $material->created_at->format('d M Y') }}</small>
                        </div>
                        <div>
                            @if(auth()->user()->isMahasiswa() && isset($progress[$material->id]))
                                <span class="badge bg-success me-2">Selesai</span>
                            @endif
                            <a href="{{ route('materials.show', $material) }}" class="btn btn-outline-primary btn-sm">
                                Baca
                            </a>
                            @if(auth()->user()->isDosen() || auth()->user()->isAdmin())
                            <form action="{{ route('materials.destroy', $material) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm" 
                                        onclick="return confirm('Hapus materi ini?')">
                                    Hapus
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Discussions Section -->
            <div class="card">
                <div class="card-header">
                    <h5>Forum Diskusi</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('discussions.store', $course) }}" method="POST" class="mb-4">
                        @csrf
                        <div class="mb-3">
                            <input type="text" name="title" class="form-control" placeholder="Judul diskusi" required>
                        </div>
                        <div class="mb-3">
                            <textarea name="content" class="form-control" rows="3" placeholder="Isi diskusi..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Buat Diskusi</button>
                    </form>

                    @foreach($discussions as $discussion)
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6>{{ $discussion->title }}</h6>
                            <p class="mb-1">{{ $discussion->content }}</p>
                            <small class="text-muted">
                                Oleh: {{ $discussion->user->name }} - 
                                {{ $discussion->created_at->format('d M Y H:i') }}
                            </small>
                            @if(auth()->user()->isDosen() || auth()->user()->isAdmin())
                            <form action="{{ route('discussions.destroy', $discussion) }}" method="POST" class="mt-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    Hapus
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Progress Sidebar -->
        @if(auth()->user()->isMahasiswa())
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5>Progress Belajar</h5>
                </div>
                <div class="card-body">
                    @php
                        $completed = count(array_filter($progress));
                        $total = count($materials);
                        $percentage = $total > 0 ? ($completed / $total) * 100 : 0;
                    @endphp
                    <div class="text-center mb-3">
                        <div class="display-4 fw-bold text-primary">{{ round($percentage) }}%</div>
                        <div class="text-muted">{{ $completed }} dari {{ $total }} materi selesai</div>
                    </div>
                    <div class="progress mb-4" style="height: 20px;">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: {{ $percentage }}%">
                        </div>
                    </div>
                    
                    @foreach($materials as $material)
                    <div class="d-flex align-items-center mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" 
                                   {{ isset($progress[$material->id]) ? 'checked' : '' }} disabled>
                        </div>
                        <span class="ms-2 {{ isset($progress[$material->id]) ? 'text-success' : 'text-muted' }}">
                            {{ $material->title }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Add Material Modal -->
@if(auth()->user()->isDosen() || auth()->user()->isAdmin())
<div class="modal fade" id="addMaterialModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('materials.store', $course) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Materi Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Judul Materi</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Konten</label>
                        <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="file" class="form-label">File Pendukung (opsional)</label>
                        <input type="file" class="form-control" id="file" name="file">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection