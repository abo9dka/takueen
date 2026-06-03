<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Roadmap;
use App\Models\RoadmapStage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RoadmapController extends Controller
{
    public function index()
    {
        return response()->json(
            Roadmap::with('field', 'stages', 'supervisor')->get()
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

        $roadmap = Roadmap::create([
            'title' => $request->title,
            'description' => $request->description,
            'field_id' => $request->field_id,
            'supervisor_id' => $request->user()->id,
            'ai_generated' => $request->ai_generated ?? false,
        ]);

        return response()->json(
            $roadmap->load('field', 'stages', 'supervisor'),
            201
        );
    }

    public function show($id)
    {
        $roadmap = Roadmap::with('field', 'stages', 'supervisor')
            ->findOrFail($id);

        return response()->json($roadmap);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'field_id' => 'sometimes|exists:fields,id',
        ]);

        $roadmap = Roadmap::findOrFail($id);

        $roadmap->update([
            'title' => $request->title ?? $roadmap->title,
            'description' => $request->description ?? $roadmap->description,
            'field_id' => $request->field_id ?? $roadmap->field_id,
        ]);

        return response()->json(
            $roadmap->load('field', 'stages', 'supervisor')
        );
    }

    public function destroy($id)
    {
        $roadmap = Roadmap::findOrFail($id);
        $roadmap->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function aiGenerate(Request $request)
    {
        $request->validate([
            'field_id' => 'required|exists:fields,id',
            'level' => 'required|in:beginner,intermediate,advanced'
        ]);

        $field = Field::findOrFail($request->field_id);

        $prompt = "
Generate a learning roadmap for {$field->name}.
Level: {$request->level}

Return ONLY JSON:
{
  \"title\": \"...\",
  \"description\": \"...\",
  \"stages\": [
    {
      \"stage_order\": 1,
      \"stage_description\": \"...\",
      \"requirements\": {
        \"topics\": [],
        \"resources\": [],
        \"prerequisites\": []
      }
    }
  ]
}
";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
            'Content-Type' => 'application/json'
        ])->post('https://api.groq.com/openai/v1/chat/completions', [
            'model' => 'llama-3.1-8b-instant',
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.2
        ]);

        $content = $response->json()['choices'][0]['message']['content'] ?? null;

        if (!$content) {
            return response()->json(['message' => 'Empty AI response'], 500);
        }

        $content = trim(preg_replace('/```json|```/', '', $content));

        preg_match('/\{[\s\S]*\}/', $content, $matches);
        $data = json_decode($matches[0] ?? null, true);

        if (!$data || !isset($data['stages'])) {
            return response()->json([
                'message' => 'Invalid AI response',
                'raw' => $content
            ], 500);
        }

        $roadmap = Roadmap::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'field_id' => $request->field_id,
            'supervisor_id' => $request->user()->id,
            'ai_generated' => true,
        ]);

        foreach ($data['stages'] as $stage) {
            RoadmapStage::create([
                'roadmap_id' => $roadmap->id,
                'stage_order' => $stage['stage_order'] ?? 0,
                'stage_description' => $stage['stage_description'] ?? '',
                'requirements' => json_encode($stage['requirements'] ?? [])
            ]);
        }

        return response()->json([
            'message' => 'Roadmap generated successfully',
            'roadmap' => $roadmap->load('field', 'stages', 'supervisor')
        ], 201);
    }
}
