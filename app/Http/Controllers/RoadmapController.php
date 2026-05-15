<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\RoadmapStage;
use Illuminate\Http\Request;
use App\Models\Roadmap;
use Illuminate\Support\Facades\Http;

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

        return response()->json([
            'message' => 'Deleted'
        ]);
    }

    public function aiGenerate(Request $request)
    {
        $request->validate([
            'field_id' => 'required|exists:fields,id',
            'level' => 'required|in:beginner,intermediate,advanced'
        ]);

        $field = Field::findOrFail($request->field_id);

        $prompt = "
You are a strict JSON generator.

Generate a learning roadmap for {$field->name}.

Level: {$request->level}

RULES:
- Return ONLY valid JSON
- NO markdown
- NO code blocks
- NO explanations
- NO extra text

FORMAT:
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
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.2
        ]);

        $content = $response->json()['choices'][0]['message']['content'] ?? null;

        if (!$content) {
            return response()->json([
                'message' => 'Empty AI response'
            ], 500);
        }

        $content = trim($content);

        $content = preg_replace('/```json|```/', '', $content);

        // استخراج JSON object
        preg_match('/\{[\s\S]*\}/', $content, $matches);

        $json = $matches[0] ?? null;

        $data = json_decode($json, true);

        if (!$data || !isset($data['stages'])) {
            return response()->json([
                'message' => 'Invalid AI response',
                'raw' => $content
            ], 500);
        }

        // create roadmap
        $roadmap = Roadmap::create([
            'title' => $data['title'] ?? 'No title',
            'description' => $data['description'] ?? 'No description',
            'field_id' => $request->field_id,
            'ai_generated' => true
        ]);

        $createdStages = [];

        // create stages
        foreach ($data['stages'] as $stage) {
            $createdStages[] = RoadmapStage::create([
                'roadmap_id' => $roadmap->id,
                'stage_order' => $stage['stage_order'] ?? 0,
                'stage_description' => $stage['stage_description'] ?? '',
                'requirements' => json_encode($stage['requirements'] ?? [])
            ]);
        }

        $roadmap->load('field', 'stages');

        return response()->json([
            'message' => 'Roadmap generated successfully',
            'roadmap' => $roadmap,
            'stages_count' => count($createdStages)
        ], 201);
    }
}
