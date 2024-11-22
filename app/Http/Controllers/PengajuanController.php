<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PengajuanController extends Controller
{
    // Function untuk menyimpan data pengajuan
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:tbl_user,user_id',
            'nik' => 'required|string',
            'jabatan' => 'required|string',
            'nama' => 'required|string',
            'divisi' => 'required|string',
            'tanggal_masuk' => 'required|date',
            'email' => 'required|email',
            'judul_training' => 'required|string',
            'penyelenggara' => 'required|string',
            'tujuan_training' => 'array|min:1', // Array minimal 1
            'tujuan_training.*' => 'string|nullable',
            'harapan_training' => 'array|min:1', // Array minimal 1
            'harapan_training.*' => 'nullable',
            'biaya' => 'required|numeric',
            'hari_tanggal' => 'required|date',
            'jam' => 'required',
            'tempat' => 'required|string',
            // 'ttd' => 'sometimes|file|mimes:jpeg,png,jpg,pdf|max:2048', // Optional file upload
            'approval_line' => 'nullable',
        ]);

        // Jika validasi gagal, kembalikan error
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Simpan tanda tangan jika ada
        $ttdPath = null;
        if ($request->hasFile('ttd')) {
            $ttdPath = $request->file('ttd')->store('ttd', 'public'); // Simpan di direktori 'storage/app/public/ttd'
        }

        // Simpan data ke dalam tabel tbl_pengajuan
        $pengajuan = Pengajuan::create([
            'user_id' => $request->user_id,
            'nik' => $request->nik,
            'jabatan' => $request->jabatan,
            'nama' => $request->nama,
            'divisi' => $request->divisi,
            'tanggal_masuk' => $request->tanggal_masuk,
            'email' => $request->email,
            'judul_training' => $request->judul_training,
            'penyelenggara' => $request->penyelenggara,
            'tujuan_training' => json_encode($request->tujuan_training), // Simpan sebagai JSON
            'harapan_training' => json_encode($request->harapan_training), // Simpan sebagai JSON
            'biaya' => $request->biaya,
            'hari_tanggal' => $request->hari_tanggal,
            'jam' => $request->jam,
            'tempat' => $request->tempat,
            'ttd' => $ttdPath,
            'status' => '0', // Set default status
            'approval_line' => $request->approval_line, // Atur jika ada proses approval
        ]);

        // Kembalikan respons berhasil
        return response()->json([
            'message' => 'Pengajuan berhasil disimpan!',
            'pengajuan' => $pengajuan
        ], 201);
    }

    // Function untuk menampilkan semua pengajuan
    public function index()
    {
        $pengajuans = Pengajuan::all();
        return response()->json($pengajuans);
    }

    // Function untuk menampilkan pengajuan berdasarkan ID
    public function show($id)
    {
        $pengajuan = Pengajuan::find($id);

        if (!$pengajuan) {
            return response()->json(['message' => 'Pengajuan tidak ditemukan'], 404);
        }

        return response()->json($pengajuan);
    }

public function update(Request $request, $id)
{
    $pengajuan = Pengajuan::find($id);
    if (!$pengajuan) {
        return response()->json(['message' => 'Pengajuan tidak ditemukan'], 404);
    }

    // Validate the incoming request
    $validator = Validator::make($request->all(), [
        'status' => 'required|in:0,1,2', // Validate status
        // Add other validation rules if needed
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Update the pengajuan with the new status
    $pengajuan->update($request->all());
    return response()->json(['message' => 'Pengajuan berhasil diperbarui'], 200);
}



    // Function to delete a pengajuan
public function destroy($id)
{
    $pengajuan = Pengajuan::find($id);

    if (!$pengajuan) {
        return response()->json(['message' => 'Pengajuan tidak ditemukan'], 404);
    }

    // Delete the pengajuan
    $pengajuan->delete();

    return response()->json(['message' => 'Pengajuan berhasil dihapus'], 200);
}

}
