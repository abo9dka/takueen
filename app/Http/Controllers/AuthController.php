<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetCodeMail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'trainer'
        ]);

        return response()->json([
            'message' => 'user created',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ]
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);



        if (!User::where('email', $request->email)->first()) {
            return response()->json([
                'message' => 'Email not found'
            ], 404);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Password incorrect'
            ], 401);
        }


        $user = Auth::user();


        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'login successfully',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'logout successfully'
        ]);
    }

    public function forgot(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $code = rand(100000, 999999);

        Cache::put(
            'reset_' . $request->email,
            ['code' => $code],
            now()->addMinutes(10)
        );

        Mail::to($request->email)->send(
            new PasswordResetCodeMail($code)
        );

        return response()->json([
            'message' => 'Check your email for reset code'
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|digits:6',
            'password' => 'required|min:6|confirmed'
        ]);

        $cached = Cache::get('reset_' . $request->email);

        if (!$cached || $cached['code'] != $request->code) {
            return response()->json([
                'message' => 'Invalid code'
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        $user->password = bcrypt($request->password);

        $user->save();

        Cache::forget('reset_' . $request->email);

        return response()->json([
            'message' => 'Password updated'
        ]);
    }

    public function createSupervisor(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'description' => 'required|string',
            'fields' => 'required|array',
            'fields.*' => 'exists:fields,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'supervisor',
            'description' => $request->description,
        ]);

        $user->fields()->attach($request->fields);

        return response()->json([
            'message' => 'Supervisor created and assigned to fields',
            'user' => $user->load('fields')
        ], 201);
    }
}

// public function forgot(Request $request)
// {
//     $request->validate([
//         'email' => 'required|email|exists:users,email'
//     ]);

//     $token = Str::random(60);

//     DB::table('password_resets')->updateOrInsert(
//         ['email' => $request->email],
//         [
//             'token' => Hash::make($token),
//             'created_at' => now()
//         ]
//     );

//     return response()->json([
//         'message' => 'reset token generated',
//         'token' => $token
//     ]);
// }

// public function reset(Request $request)
// {
//     $request->validate([
//         'email' => 'required|email',
//         'password' => 'required|min:8|confirmed',
//         'token' => 'required'
//     ]);

//     $record = DB::table('password_resets')
//         ->where('email', $request->email)
//         ->first();

//     if (!$record) {
//         return response()->json([
//             'message' => 'invalid email'
//         ], 400);
//     }

//     if (!Hash::check($request->token, $record->token)) {
//         return response()->json([
//             'message' => 'invalid token'
//         ], 400);
//     }

//     $user = User::where('email', $request->email)->first();

//     $user->password = Hash::make($request->password);

//     $user->save();

//     DB::table('password_resets')
//         ->where('email', $request->email)
//         ->delete();

//     return response()->json([
//         'message' => 'password reset successfully'
//     ]);
// }