<?php

namespace App\Http\Controllers;

use App\Models\Soal;
use Illuminate\Http\Request;

class SoalController extends Controller
{
    public function index()
    {
        $soals = Soal::all();
        return response()->json($soals);
    }

    public function getByContentId($content_id)
    {
        $soals = Soal::where('content_id', $content_id)->get();
        return response()->json($soals);
    }

    public function store(Request $request)
    {
        $request->validate([
            'content_id' => 'required|exists:tbl_content,content_id',
            'pertanyaan' => 'required|string|max:255',
            'jawaban' => 'required|array',
            'jawaban_benar' => 'required|string|max:255',
            'durasi' => 'required|integer'
        ]);

        try {
            $soal = new Soal;
            $soal->content_id = $request->content_id;
            $soal->pertanyaan = $request->pertanyaan;
            $soal->jawaban = json_encode($request->jawaban);
            $soal->jawaban_benar = $request->jawaban_benar;
            $soal->durasi = $request->durasi * 60;

            $soal->save();

            return response()->json($soal, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to save soal', 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'pertanyaan' => 'required|string|max:255',
            'jawaban' => 'required|array',
            'jawaban_benar' => 'required|string|max:255',
            'durasi' => 'required|integer'
        ]);
    
        try {
            $soal = Soal::findOrFail($id);
            $soal->pertanyaan = $request->pertanyaan;
            $soal->jawaban = json_encode($request->jawaban);
            $soal->jawaban_benar = $request->jawaban_benar;
            $soal->durasi = $request->durasi * 60;
    
            $soal->save();
            return response()->json($soal);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update soal', 'message' => $e->getMessage()], 500);
        }
    }
    


    public function destroy($id)
    {
        try {
            Soal::destroy($id);
            return response()->json(['message' => 'Soal deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete soal', 'message' => $e->getMessage()], 500);
        }
    }
}
