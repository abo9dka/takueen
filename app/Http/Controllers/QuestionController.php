<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index()
    {
        $question = Question::with('field')->get();
        return response()->json($question, 200);
    }

    public function show($id)
    {
        $question = Question::findOrFail($id);
        return response()->json($question, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'field_id' => 'required|exists:fields,id',
            'title' => 'required',
            'description' => 'required',
            'difficulty' => 'required'
        ]);

        $question = Question::create([
            'field_id' => $request->field_id,
            'title' => $request->title,
            'description' => $request->description,
            'difficulty' => $request->difficulty
        ]);

        return response()->json($question, 201);
    }

    public function update(Request $request, $id)
    {
        $question = Question::findOrFail($id);

        $question->update([
            'field_id' => $request->field_id,
            'title' => $request->title,
            'description' => $request->description,
            'difficulty' => $request->difficulty,
        ]);

        return response()->json($question, 200);
    }

    public function destroy($id)
    {
        $question = Question::findOrFail($id);
        $question->delete();

        return response()->json([
            'message' => 'Question deleted'
        ]);
    }
}