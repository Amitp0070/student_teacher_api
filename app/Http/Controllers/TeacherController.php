<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index()
    {
        return Teacher::with('subjects')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string'
        ]);

        $teacher = Teacher::create($request->only('name'));

        return response()->json($teacher, 201);
    }

    public function show($id)
    {
        $teacher = Teacher::with('subjects')->findOrFail($id);
        return response()->json($teacher);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|string'
        ]);

        $teacher = Teacher::findOrFail($id);
        $teacher->update($request->only('name'));

        return response()->json($teacher);
    }

    public function destroy($id)
    {
        $teacher = Teacher::findOrFail($id);
        $teacher->delete();

        return response()->json(['message' => 'Teacher deleted successfully']);
    }
}
