<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Course;
use App\Models\Material;
use App\Models\Discussion;
use App\Models\Progress;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@elearning.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Dosen
        $dosen1 = User::create([
            'name' => 'Dr. Ahmad Wijaya, M.Kom',
            'email' => 'ahmad@elearning.com',
            'password' => bcrypt('password'),
            'role' => 'dosen',
        ]);

        $dosen2 = User::create([
            'name' => 'Dr. Siti Rahayu, M.T',
            'email' => 'siti@elearning.com',
            'password' => bcrypt('password'),
            'role' => 'dosen',
        ]);

        // Mahasiswa
        $mahasiswa1 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@elearning.com',
            'password' => bcrypt('password'),
            'role' => 'mahasiswa',
        ]);

        $mahasiswa2 = User::create([
            'name' => 'Ani Lestari',
            'email' => 'ani@elearning.com',
            'password' => bcrypt('password'),
            'role' => 'mahasiswa',
        ]);

        // Courses
        $course1 = Course::create([
            'name' => 'Pemrograman Web Lanjut',
            'description' => 'Mata kuliah tentang pemrograman web modern dengan framework Laravel',
            'code' => 'PW001',
            'lecturer_id' => $dosen1->id,
        ]);

        $course2 = Course::create([
            'name' => 'Basis Data',
            'description' => 'Mata kuliah tentang desain dan implementasi basis data',
            'code' => 'BD001',
            'lecturer_id' => $dosen2->id,
        ]);

        // Materials untuk Course 1
        $materials1 = [
            [
                'title' => 'Pengenalan Laravel',
                'content' => 'Laravel adalah framework PHP yang elegant...',
                'course_id' => $course1->id,
            ],
            [
                'title' => 'Routing dan Controller',
                'content' => 'Dalam Laravel, routing digunakan untuk...',
                'course_id' => $course1->id,
            ],
            [
                'title' => 'Database Migration',
                'content' => 'Migration digunakan untuk version control database...',
                'course_id' => $course1->id,
            ]
        ];

        foreach ($materials1 as $material) {
            Material::create($material);
        }

        // Materials untuk Course 2
        $materials2 = [
            [
                'title' => 'Konsep Basis Data',
                'content' => 'Basis data adalah kumpulan data yang terorganisir...',
                'course_id' => $course2->id,
            ],
            [
                'title' => 'Normalisasi Database',
                'content' => 'Normalisasi adalah proses pengorganisasian data...',
                'course_id' => $course2->id,
            ]
        ];

        foreach ($materials2 as $material) {
            Material::create($material);
        }

        // Discussions
        Discussion::create([
            'title' => 'Cara install Laravel?',
            'content' => 'Mohon bantuan untuk cara install Laravel yang benar...',
            'course_id' => $course1->id,
            'user_id' => $mahasiswa1->id,
        ]);

        Discussion::create([
            'title' => 'Perbedaan MySQL dan PostgreSQL',
            'content' => 'Apa perbedaan utama antara MySQL dan PostgreSQL?',
            'course_id' => $course2->id,
            'user_id' => $mahasiswa2->id,
        ]);

        // Progress tracking
        $materials = Material::all();
        foreach ($materials as $material) {
            if (rand(0, 1)) {
                Progress::create([
                    'user_id' => $mahasiswa1->id,
                    'material_id' => $material->id,
                    'completed' => true,
                    'completed_at' => now(),
                ]);
            }
        }
    }
}