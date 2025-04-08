<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::with(['teacher', 'students'])->get();

        $transformed = $subjects->map(function ($subject) {
            return [
                'subject_id' => $subject->id,
                'subject_title' => $subject->title,
                'teacher' => [
                    'teacher_id' => $subject->teacher->id ?? null,
                    'teacher_name' => $subject->teacher->name ?? 'N/A',
                ],
                'students' => $subject->students->map(function ($student) {
                    return [
                        'student_id' => $student->id,
                        'student_name' => $student->name,
                    ];
                }),
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Subjects fetched successfully',
            'data' => $transformed
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'teacher_id' => 'required|exists:teachers,id'
        ]);

        $subject = Subject::create($request->only('title', 'teacher_id'));

        // Load the teacher relationship to include in response
        $subject->load('teacher');

        return response()->json([
            'status' => true,
            'message' => 'Subject created successfully',
            'data' => [
                'subject_id' => $subject->id,
                'subject_title' => $subject->title,
                'teacher_id' => $subject->teacher->id,
                'teacher_name' => $subject->teacher->name
            ]
        ], 201);
    }

    public function show($id)
    {
        $subject = Subject::with(['teacher', 'students'])->findOrFail($id);

        return response()->json([
            'status' => true,
            'message' => 'Subject details fetched successfully',
            'data' => [
                'subject_id' => $subject->id,
                'subject_title' => $subject->title,
                'teacher' => [
                    'teacher_id' => $subject->teacher->id,
                    'teacher_name' => $subject->teacher->name,
                ],
                'students' => $subject->students->map(function ($student) {
                    return [
                        'student_id' => $student->id,
                        'student_name' => $student->name,
                    ];
                }),
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'sometimes|string',
            'teacher_id' => 'sometimes|exists:teachers,id'
        ]);

        $subject = Subject::findOrFail($id);
        $subject->update($request->only('title', 'teacher_id'));

        $subject->load('teacher');

        return response()->json([
            'status' => true,
            'message' => 'Subject updated successfully',
            'data' => [
                'subject_id' => $subject->id,
                'subject_title' => $subject->title,
                'teacher' => [
                    'teacher_id' => $subject->teacher->id ?? null,
                    'teacher_name' => $subject->teacher->name ?? 'N/A',
                ]
            ]
        ]);
    }


    public function destroy($id)
    {
        $subject = Subject::with('students')->findOrFail($id); // Optional: students if you want to log/delete associations

        // Store info before delete
        $subjectData = [
            'id' => $subject->id,
            'title' => $subject->title,
        ];

        // Detach relationships and delete subject
        $subject->students()->detach();
        $subject->delete();

        return response()->json([
            'status' => true,
            'message' => 'Subject deleted successfully',
            'data' => $subjectData
        ]);
    }
}
