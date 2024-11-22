<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage; // Add this for storage handling

class AuthController extends Controller
{
    public function login(Request $request)
{
    // Validasi input
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|min:2',
    ]);

    // Cari user berdasarkan email
    $user = User::where('email', $request->email)->first();

    // Cek kredensial menggunakan Hash::check
    if ($user && Hash::check($request->password, $user->password)) { // Use Hash::check
        // Login dan buat token API
        $token = $user->createToken('api_token')->plainTextToken;

        // Kembalikan data user dan token API
        return response()->json([
            'message' => 'Login berhasil',
            'nama_lengkap' => $user->nama_lengkap,
            'role_id' => $user->role_id,
            'token' => $token,  // Kembalikan token
        ]);
    } else {
        // Kredensial salah
        return response()->json(['error' => 'Email atau password salah'], 401);
    }
}



    public function logout(Request $request)
    {
        // Hapus token yang sedang digunakan oleh user
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil']);
    }

    public function getUser(Request $request)
    {
        if ($request->has('list')) {
            // Return all users for karyawan.vue
            $users = User::all();
            return response()->json($users);
        } else {
            // Return single user data for role-based menu rendering
            $user = auth()->user();
            return response()->json($user);
        }
    }

    public function update(Request $request)
{
    // Retrieve the currently authenticated user
    $user = Auth::user(); // Make sure this is correct

    // Ensure that $user is not null
    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }

    // Validate the request
    $request->validate([
        'nik' => 'required|string|max:255',
        'nama_lengkap' => 'required|string|max:255',
        'email' => 'required|string|email|max:255',
        'role_id' => 'required|integer',
        'no_telp' => 'nullable|string|max:255',
        // 'approval_line' => 'nullable|string|max:255',
       'join_date' => 'nullable|date_format:Y-m-d',
        'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validation for the image
        'password' => 'nullable|string|min:6|confirmed',
    ]);

    // Update user data
    $user->nik = $request->nik;
    $user->nama_lengkap = $request->nama_lengkap;
    $user->username = $request->username;
    $user->email = $request->email;
    $user->role_id = $request->role_id;
    $user->no_telp = $request->no_telp;
    $user->divisi = $request->divisi;
    $user->jabatan = $request->jabatan;
    // $user->approval_line = $request->approval_line;
    $user->join_date = $request->join_date;

    // Handle avatar upload if it exists
    if ($request->hasFile('foto')) {
        $path = $request->file('foto')->store('avatars', 'public');
        $user->foto = $path;
    }

    // Hash and update password if provided
    if ($request->filled('password')) {
        $user->password = Hash::make($request->password); // Hash the new password
    }

    // Save the user data
    $user->save(); // Ensure $user is an instance of the User model

    // Return the updated user data
    return response()->json($user, 200);
}

public function updatedata(Request $request, $id)
{
    // Validasi data yang dikirim
    $request->validate([
        'nik' => 'required|string|max:255',
        'nama_lengkap' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'username' => 'required|string|max:255',
        'no_telp' => 'nullable|string|max:255',
        'approval_line' => 'nullable|string|max:255',
        'role_id' => 'required|integer',
        'password' => 'nullable|string|min:6', // Opsional, tetapi jika dikirim harus terkonfirmasi
        'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    // Temukan user berdasarkan ID
    $user = User::find($id);

    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }

    // Update data user
    $user->nik = $request->nik;
    $user->nama_lengkap = $request->nama_lengkap;
    $user->username = $request->username;
    $user->email = $request->email;
    $user->no_telp = $request->no_telp;
    $user->divisi = $request->divisi;
    $user->jabatan = $request->jabatan;
    $user->approval_line = $request->approval_line;
    $user->role_id = $request->role_id;

    // Jika ada file foto yang di-upload
    if ($request->hasFile('foto')) {
        $path = $request->file('foto')->store('avatars', 'public');
        $user->foto = $path;
    }

    // Jika ada password baru yang dikirim, hash dan simpan
    if ($request->filled('password')) {
        $user->password = Hash::make($request->password);
    }

    // Simpan perubahan ke database
    $user->save();

    return response()->json(['message' => 'Employee updated successfully', 'user' => $user], 200);
}


public function add(Request $request)
{
    // Validate input fields
    $request->validate([
        'nik' => 'required|string|max:255',
        'nama_lengkap' => 'required|string|max:255',
        'email' => 'required|email|unique:tbl_user,email', // Ensure email is unique
        'password' => 'required|string|min:6',
        'role_id' => 'required|integer',
        'no_telp' => 'nullable|string|max:255',
        'divisi' => 'nullable|string|max:255',
        'jabatan' => 'nullable|string|max:255',
        'join_date' => 'nullable|date_format:Y-m-d', // Date format validation
        'approval_line' => 'nullable|string|max:255',
        'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Image validation
    ]);

    // Create a new User instance
    $user = new User();
    $user->nik = $request->nik;
    $user->nama_lengkap = $request->nama_lengkap;
    $user->email = $request->email; // Store email in the database
    $user->password = Hash::make($request->password); // Hash the password
    $user->role_id = $request->role_id;
    $user->no_telp = $request->no_telp;
    $user->username = $request->username; // Include username
    $user->divisi = $request->divisi;
    $user->jabatan = $request->jabatan;
    $user->join_date = $request->join_date;
    $user->approval_line = $request->approval_line;

    // Handle file upload for profile picture
    if ($request->hasFile('foto')) {
        $file = $request->file('foto');
        $path = $file->store('avatars', 'public'); // Save image in the storage
        $user->foto = $path; // Save the file path in the database
    }

    // Save the user instance to the database
    if ($user->save()) {
        return response()->json(['message' => 'Employee added successfully', 'user_id' => $user->user_id], 201);
    } else {
        return response()->json(['error' => 'Failed to add employee'], 500);
    }
}
public function destroy($id)
{
    // Find the user by ID
    $user = User::find($id);
    
    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }
    
    // Delete the user
    $user->delete();

    return response()->json(['message' => 'User deleted successfully']);
}


    
}


