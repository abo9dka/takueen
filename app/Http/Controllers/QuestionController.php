<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Question;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
            'difficulty' => 'required',
            'is_placement' => 'required|boolean'
        ]);

        $question = Question::create([
            'field_id' => $request->field_id,
            'title' => $request->title,
            'description' => $request->description,
            'difficulty' => $request->difficulty,
            'is_placement' => $request->is_placement
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
            'is_placement' => $request->is_placement
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

    public function aiGenerate(Request $request)
    {
        $request->validate([
            'field_id' => 'required|exists:fields,id',
            'difficulty' => 'required|in:beginner,intermediate,advanced',
            'count' => 'required|integer|min:1|max:10'
        ]);

        $field = Field::findOrFail($request->field_id);

        $prompt = "
You are a strict JSON generator.

Generate {$request->count} coding questions for {$field->name}.

Difficulty: {$request->difficulty}

RULES:
- Return ONLY valid JSON array
- NO explanations
- NO markdown
- NO code blocks
- NO extra text

FORMAT:
[
  {
    \"title\": \"...\",
    \"description\": \"...\"
  }
]
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

        // تنظيف الرد من أي markdown
        $content = trim($content);

        $content = preg_replace('/```json|```/', '', $content);

        // استخراج JSON array فقط
        preg_match('/\[[\s\S]*\]/', $content, $matches);

        $json = $matches[0] ?? null;

        $questions = json_decode($json, true);

        if (!is_array($questions)) {
            return response()->json([
                'message' => 'AI response invalid',
                'raw' => $content
            ], 500);
        }

        $created = [];

        foreach ($questions as $q) {
            $created[] = Question::create([
                'field_id' => $request->field_id,
                'title' => $q['title'] ?? 'No title',
                'description' => $q['description'] ?? 'No description',
                'difficulty' => $request->difficulty,
                'is_placement' => false
            ]);
        }

        return response()->json([
            'message' => 'Questions generated successfully',
            'count' => count($created),
            'questions' => $created
        ]);
    }
    public function progress($fieldId)
    {
        $user = auth()->user();

        $totalQuestions = Question::where('field_id', $fieldId)->count();

        $solvedQuestions = Submission::where('user_id', $user->id)
            ->where('status', 'accepted')
            ->whereHas('question', function ($q) use ($fieldId) {
                $q->where('field_id', $fieldId);
            })
            ->distinct('question_id')
            ->count('question_id');

        return response()->json([
            'solved' => $solvedQuestions,
            'total' => $totalQuestions,
            'progress' => $totalQuestions > 0
                ? round(($solvedQuestions / $totalQuestions) * 100)
                : 0
        ]);
    }
}
