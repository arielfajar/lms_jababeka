<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    // Get all units
    public function index()
    {
        // Fetch all unit data from the database
        $units = Unit::all();

        // Return the data as JSON response
        return response()->json($units);
    }

    // Store a new unit
    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'unit_nama' => 'required|string|max:255',
            'unit_slug' => 'required|string|max:255',
            'divisi' => 'required|string|max:255',
        ]);

        // Create a new unit entry in the database
        $unit = Unit::create($request->all());

        // Return the newly created unit as JSON
        return response()->json($unit, 201);
    }

    // Update an existing unit
    public function update(Request $request, $id)
    {
        // Find the unit by ID
        $unit = Unit::findOrFail($id);

        // Validate the incoming request
        $request->validate([
            'unit_nama' => 'required|string|max:255',
            'unit_slug' => 'required|string|max:255',
            'divisi' => 'required|string|max:255',
        ]);

        // Update the unit with new data
        $unit->update($request->all());

        // Return the updated unit as JSON
        return response()->json($unit);
    }

    // Delete a unit
    public function destroy($id)
    {
        // Find the unit by ID and delete it
        $unit = Unit::findOrFail($id);
        $unit->delete();

        // Return a success response
        return response()->json(['message' => 'Unit deleted successfully']);
    }
}
