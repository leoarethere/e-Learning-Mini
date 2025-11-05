<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Pendaftaran Mahasiswa untuk: ') . $course->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.courses.enrollments.update', $course) }}" method="POST">
                        @csrf
                        
                        <h3 class="text-lg font-medium mb-4">Pilih Mahasiswa yang Terdaftar:</h3>

                        <div class="space-y-2">
                            @forelse ($allStudents as $student)
                                <label class="flex items-center">
                                    <input type="checkbox" name="student_ids[]" value="{{ $student->id }}"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                        @checked(in_array($student->id, $enrolledStudentIds))
                                    >
                                    <span class="ms-2 text-sm text-gray-600">{{ $student->name }} ({{ $student->email }})</span>
                                </label>
                            @empty
                                <p class="text-gray-500">Belum ada mahasiswa terdaftar di sistem.</p>
                            @endforelse
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.courses.index') }}" class="text-gray-600 hover:text-gray-900 me-4">
                                {{ __('Batal') }}
                            </a>

                            <x-primary-button>
                                {{ __('Update Pendaftaran') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>