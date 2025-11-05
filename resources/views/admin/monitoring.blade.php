@extends('layouts.app')

@section('title', 'Monitoring Aktivitas')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Monitoring Aktivitas Mahasiswa</h1>
    
    <div class="card mb-4">
        <div class="card-header">
            <h5>Progress Belajar Mahasiswa</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama Mahasiswa</th>
                            <th>Email</th>
                            <th>Materi Diselesaikan</th>
                            <th>Total Materi</th>
                            <th>Progress</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                        <tr>
                            <td>{{ $student->name }}</td>
                            <td>{{ $student->email }}</td>
                            <td>{{ $student->completed_materials }}</td>
                            <td>{{ $materialsCount }}</td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ $student->completion_rate }}%">
                                    </div>
                                </div>
                            </td>
                            <td>{{ round($student->completion_rate, 1) }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection