<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $course->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="row">
                <div class="col-lg-8">
                    <div class="bg-white shadow-sm sm:rounded-lg mb-4">
                        <div class="p-6 border-b border-gray-200 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Materi Kuliah</h5>
                            
                            {{-- PERBAIKAN: Menggunakan $isLecturer dari controller --}}
                            @if($isLecturer)
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addMaterialModal">
                                Tambah Materi
                            </button>
                            @endif
                        </div>
                        <div class="p-6">
                            @forelse($materials as $material)
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
                                    
                                    {{-- PERBAIKAN: Menggunakan $isLecturer dari controller --}}
                                    @if($isLecturer)
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
                            @empty
                            <p class="text-muted">Belum ada materi yang ditambahkan.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <h5 class="mb-0">Forum Diskusi</h5>
                        </div>
                        <div class="p-6">
                            <form action="{{ route('discussions.store', $course) }}" method="POST" class="mb-4">
                                @csrf
                                <div class="mb-3">
                                    <x-text-input type="text" name="title" class="form-control" placeholder="Judul diskusi" required />
                                </div>
                                <div class="mb-3">
                                    <textarea name="content" class="form-control border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3" placeholder="Isi diskusi..." required></textarea>
                                </div>
                                <x-primary-button type="submit">Buat Diskusi</x-primary-button>
                            </form>

                            @forelse($discussions as $discussion)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6>{{ $discussion->title }}</h6>
                                    <p class="mb-1">{{ $discussion->content }}</p>
                                    <small class="text-muted">
                                        Oleh: {{ $discussion->user->name }} - 
                                        {{ $discussion->created_at->format('d M Y H:i') }}
                                    </small>
                                    
                                    {{-- PERBAIKAN: Hanya dosen pengajar/admin yang bisa hapus diskusi --}}
                                    @if($isLecturer || $discussion->user_id == auth()->id())
                                    <form action="{{ route('discussions.destroy', $discussion) }}" method="POST" class="mt-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus diskusi ini?');">
                                            Hapus
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                            @empty
                            <p class="text-muted">Belum ada diskusi.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                @if(auth()->user()->isMahasiswa())
                <div class="col-lg-4">
                    <div class="bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <h5 class="mb-0">Progress Belajar</h5>
                        </div>
                        <div class="card-body p-6">
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
    </div>

    {{-- PERBAIKAN: Menggunakan $isLecturer dari controller --}}
    @if($isLecturer)
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
</x-app-layout>