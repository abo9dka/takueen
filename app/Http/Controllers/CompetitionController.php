<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompetitionController extends Controller
{
    public function index(Request $request)
    {
        Competition::where('status', '!=', 'finished')
            ->where('end_date', '<', now())
            ->update([
                'status' => 'finished'
            ]);

        $competitions = Competition::with([
            'user',
            'supervisors'
        ])->withCount('participants');

        if ($request->status == 'active') {

            $competitions->where('start_date', '<=', now())
                ->where('end_date', '>=', now());
        } elseif ($request->status == 'upcoming') {

            $competitions->where('start_date', '>', now());
        } elseif ($request->status == 'past') {

            $competitions->where('status', 'finished');
        }

        return $competitions->latest()->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'link_register_competition' => 'nullable|url',
            'image' => 'nullable|string',
            'prize' => 'nullable|integer',
            'status' => 'required|in:upcoming,ongoing',
            'supervisor_ids' => 'nullable|array',
            'supervisor_ids.*' => [
                Rule::exists('users', 'id')
                    ->where('role', 'supervisor'),
            ],
        ]);

        $competition = Competition::create([

            'title' => $request->title,

            'description' => $request->description,

            'start_date' => $request->start_date,

            'end_date' => $request->end_date,

            'link_register_competition'
            => $request->link_register_competition,

            'image' => $request->image,

            'prize' => $request->prize ?? 0,

            'user_id' => $request->user()->id,
        ]);
        if ($request->has('supervisor_ids')) {
            $competition->supervisors()
                ->attach($request->supervisor_ids);
        }
        return response()->json([
            'message' => 'Competition created successfully',
            'competition' => $competition->load([
                'user',
                'supervisors'
            ])
        ]);
    }
    public function show($id)
    {
        $competition = Competition::with([
            'user',
            'supervisors'
        ])->withCount('participants')
            ->findOrFail($id);

        return $competition;
    }

    public function update(Request $request, $id)
    {
        $competition = Competition::findOrFail($id);

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'status' => 'sometimes|in:upcoming,active',
            'link_register_competition' => 'nullable|url',
            'image' => 'nullable|string',
            'prize' => 'nullable|integer',
            'supervisor_ids' => 'sometimes|array',
            'supervisor_ids.*' => [
                Rule::exists('users', 'id')
                    ->where('role', 'supervisor'),
            ],
        ]);
        $competition->update(
            $request->only([
                'title',
                'description',
                'start_date',
                'end_date',
                'link_register_competition',
                'image',
                'prize',
                'status'
            ])
        );

        if ($request->has('supervisor_ids')) {

            $competition->supervisors()
                ->sync($request->supervisor_ids);
        }

        return response()->json([

            'message' => 'Competition updated successfully',
            'competition' => $competition->load([
                'user',
                'supervisors'
            ])
        ]);
    }
    public function destroy($id)
    {
        $competition = Competition::findOrFail($id);
        $competition->delete();
        return response()->json([
            'message' => 'Competition deleted successfully'
        ]);
    }

    public function assignSupervisor(Request $request, $id)
    {
        $competition = Competition::findOrFail($id);
        $request->validate([
            'supervisor_ids' => 'required|array',
            'supervisor_ids.*' => [
                Rule::exists('users', 'id')
                    ->where('role', 'supervisor'),
            ],
        ]);

        $competition->supervisors()
            ->sync($request->supervisor_ids);

        return response()->json([

            'message'
            => 'Supervisors assigned successfully',

            'competition'
            => $competition->load([
                'user',
                'supervisors'
            ])
        ]);
    }
    public function join(Request $request, $id)
    {
        $competition = Competition::findOrFail($id);
        $competition->participants()
            ->syncWithoutDetaching([
                $request->user()->id
            ]);
        return response()->json([
            'message'
            => 'Joined successfully'
        ]);
    }

    public function leave(Request $request, $id)
    {
        $competition = Competition::findOrFail($id);
        $competition->participants()
            ->detach($request->user()->id);
        return response()->json([
            'message'
            => 'Left competition successfully'
        ]);
    }
}
