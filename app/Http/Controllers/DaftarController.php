<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Daftar;
use App\Models\User;
use App\Models\Explore; // Assuming you have an Explore model
use Illuminate\Support\Facades\Auth;

class DaftarController extends Controller
{
    public function store(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'user_id' => 'required|exists:tbl_user,user_id',
            'explore_id' => 'required|exists:tbl_explore,explore_id',
        ]);

        // Create a new Daftar entry
        Daftar::create([
            'user_id' => $request->user_id,
            'explore_id' => $request->explore_id,
            'status' => 0,
        ]);

        return response()->json(['message' => 'Registration successful'], 201);
    }

    public function getUserTrainings($userId)
    {
        // Fetch the user's trainings with related explore information
        $trainings = Daftar::where('user_id', $userId)
            ->with('explore') // Assuming there's a relation defined in the Daftar model
            ->get()
            ->map(function ($daftar) {
                return [
                    'title' => $daftar->explore->judul, // Assuming title is in the Explore model
                    'image' => $daftar->explore->img, // Assuming image is in the Explore model
                    'explore_id' => $daftar->explore->explore_id, // Add explore_id for routing
                ];
            });

        return response()->json($trainings);
    }

    public function getAllDaftar()
{
    $daftars = Daftar::with('explore', 'user')
        ->get()
        ->map(function ($daftar) {
            return [
                'daftar_id' => $daftar->daftar_id,
                'user_id' => $daftar->user_id,
                'nama_lengkap' => optional($daftar->user)->nama_lengkap, // Menggunakan optional() untuk menghindari error jika user null
                'title' => $daftar->explore->judul,
                'explore_id' => $daftar->explore->explore_id,
                'status' => $daftar->status
            ];
        });

    return response()->json($daftars);
}

public function updateStatus(Request $request, $id)
{
    // Validasi status
    $request->validate([
        'status' => 'required|in:1,2'
    ]);

    // Temukan entry berdasarkan ID
    $daftar = Daftar::find($id);

    // Cek jika entry ditemukan
    if (!$daftar) {
        return response()->json(['message' => 'Entry not found'], 404);
    }

    // Perbarui status
    $daftar->status = $request->status;
    $daftar->save();

    return response()->json(['message' => 'Status updated successfully']);
}


public function destroy($id)
{
    // Find the entry by ID
    $daftar = Daftar::find($id);

    // Check if the entry exists
    if (!$daftar) {
        return response()->json(['message' => 'Entry not found'], 404);
    }

    // Delete the entry
    $daftar->delete();

    return response()->json(['message' => 'Entry deleted successfully'], 200);
}


}
