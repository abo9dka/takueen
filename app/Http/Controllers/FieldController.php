<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\RoadmapProgress;
use App\Models\User;
use App\Models\UserFieldLevel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FieldController extends Controller
{
    // get
    public function index()
    {
        $field = Field::with('supervisors')->get();

        return response()->json($field, 200);
    }
    // add field
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'supervisors' => 'nullable|array',
            'supervisors.*' => [
                'integer',
                Rule::exists('users', 'id')
                    ->where('role', 'supervisor'),
            ],
        ]);

        $field = Field::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        $field->supervisors()->attach($request->supervisors);

        return response()->json([
            'message' => 'Field created successfully',
            'field' => $field->load('supervisors')
        ], 201);
    }
    // update
    public function update(Request $request, $id)
    {
        $field = Field::findOrFail($id);

        $field->update([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $request->image,
        ]);

        if ($request->has('supervisors')) {
            $field->supervisors()->sync($request->supervisors);
        }

        return response()->json([
            'message' => 'updated successfully'
        ]);
    }
    // delete
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

    // get all field questions by id
    public function getFieldQuestions($id)
    {
        $field = Field::with('questions')->findOrFail($id);

        return response()->json($field, 200);
    }
    public function assignFields(Request $request, $id)
    {
        $request->validate([
            'fields' => 'required|array',
            'fields.*' => 'exists:fields,id',
        ]);

        $user = User::where('role', 'supervisor')->findOrFail($id);

        $user->fields()->sync($request->fields);

        return response()->json([
            'message' => 'Fields assigned successfully',
            'user' => $user->load('fields')
        ]);
    }

    public function detailsField($id)
    {
        $field = Field::with([
            'questions',
            'roadmaps.stages',
            'roadmaps.projects',
            'roadmaps.supervisor'
        ])->find($id);

        if (!$field) {
            return response()->json([
                'message' => 'Field not found'
            ], 404);
        }

        $questionsCount = $field->questions->count();

        $beginner = $field->questions->where('difficulty', 'beginner')->count();
        $intermediate = $field->questions->where('difficulty', 'intermediate')->count();
        $advanced = $field->questions->where('difficulty', 'advanced')->count();

        $totalStudents = UserFieldLevel::where('field_id', $field->id)
            ->distinct('user_id')
            ->count('user_id');

        $roadmapIds = $field->roadmaps->pluck('id');

        $completedStudents = RoadmapProgress::where('status', 'completed')
            ->whereIn('roadmap_id', $roadmapIds)
            ->select('user_id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(DISTINCT roadmap_id) = ?', [$roadmapIds->count()])
            ->count();

        $percentage = $totalStudents > 0
            ? round(($completedStudents / $totalStudents) * 100, 2)
            : 0;

        $supervisors = $field->roadmaps
            ->pluck('supervisor')
            ->filter()
            ->unique('id')
            ->values();

        return response()->json([
            'id' => $field->id,
            'name' => $field->name,
            'description' => $field->description,
            'icon' => $field->icon,

            'questions_count' => $questionsCount,
            'beginner_questions' => $beginner,
            'intermediate_questions' => $intermediate,
            'advanced_questions' => $advanced,

            'roadmaps' => $field->roadmaps,

            'students_count' => $totalStudents,
            'completed_students_count' => $completedStudents,
            'completion_percentage' => $percentage . '%',

            'supervisors' => $supervisors,
        ]);
    }
}
