<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function leaderboard()
    {
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

        return response()->json($users, 200);
    }
    public function trainees()
    {
        $trainees = User::where('role', 'trainer')
            ->select('id', 'name', 'role')
            ->get();

        return response()->json($trainees, 200);
    }
    public function supervisorsByField($fieldId)
    {
        $users = DB::table('field_user')
            ->where('field_id', $fieldId)
            ->get();

        return response()->json($users);
    }
    public function supervisorsById($id)
    {
        $supervisor = User::where('role', 'supervisor')
            ->where('id', $id)
            ->select('id', 'name', 'role', 'description')
            ->first();

        if (!$supervisor) {
            return response()->json(['message' => 'Supervisor not found'], 404);
        }

        return response()->json($supervisor, 200);
    }
    public function supervisors()
    {
        $supervisors = User::where('role', 'supervisor')
            ->select('id', 'name', 'role', 'description')
            ->get();

        return response()->json($supervisors, 200);
    }
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed'
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect'
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'message' => 'Password changed successfully'
        ]);
    }
    public function supervisorTrainees($id)
    {
        $supervisor = User::where('role', 'supervisor')->findOrFail($id);

        return response()->json([
            'supervisor' => $supervisor->name,
            'trainees' => $supervisor->trainees
        ]);
    }
    public function chooseSupervisor(Request $request)
    {
        $request->validate([
            'supervisor_id' => 'required|exists:users,id'
        ]);

        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        if ($user->role !== 'trainer') {
            return response()->json([
                'message' => 'Only trainees can choose supervisor'
            ], 403);
        }

        $user->supervisor_id = $request->supervisor_id;
        $user->save();

        return response()->json([
            'message' => 'Supervisor selected successfully',
            'user' => $user->fresh()
        ]);
    }
}
