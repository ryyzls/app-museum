<?php

namespace App\Http\Controllers;

use App\Models\Exhibition;
use Illuminate\Http\Request;

class ExhibitionController extends Controller
{
    public function index()
    {
        $exhibitions = Exhibition::with(['museum', 'artworks'])->get();

        return view(
            'exhibitions.index',
            compact('exhibitions')
        );
    }

    public function show($id)
    {
        $exhibition = Exhibition::with([
            'museum',
            'artworks'
        ])->findOrFail($id);

        return view(
            'exhibitions.show',
            compact('exhibition')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'museum_id' => 'required|exists:museums,id',
        ]);

        $exhibition = Exhibition::create($validated);

        return response()->json([
            'message' => 'Exhibition created successfully',
            'data' => $exhibition
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $exhibition = Exhibition::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date',
            'museum_id' => 'sometimes|exists:museums,id',
        ]);

        $exhibition->update($validated);

        return response()->json([
            'message' => 'Exhibition updated successfully',
            'data' => $exhibition
        ]);
    }

    public function destroy($id)
    {
        $exhibition = Exhibition::findOrFail($id);
        $exhibition->delete();

        return response()->json([
            'message' => 'Exhibition deleted successfully'
        ]);
    }
}
