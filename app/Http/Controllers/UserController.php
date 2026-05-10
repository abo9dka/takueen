<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function leaderboard()
    {
        $users = User::where('role', 'trainer')
            ->orderByDesc('points')
            ->select('id', 'name', 'points', 'role')
            ->take(10)
            ->get();

        return response()->json($users, 200);
    }
    public function trainees()
    {
        $trainees = User::where('role', 'trainee')
            ->select('id', 'name', 'role')
            ->get();

        return response()->json($trainees, 200);
    }
    public function supervisors()
    {
        $supervisors = User::where('role', 'supervisor')
            ->select('id', 'name', 'role')
            ->get();

        return response()->json($supervisors, 200);
    }
}
