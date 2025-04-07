<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        return Subject::with(['teacher', 'students'])->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'teacher_id' => 'required|exists:teachers,id'
        ]);

        $subject = Subject::create($request->only('title', 'teacher_id'));

        return response()->json($subject, 201);
    }

    public function show($id)
    {
        $subject = Subject::with(['teacher', 'students'])->findOrFail($id);
        return response()->json($subject);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'sometimes|string',
            'teacher_id' => 'sometimes|exists:teachers,id'
        ]);

        $subject = Subject::findOrFail($id);
        $subject->update($request->only('title', 'teacher_id'));

        return response()->json($subject);
    }

    public function destroy($id)
    {
        $subject = Subject::findOrFail($id);
        $subject->students()->detach();
        $subject->delete();

        return response()->json(['message' => 'Subject deleted successfully']);
    }
}
