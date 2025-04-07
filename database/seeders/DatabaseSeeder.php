<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Insert Dummy Teachers
        Teacher::insert([
            [
                'name' => 'Mr. Sharma',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ms. Verma',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // Insert Dummy Students
        Student::insert([
            [
                'name' => 'Rahul Singh',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Priya Mehta',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Aman Yadav',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // Insert Dummy Subjects with teacher mapping
        Subject::insert([
            [
                'title' => 'Mathematics',
                'teacher_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Science',
                'teacher_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'English',
                'teacher_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // Insert Dummy student-subject mapping
        DB::table('student_subject')->insert([
            [
                'student_id' => 1,
                'subject_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => 1,
                'subject_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => 2,
                'subject_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => 2,
                'subject_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => 3,
                'subject_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
