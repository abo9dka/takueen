<?php

namespace App\Http\Controllers;

use App\Models\Field;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FieldController extends Controller
{
    //get
    public function index()
    {
        $field = Field::all();
        return response()->json($field, 200);
    }
    //add fieled
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'supervisors' => 'required|array',
            'supervisors.*' => [
                'required',
                Rule::exists('users', 'id')->where('role', 'supervisor'),
            ],
        ]);

        $field = Field::create([
            'name' => $request->name,
            'description' => $request->description
        ]);

        $field->supervisors()->attach($request->supervisors);

        return response()->json([
            'message' => 'Field created successfully',
            'field' => $field->load('supervisors')
        ], 201);
    }
    //update
    public function update(Request $request, $id)
    {
        $field = Field::find($id);

        if (!$field) {
            return response()->json([
                'message' => 'Field not found'
            ], 404);
        }

        $field->update([
            'name' => $request->name,
            'description' => $request->description,
            "image" => $request->image
        ]);

        return response()->json($field);
    }
    //delete
    public function destroy($id)
    {
        $field = Field::find($id);

        if (!$field) {
            return response()->json([
                'message' => 'Field not found'
            ], 404);
        }

        $field->delete();

        return response()->json([
            'message' => 'Field deleted'
        ]);
    }
    //get all field questions by id
    public function getFieldQuestions($id)
    {
        $field = Field::with('questions')->findOrFail($id);
        return response()->json($field, 200);
    }
}
