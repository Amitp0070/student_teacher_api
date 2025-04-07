<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index()
    {

        $data = DB::table('students')
            ->join('student_subject', 'students.id', '=', 'student_subject.student_id')
            ->join('subjects', 'student_subject.subject_id', '=', 'subjects.id')
            ->join('teachers', 'subjects.teacher_id', '=', 'teachers.id')
            ->select(
                'students.id as student_id',
                'students.name as student_name',
                'subjects.id as subject_id',
                'subjects.title as subject_name',
                'teachers.id as teacher_id',
                'teachers.name as teacher_name'
            )
            ->get();

        // Group data by student
        $grouped = $data->groupBy('student_id')->map(function ($items) {
            $studentName = $items->first()->student_name;

            $subjects = $items->map(function ($item) {
                return [
                    'subject_id' => $item->subject_id,
                    'subject_name' => $item->subject_name,
                    'teacher_id' => $item->teacher_id,
                    'teacher_name' => $item->teacher_name,
                ];
            });

            return [
                'student_id' => $items->first()->student_id,
                'student_name' => $studentName,
                'subjects' => $subjects,
            ];
        })->values();

        return response()->json([
            'status' => true,
            'message' => 'Data fetched successfully',
            'data' => $grouped
        ]);



        // $students = Student::with('subjects.teacher')->get();

        // $transformed = $students->map(function ($student) {
        //     return [
        //         'student_name' => $student->name,
        //         'subjects' => $student->subjects->map(function ($subject) {
        //             return [
        //                 'subject_name' => $subject->title,
        //                 'teacher_name' => $subject->teacher->name ?? 'N/A',
        //             ];
        //         }),
        //     ];
        // });

        // return response()->json([
        //     'status' => true,
        //     'message' => 'Students fetched successfully',
        //     'data' => $transformed
        // ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subject_ids' => 'required|array',
            'subject_ids.*' => 'exists:subjects,id',
        ]);

        $student = Student::create([
            'name' => $request->name,
        ]);

        // Attach multiple subjects to the student
        $student->subjects()->attach($request->subject_ids);

        return response()->json([
            'status' => true,
            'message' => 'Student created successfully with subjects',
            'data' => [
                'student' => $student->load('subjects.teacher') // if you want to return full subject + teacher data
            ]
        ], 201);
    }


    public function show($id)
    {
        $student = Student::with('subjects.teacher')->findOrFail($id);

        // Transform the data into nested format
        $transformed = [
            'student_id' => $student->id,
            'student_name' => $student->name,
            'subjects' => $student->subjects->map(function ($subject) {
                return [
                    'subject_id' => $subject->id,
                    'subject_name' => $subject->title,
                    'teacher_id' => $subject->teacher->id ?? null,
                    'teacher_name' => $subject->teacher->name ?? 'N/A',
                ];
            })
        ];

        return response()->json([
            'status' => true,
            'message' => 'Student details fetched successfully',
            'data' => $transformed
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'string|max:255',
            'subject_ids' => 'nullable|array',
            'subject_ids.*' => 'exists:subjects,id',
        ]);

        $student = Student::findOrFail($id);

        // Update student name if provided
        if ($request->filled('name')) {
            $student->update(['name' => $request->name]);
        }

        // Sync subjects if provided
        if ($request->has('subject_ids')) {
            $student->subjects()->sync($request->subject_ids);
        }

        // Reload relationships
        $student->load('subjects.teacher');

        // Transform to match desired structure
        $transformed = [
            'student_id' => $student->id,
            'student_name' => $student->name,
            'subjects' => $student->subjects->map(function ($subject) {
                return [
                    'subject_id' => $subject->id,
                    'subject_name' => $subject->title,
                    'teacher_id' => $subject->teacher->id ?? null,
                    'teacher_name' => $subject->teacher->name ?? 'N/A',
                ];
            })
        ];

        return response()->json([
            'status' => true,
            'message' => 'Student updated successfully',
            'data' => $transformed
        ]);
    }

    public function destroy($id)
    {
        // Student ko subjects ke sath load karna (delete hone se pehle data capture karna)
        $student = Student::with('subjects')->findOrFail($id);

        // Delete se pehle student ka naam aur subjects ka data save kar lete hain
        $studentData = [
            'student_name' => $student->name,
            'subjects' => $student->subjects->map(function ($subject) {
                return [
                    'subject_id' => $subject->id,
                    'subject_name' => $subject->title,
                ];
            }),
        ];

        // Pehle relation delete karna (pivot table se)
        $student->subjects()->detach();

        // Ab actual student delete karna
        $student->delete();

        // Response me deleted student ka data bhi bhejna
        return response()->json([
            'status' => true,
            'message' => 'Student deleted successfully',
            'deleted_data' => $studentData
        ]);
    }
}
