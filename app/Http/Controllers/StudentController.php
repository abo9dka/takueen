<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Submission;
use App\Models\User;
use App\Models\UserFieldLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function calculateLevel($userId, $fieldId)
    {
        $total = Question::where('field_id', $fieldId)
            ->where('is_placement', true)
            ->count();

        if ($total == 0) {
            return 'beginner';
        }

        $accepted = Submission::where('user_id', $userId)
            ->where('status', 'accepted')
            ->whereHas('question', function ($q) use ($fieldId) {
                $q->where('field_id', $fieldId)
                    ->where('is_placement', true);
            })
            ->distinct('question_id')
            ->count('question_id');

        $percentage = ($accepted / $total) * 100;

        if ($percentage < 40) {
            return 'beginner';
        }

        if ($percentage < 70) {
            return 'intermediate';
        }

        return 'advanced';
    }

    public function placementTest(Request $request, $fieldId)
    {
        $user = $request->user();

        $fieldLevel = UserFieldLevel::where('user_id', $user->id)
            ->where('field_id', $fieldId)
            ->first();

        // إذا ما عنده مستوى -> اختبار
        if (!$fieldLevel) {
            $questions = Question::where('field_id', $fieldId)
                ->inRandomOrder()
                ->limit(10)
                ->get();

            return response()->json([
                'type' => 'placement',
                'questions' => $questions
            ]);
        }

        // إذا عنده مستوى -> تدريب عادي
        $questions = Question::where('field_id', $fieldId)
            ->where('difficulty', $fieldLevel->level)
            ->where('is_placement', false)
            ->get();

        return response()->json([
            'type' => 'normal',
            'level' => $fieldLevel->level,
            'questions' => $questions
        ]);
    }

    public function finishTest(Request $request, $fieldId)
    {
        $user = $request->user();

        $totalAnswered = Submission::where('user_id', $user->id)
            ->whereHas('question', function ($q) use ($fieldId) {
                $q->where('field_id', $fieldId)
                    ->where('is_placement', true);
            })
            ->distinct('question_id')
            ->count('question_id');

        if ($totalAnswered < 10) {
            return response()->json([
                'message' => 'You must answer all placement questions',
                'answered' => $totalAnswered
            ], 400);
        }

        $level = $this->calculateLevel($user->id, $fieldId);

        UserFieldLevel::updateOrCreate(
            [
                'user_id' => $user->id,
                'field_id' => $fieldId
            ],
            [
                'level' => $level
            ]
        );
        // DB::table('field_user')->updateOrInsert(
        //     [
        //         'user_id' => $user->id,
        //         'field_id' => $fieldId,
        //     ],
        //     [
        //         'last_used_at' => now(),
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ]
        // );
        return response()->json([
            'message' => 'Placement test finished',
            'level' => $level
        ]);
    }
    public function studentDashboard(Request $request)
    {
        $user = $request->user();

        $solvedQuestions = Submission::where('user_id', $user->id)
            ->where('status', 'accepted')
            ->distinct('question_id')
            ->count('question_id');

        $xp = $solvedQuestions * 10;

        $xp = min($xp, 100000);

        $currentLevel = min(600, floor(sqrt($xp / 50)));

        if ($xp < 2000) {
            $systemLevel = 'beginner';
        } elseif ($xp < 5000) {
            $systemLevel = 'intermediate';
        } else {
            $systemLevel = 'advanced';
        }

        $activeTrack = DB::table('field_user')
            ->join('fields', 'fields.id', '=', 'field_user.field_id')
            ->where('field_user.user_id', $user->id)
            ->orderByDesc('field_user.last_used_at')
            ->first();

        $totalQuestions = 0;

        if ($activeTrack) {
            $totalQuestions = Question::where('field_id', $activeTrack->field_id)
                ->count();
        }

        $remainingQuestions = max(0, $totalQuestions - $solvedQuestions);

        $progress = $totalQuestions > 0
            ? round(($remainingQuestions / $totalQuestions) * 100, 2)
            : 100;

        $users = User::where('role', 'trainer')
            ->orderByDesc('points')
            ->select(
                'id',
                'name',
                'role',
                'profile_picture',
                DB::raw('points as xp')
            )
            ->take(10)
            ->get();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'points' => $user->points,
                'streak' => $user->streak,
                'profile_picture' => $user->profile_picture,

                'level' => $currentLevel,
                'level_text' => $systemLevel,
            ],

            'solved_questions' => $solvedQuestions,
            'xp' => $xp,
            'current_level' => $currentLevel,
            'progress_percentage' => $progress . '%',
            'current_track' => $activeTrack ? [
                'id' => $activeTrack->field_id,
                'name' => $activeTrack->name,
                'description' => $activeTrack->description,
                'total_questions' => $totalQuestions,
                'solved_questions' => $solvedQuestions,
                'remaining_questions' => max(0, $totalQuestions - $solvedQuestions),
                'progress_percentage' => $totalQuestions > 0
                    ? round(($solvedQuestions / $totalQuestions) * 100, 2)
                    : 0,
                'level' => $fieldLevel?->level ?? 'beginner',
                'xp' => $solvedQuestions * 10,
            ] : null,

            'leaderboard' => $users
        ]);
    }
    public function myFields(Request $request)
    {
        $user = $request->user();

        $tracks = DB::table('field_user')
            ->join('fields', 'fields.id', '=', 'field_user.field_id')
            ->where('field_user.user_id', $user->id)
            ->select(
                'fields.id',
                'fields.name',
                'fields.description'
            )
            ->get();

        $result = [];

        foreach ($tracks as $track) {

            $totalQuestions = Question::where('field_id', $track->id)
                ->where('is_placement', false)
                ->count();

            $solvedQuestions = Submission::where('user_id', $user->id)
                ->where('status', 'accepted')
                ->whereHas('question', function ($q) use ($track) {
                    $q->where('field_id', $track->id)
                        ->where('is_placement', false);
                })
                ->distinct('question_id')
                ->count('question_id');

            $progress = $totalQuestions > 0
                ? round(($solvedQuestions / $totalQuestions) * 100)
                : 0;

            $fieldLevel = UserFieldLevel::where('user_id', $user->id)
                ->where('field_id', $track->id)
                ->first();

            $result[] = [
                'id' => $track->id,
                'name' => $track->name,
                'description' => $track->description ?? '',
                'level' => $fieldLevel?->level ?? 'beginner',
                'progress_percentage' => $progress . '%',
                'solved_questions' => $solvedQuestions,
                'total_questions' => $totalQuestions,
                'remaining_questions' => max(0, $totalQuestions - $solvedQuestions),
                'xp' => $solvedQuestions * 10,
                'completed_modules' => 0,
                'total_modules' => 0,
            ];
        }

        $currentTrack = DB::table('field_user')
            ->join('fields', 'fields.id', '=', 'field_user.field_id')
            ->where('field_user.user_id', $user->id)
            ->orderByDesc('field_user.last_used_at')
            ->select(
                'fields.id',
                'fields.name',
                'fields.description',
                'field_user.last_used_at'
            )
            ->first();

        $currentTrackData = null;

        if ($currentTrack) {

            $totalQuestions = Question::where('field_id', $currentTrack->id)
                ->where('is_placement', false)
                ->count();

            $solvedQuestions = Submission::where('user_id', $user->id)
                ->where('status', 'accepted')
                ->whereHas('question', function ($q) use ($currentTrack) {
                    $q->where('field_id', $currentTrack->id)
                        ->where('is_placement', false);
                })
                ->distinct('question_id')
                ->count('question_id');

            $progress = $totalQuestions > 0
                ? round(($solvedQuestions / $totalQuestions) * 100)
                : 0;

            $fieldLevel = UserFieldLevel::where('user_id', $user->id)
                ->where('field_id', $currentTrack->id)
                ->first();

            $currentTrackData = [
                'id' => $currentTrack->id,
                'name' => $currentTrack->name,
                'description' => $currentTrack->description,

                'level' => $fieldLevel?->level ?? 'beginner',

                'xp' => $solvedQuestions * 10,

                'progress_percentage' => $progress . '%',

                'solved_questions' => $solvedQuestions,
                'remaining_questions' => max(0, $totalQuestions - $solvedQuestions),
                'total_questions' => $totalQuestions,

                'completed_modules' => 0,
                'total_modules' => 0,

                'last_used_at' => $currentTrack->last_used_at,
            ];
        }

        return response()->json([
            'tracks' => $result,
            'currentTrack' => $currentTrackData,
        ]);
    }
    public function joinField(Request $request)
    {
        $request->validate([
            'field_id' => 'required|exists:fields,id',
        ]);

        $userId = auth()->id();

        $exists = DB::table('field_user')
            ->where('user_id', $userId)
            ->where('field_id', $request->field_id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Already enrolled'], 409);
        }

        DB::table('field_user')->insert([
            'user_id' => $userId,
            'field_id' => $request->field_id,
            'last_used_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Joined field successfully']);
    }
    public function openField(Request $request)
    {
        $request->validate([
            'field_id' => 'required|exists:fields,id',
        ]);

        $userId = auth()->id();

        DB::table('field_user')
            ->where('user_id', $userId)
            ->where('field_id', $request->field_id)
            ->update([
                'last_used_at' => now()
            ]);

        return response()->json(['message' => 'updated']);
    }
}


















    //     public function joinField(Request $request)
    // {
    //     $request->validate([
    //         'field_id' => 'required|exists:fields,id',
    //     ]);

    //     $userId = auth()->id();

    //     $hasLevel = UserFieldLevel::where('user_id', $userId)
    //         ->where('field_id', $request->field_id)
    //         ->exists();

    //     if (!$hasLevel) {
    //         return response()->json([
    //             'message' => 'You must finish placement test first'
    //         ], 403);
    //     }

    //     $exists = DB::table('field_user')
    //         ->where('user_id', $userId)
    //         ->where('field_id', $request->field_id)
    //         ->exists();

    //     if ($exists) {
    //         return response()->json([
    //             'message' => 'Already enrolled'
    //         ], 409);
    //     }

    //     DB::table('field_user')->insert([
    //         'user_id' => $userId,
    //         'field_id' => $request->field_id,
    //         'last_used_at' => now(),
    //         'created_at' => now(),
    //         'updated_at' => now(),
    //     ]);

    //     return response()->json([
    //         'message' => 'Joined field successfully'
    //     ]);
    // }