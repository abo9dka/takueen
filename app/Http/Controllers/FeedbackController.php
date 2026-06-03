<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'rating' => 'nullable|integer|min:1|max:5'
        ]);
        $user = Auth::user();
        $feedback = Feedback::create([
            'user_id' => $user->id,
            'message' => $request->message,
            'rating' => $request->rating
        ]);

        return response()->json([
            'message' => 'Feedback added successfully',
            'data' => $feedback
        ], 201);
    }

    public function index()
    {
        $feedbacks = Feedback::with('user')
            ->latest()
            ->get();

        return response()->json($feedbacks);
    }
}
