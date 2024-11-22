<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nilai;
use App\Models\Content;

class NilaiController extends Controller
{
    public function store(Request $request)
    {
        // Validasi request
        $validatedData = $request->validate([
            'content_id' => 'required|exists:tbl_content,content_id', // Pastikan content_id ada di tbl_content
            'user_id' => 'required|exists:tbl_user,user_id', // Pastikan user_id ada di tbl_user
            'jawaban_pilihan' => 'nullable|array',
            'nilai' => 'nullable|numeric',
        ]);

        try {
            // Ambil content_id dari request
            $content_id = $request->input('content_id');

            // Ambil jenis dari tbl_content berdasarkan content_id
            $content = Content::find($content_id);

            if (!$content) {
                return response()->json(['error' => 'Content not found'], 404);
            }

            // Ambil jenis dari content
            $jenis = $content->jenis;

            $roundedNilai = round($request->input('nilai'), 0);

            // Buat data untuk disimpan ke tbl_nilai
            $nilaiData = [
                'content_id' => $content_id,
                'user_id' => $request->input('user_id'), // atau bisa juga dari auth()->id();
                'jenis' => $jenis, // jenis dari tbl_content
                'jawaban_pilihan' => $request->input('jawaban_pilihan'),
                'nilai' => $roundedNilai,
                'progress' => 1 // Progress otomatis jadi 1
            ];

            // Simpan data ke database
            Nilai::create($nilaiData);

            return response()->json(['message' => 'Nilai berhasil disimpan'], 201);
        } catch (\Exception $e) {
            // Tangani error
            return response()->json(['error' => 'Error menyimpan nilai: ' . $e->getMessage()], 500);
        }
    }

    public function getScores(Request $request)
    {
        // Jika user_id disediakan, ambil nilai untuk user tertentu
        $user_id = $request->query('user_id');

        if ($user_id) {
            $scores = Nilai::where('user_id', $user_id)->get(['content_id', 'jawaban_pilihan', 'nilai', 'progress', 'jenis']);
        } else {
            // Jika tidak ada user_id, ambil semua nilai
            $scores = Nilai::all(['content_id', 'user_id', 'jawaban_pilihan', 'nilai', 'progress', 'jenis']);
        }

        return response()->json($scores);
    }

}
