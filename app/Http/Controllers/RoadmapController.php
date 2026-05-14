<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Roadmap;

class RoadmapController extends Controller
{
    public function index()
    {
        return response()->json(
            Roadmap::with('field', 'stages')->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'field_id' => 'required|exists:fields,id',
            'ai_generated' => 'boolean',
        ]);

        $roadmap = Roadmap::create($request->all());
        $roadmap->load('field', 'stages');
        return response()->json($roadmap, 201);
    }

    public function show($id)
    {
        $roadmap = Roadmap::with('field', 'stages')->findOrFail($id);
        return response()->json($roadmap);
    }

    public function update(Request $request, $id)
    {
        $roadmap = Roadmap::where('id', $id)->firstOrFail();
        $roadmap->update($request->all());
        $roadmap->load('field', 'stages');
        return response()->json($roadmap);
    }

    public function destroy($id)
    {
        $roadmap = Roadmap::where('id', $id)->firstOrFail();
        $roadmap->delete();
        return response()->json(['message' => 'Deleted']);
    }
}