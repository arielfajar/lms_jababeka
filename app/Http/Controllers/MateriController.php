<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MateriController extends Controller
{
    public function index()
    {
        $materi = Materi::all();
        return response()->json($materi);
    }

    public function getByContentId($content_id)
    {
        $materi = Materi::where('content_id', $content_id)->get();
        return response()->json($materi);
    }

    public function store(Request $request)
    {
        $request->validate([
            'content_id' => 'required|exists:tbl_content,content_id',
            'judul' => 'required|string|max:255',
            'jenis' => 'required|string|max:255',
            'link' => 'required',
        ]);

        try {
            $materi = new Materi;
            $materi->content_id = $request->content_id;
            $materi->judul = $request->judul;
            $materi->jenis = $request->jenis;

            if ($request->hasFile('link')) {
                $file = $request->file('link');
                $fileName = time().'_'.$file->getClientOriginalName();
                $filePath = $file->storeAs('materi', $fileName, 'public');
                $materi->link = $filePath;
            } else if ($request->jenis === 'youtube') {
                $youtubeLink = $request->link;
                preg_match('/(youtu\.be\/|youtube\.com\/(watch\?(.*&)?v=|(embed|v)\/))([^?&"\'>]+)/', $youtubeLink, $matches);
                $embedLink = 'https://www.youtube.com/embed/' . $matches[5];
                $materi->link = $embedLink;
            } else {
                $materi->link = $request->link;
            }

            $materi->save();

            return response()->json($materi, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to save materi', 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'jenis' => 'required|string|max:255',
            'link' => 'nullable',
        ]);
    
        try {
            $materi = Materi::findOrFail($id);
            $materi->judul = $request->judul;
            $materi->jenis = $request->jenis;
    
            // Periksa apakah ada file baru yang diunggah
            if ($request->hasFile('link')) {
                // Hapus file lama jika ada
                if (Storage::exists('public/' . $materi->link)) {
                    Storage::delete('public/' . $materi->link);
                }
    
                $file = $request->file('link');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('materi', $fileName, 'public');
                $materi->link = $filePath;
            } else if ($request->jenis === 'youtube' && $request->link) {
                // Update link jika tipe adalah YouTube
                preg_match('/(youtu\.be\/|youtube\.com\/(watch\?(.*&)?v=|(embed|v)\/))([^?&"\'>]+)/', $request->link, $matches);
                $materi->link = 'https://www.youtube.com/embed/' . $matches[5];
            }
    
            $materi->save();
            return response()->json($materi);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update materi', 'message' => $e->getMessage()], 500);
        }
    }
    


    public function destroy($id)
    {
        try {
            $materi = Materi::findOrFail($id);

            if (Storage::exists('public/' . $materi->link)) {
                Storage::delete('public/' . $materi->link);
            }

            $materi->delete();
            return response()->json(['message' => 'Materi deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete materi', 'message' => $e->getMessage()], 500);
        }
    }
}
