<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sertifikat;

class SertifikatController extends Controller
{
    public function store(Request $request)
{
    $sertifikat = Sertifikat::create([
        'explore_id' => $request->explore_id,
        'user_id' => $request->user_id,
        'tanggal' => $request->tanggal,
    ]);

    return response()->json(['message' => 'Sertifikat berhasil disimpan'], 200);
}
// SertifikatController.php
public function index(Request $request)
{
    $userId = $request->input('user_id'); // Get the user_id from the request
    $sertifikats = Sertifikat::with(['explore', 'user'])
        ->when($userId, function ($query) use ($userId) {
            return $query->where('user_id', $userId); // Filter by user_id
        })
        ->get();
    return response()->json($sertifikats, 200);
}

public function checkIfExists(Request $request) {
    $exists = Sertifikat::where('user_id', $request->user_id)
                ->where('explore_id', $request->explore_id)
                ->exists();

    return response()->json(['exists' => $exists]);
}

public function countUserCertificates(Request $request) {
    $userId = $request->query('user_id');

    $certificateCount = Sertifikat::where('user_id', $userId)->count();

    return response()->json(['count' => $certificateCount]);
}


}
