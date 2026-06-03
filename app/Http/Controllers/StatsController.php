<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function index()
    {
        return response()->json([
            'total_users' => User::count(),
            'total_questions' => Question::count(),
            'total_submissions' => Submission::count(),
            'accepted_solutions' => Submission::where('status', 'accepted')->count(),
        ]);
    }
}
