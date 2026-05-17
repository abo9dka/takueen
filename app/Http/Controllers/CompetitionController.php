<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use Illuminate\Http\Request;

class CompetitionController extends Controller
{
    
    public function index()
    {
        return Competition::all();
    }

   
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|string',
            'link_register_competition' => 'nullable|url',
            'user_id' => 'required|exists:users,id'
        ]);

        $competition = Competition::create([
            'title' => $request->title,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
            'link_register_competition' => $request->link_register_competition,
            'user_id' => $request->user_id
        ]);

        return response()->json($competition, 201);
    }

    
    public function show($id)
    {
        return Competition::findOrFail($id);
    }

   
    public function update(Request $request, $id)
    {
        $competition = Competition::findOrFail($id);

        $competition->update($request->all());

        return response()->json($competition);
    }

    
    public function destroy($id)
    {
        $competition = Competition::findOrFail($id);

        $competition->delete();

        return response()->json([
            'message' => 'Competition deleted successfully'
        ]);
    }
}