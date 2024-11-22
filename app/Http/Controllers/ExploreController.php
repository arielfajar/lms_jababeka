<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Explore;
use Illuminate\Support\Facades\Storage;

class ExploreController extends Controller
{
    public function index()
    {
        return Explore::all();
    }

    public function show($id)
    {
        return Explore::findOrFail($id);
    }

    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'judul' => 'required|string|max:255',
            'tentang' => 'required|string',
            'objective' => 'required|string',
            'course' => 'required|string',
            'trainer' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate the image
        ]);

        // Create a new Explore instance
        $explore = new Explore();
        $explore->judul = $request->judul;
        $explore->tentang = $request->tentang;
        $explore->objective = $request->objective;
        $explore->course = $request->course;
        $explore->trainer = $request->trainer;

        // Handle the image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('explore', 'public'); // Store the image in the 'public/uploads' directory
            $explore->img = $imagePath; // Set the img attribute to the path
        }

        $explore->save(); // Save the new entry

        return response()->json(['message' => 'Explore entry created successfully', 'explore_id' => $explore->explore_id], 201);
    }

    public function update(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'judul' => 'required|string|max:255',
            'tentang' => 'required|string',
            'objective' => 'required|string',
            'course' => 'required|string',
            'trainer' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate the image
        ]);

        // Find the Explore entry
        $explore = Explore::findOrFail($id);

        // Update the fields
        $explore->judul = $request->judul;
        $explore->tentang = $request->tentang;
        $explore->objective = $request->objective;
        $explore->course = $request->course;
        $explore->trainer = $request->trainer;

        // Handle the image upload
        if ($request->hasFile('image')) {
            // Delete the old image from storage
            if ($explore->img && Storage::exists('public/' . $explore->img)) {
                Storage::delete('public/' . $explore->img);
            }
            $imagePath = $request->file('image')->store('explore', 'public'); // Store the new image
            $explore->img = $imagePath; // Set the img attribute to the new path
        }

        $explore->save(); // Save the updated entry

        return response()->json(['message' => 'Explore entry updated successfully'], 200);
    }

    public function destroy($id)
    {
        // Find the Explore entry by ID
        $explore = Explore::findOrFail($id);

        // Delete the image from storage
        if ($explore->img && Storage::exists('public/' . $explore->img)) {
            Storage::delete('public/' . $explore->img);
        }

        // Delete the Explore entry from the database
        $explore->delete();

        return response()->json(['message' => 'Explore entry deleted successfully'], 200);
    }
}
