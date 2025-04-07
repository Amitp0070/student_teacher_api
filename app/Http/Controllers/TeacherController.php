<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = Teacher::with('subjects')->get();

        $transformed = $teachers->map(function ($teacher) {
            return [
                'teacher_id' => $teacher->id,
                'teacher_name' => $teacher->name,
                'subjects' => $teacher->subjects->map(function ($subject) {
                    return [
                        'subject_id' => $subject->id,
                        'subject_title' => $subject->title,
                    ];
                }),
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Teachers fetched successfully',
            'data' => $transformed
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subjects' => 'nullable|array',
            'subjects.*' => 'required|string|max:255'
        ]);

        // Create Teacher
        $teacher = Teacher::create([
            'name' => $request->name,
        ]);

        // Create subjects and attach with teacher
        if (!empty($request->subjects)) {
            foreach ($request->subjects as $title) {
                $teacher->subjects()->create([
                    'title' => $title
                ]);
            }
        }

        // Reload with subjects
        $teacher->load('subjects');

        // Transform response
        $data = [
            'teacher_id' => $teacher->id,
            'teacher_name' => $teacher->name,
            'subjects' => $teacher->subjects->map(function ($subject) {
                return [
                    'subject_id' => $subject->id,
                    'subject_title' => $subject->title,
                ];
            }),
        ];

        return response()->json([
            'status' => true,
            'message' => 'Teacher with subjects created successfully',
            'data' => $data
        ], 201);
    }


    public function show($id)
    {
        $teacher = Teacher::with('subjects')->findOrFail($id);

        $data = [
            'teacher_id' => $teacher->id,
            'teacher_name' => $teacher->name,
            'subjects' => $teacher->subjects->map(function ($subject) {
                return [
                    'subject_id' => $subject->id,
                    'subject_title' => $subject->title,
                ];
            }),
        ];

        return response()->json([
            'status' => true,
            'message' => 'Teacher details fetched successfully',
            'data' => $data
        ]);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'subjects' => 'nullable|array',
            'subjects.*' => 'required|string|max:255'
        ]);

        $teacher = Teacher::findOrFail($id);

        // Update teacher name
        if ($request->has('name')) {
            $teacher->update(['name' => $request->name]);
        }

        // If subjects are passed, delete old and add new
        if ($request->has('subjects')) {
            // Delete old subjects
            $teacher->subjects()->delete();

            // Create new subjects
            foreach ($request->subjects as $title) {
                $teacher->subjects()->create([
                    'title' => $title
                ]);
            }
        }

        // Reload with subjects
        $teacher->load('subjects');

        // Transform response
        $data = [
            'teacher_id' => $teacher->id,
            'teacher_name' => $teacher->name,
            'subjects' => $teacher->subjects->map(function ($subject) {
                return [
                    'subject_id' => $subject->id,
                    'subject_title' => $subject->title,
                ];
            }),
        ];

        return response()->json([
            'status' => true,
            'message' => 'Teacher updated successfully',
            'data' => $data
        ]);
    }


    public function destroy($id)
    {
        $teacher = Teacher::with('subjects')->findOrFail($id);

        // Optional: Delete related subjects if needed (if cascading is not enabled)
        // $teacher->subjects()->delete();

        $teacher->delete();

        return response()->json([
            'status' => true,
            'message' => 'Teacher deleted successfully',
            'data' => [
                'teacher_id' => $teacher->id,
                'teacher_name' => $teacher->name,
            ]
        ]);
    }
}
