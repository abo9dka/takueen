<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RoadmapStage;

class RoadmapStageController extends Controller
{
    public function show($id)
    {
        $stage = RoadmapStage::with('roadmap.field')->findOrFail($id);
        return response()->json($stage);
    }
    public function store(Request $request)
    {
        $request->validate([
            'stage_description' => 'required|string',
            'stage_order' => 'required|integer',
            'requirements' => 'nullable|string',
            'roadmap_id' => 'required|exists:roadmaps,id',
        ]);

        $stage = RoadmapStage::create($request->all());
        $stage->load('roadmap.field');
        return response()->json($stage, 201);
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'stage_description' => 'required|string',
            'stage_order' => 'required|integer',
            'requirements' => 'nullable|string',
            'roadmap_id' => 'required|exists:roadmaps,id',
        ]);

        $stage = RoadmapStage::where('id', $id)->firstOrFail();
        $stage->update($request->all());
        $stage->load('roadmap.field');
        return response()->json($stage);
    }
    public function destroy($id)
    {
        $stage = RoadmapStage::where('id', $id)->firstOrFail();
        $stage->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
