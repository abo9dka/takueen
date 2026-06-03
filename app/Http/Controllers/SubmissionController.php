<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\Contest;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubmissionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'code' => 'required'
        ]);

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        $submission = Submission::create([
            'user_id' => $user->id,
            'question_id' => $request->question_id,
            'code' => $request->code,
            'status' => 'pending',
            'feedback' => null
        ]);

        return response()->json($submission, 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'required'
        ]);

        $submission = Submission::findOrFail($id);

        if ($submission->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $submission->update([
            'code' => $request->code,
            'status' => 'pending'
        ]);

        return response()->json($submission);
    }

    public function evaluate($id)
    {
        $submission = Submission::with('question', 'user')->findOrFail($id);

        $user = request()->user();

        if (!$user || $submission->user_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        if ($submission->status === 'accepted') {
            return response()->json([
                'message' => 'Already accepted'
            ], 400);
        }

        if ($submission->status === 'processing') {
            return response()->json([
                'message' => 'Submission is already being evaluated'
            ], 409);
        }

        $submission->update([
            'status' => 'processing'
        ]);

        $prompt = "
Return ONLY valid JSON (no markdown, no text):

{
  \"status\": \"accepted\" or \"wrong\",
  \"feedback\": \"short explanation\",
  \"correct_solution\": \"full correct code if wrong, otherwise empty string\"
}

### Problem:
{$submission->question->description}

### Student Code:
{$submission->code}
";

        $response = Http::timeout(30)->withHeaders([
            'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
            'Content-Type' => 'application/json'
        ])->post('https://api.groq.com/openai/v1/chat/completions', [
            "model" => "llama-3.1-8b-instant",
            "messages" => [
                [
                    "role" => "user",
                    "content" => $prompt
                ]
            ]
        ]);

        if (!$response->successful()) {
            $submission->update(['status' => 'pending']);

            return response()->json([
                'message' => 'AI service failed'
            ], 503);
        }

        $json = $response->json();

        $content = $json['choices'][0]['message']['content'] ?? null;

        if (!$content) {
            $submission->update(['status' => 'pending']);

            return response()->json([
                'message' => 'Empty AI response'
            ], 500);
        }

        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $submission->update(['status' => 'pending']);

            return response()->json([
                'message' => 'Invalid AI response format',
                'raw' => $content
            ], 500);
        }

        $allowed = ['accepted', 'wrong'];

        if (!in_array($data['status'] ?? null, $allowed)) {
            $submission->update(['status' => 'pending']);

            return response()->json([
                'message' => 'Invalid AI status value'
            ], 422);
        }

        $newStatus = $data['status'];
        $feedback = $data['feedback'] ?? 'No feedback';
        $correctSolution = $data['correct_solution'] ?? null;

        $oldStatus = $submission->status;

        $submission->update([
            'status' => $newStatus,
            'feedback' => $feedback,
        ]);

        $contest = Competition::where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        if ($newStatus === 'accepted' && $contest) {
            $pivot = DB::table('contest_question')
                ->where('contest_id', $contest->id)
                ->where('question_id', $submission->question_id)
                ->first();
        }

        if ($newStatus === 'accepted' && $oldStatus !== 'accepted') {
            $submission->user->increment('points', 10);
        }

        return response()->json([
            'status' => $newStatus,
            'feedback' => $feedback,
            'correct_solution' => $correctSolution,
        ]);
    }
}
