<?php

namespace App\Http\Controllers;

use App\Models\Ttd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TtdController extends Controller
{
    public function index()
    {
        return Ttd::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'img' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Store the image
        $imagePath = $request->file('img')->store('avatars', 'public');

        // Create the TTD record
        $ttd = Ttd::create([
            'nama' => $request->nama,
            'jabatan' => $request->jabatan,
            'img' => $imagePath,
        ]);

        return response()->json($ttd, 201);
    }

    public function update(Request $request)
{
    try {
        $request->validate([
            'id' => 'required|exists:tbl_ttd,ttd_id',
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            // 'img' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $ttd = Ttd::findOrFail($request->id);

        if ($request->hasFile('img')) {
            // Delete old image
            Storage::disk('public')->delete($ttd->img);

            // Store the new image
            $imagePath = $request->file('img')->store('avatars', 'public');
            $ttd->img = $imagePath;
        }

        // Update other fields
        $ttd->nama = $request->nama;
        $ttd->jabatan = $request->jabatan;
        $ttd->save();

        return response()->json($ttd, 200);
    } catch (\Exception $e) {
        // Log the error message for debugging
        Log::error('Update TTD error: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to update TTD: ' . $e->getMessage()], 500);
    }
}


    public function destroy($id)
    {
        $ttd = Ttd::findOrFail($id);
        Storage::disk('public')->delete($ttd->img);
        $ttd->delete();

        return response()->json(null, 204);
    }
}
