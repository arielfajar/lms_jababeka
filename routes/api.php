<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\DaftarController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\SoalController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\SertifikatController;
use App\Http\Controllers\TtdController;


Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'getUser']); // Updated to use getUser method
    Route::post('/user/update', [AuthController::class, 'update']);
    Route::post('/user/add', [AuthController::class, 'add']);
    Route::post('user/{id}', [AuthController::class, 'updatedata']);
    Route::delete('user/{id}', [AuthController::class, 'destroy']);
});



Route::get('/units', [UnitController::class, 'index']); // Get all units
Route::post('/units', [UnitController::class, 'store']); // Create a new unit
Route::put('/units/{id}', [UnitController::class, 'update']); // Update a unit
Route::delete('/units/{id}', [UnitController::class, 'destroy']); // Delete a unit


Route::get('/explore', [ExploreController::class, 'index']);
Route::get('/explore/{id}', [ExploreController::class, 'show']);
Route::post('/explore', [ExploreController::class, 'store']);
Route::post('/explore/{id}', [ExploreController::class, 'update']);
Route::delete('/explore/{id}', [ExploreController::class, 'destroy']);

Route::post('/content', [ContentController::class, 'store']);
Route::get('/content', [ContentController::class, 'index']);
Route::get('/content/{explore_id}', [ContentController::class, 'getByExploreId']);


Route::get('/soal', [SoalController::class, 'index']);
Route::get('/soal/{content_id}', [SoalController::class, 'getByContentId']);
Route::post('/soal', [SoalController::class, 'store']);
Route::put('/materi/{id}', [MateriController::class, 'update']);
Route::delete('/soal/{id}', [SoalController::class, 'destroy']);


Route::get('/materi', [MateriController::class, 'index']);
Route::get('/materi/{content_id}', [MateriController::class, 'getByContentId']);
Route::post('/materi', [MateriController::class, 'store']);
Route::put('/soal/{id}', [SoalController::class, 'update']);
Route::delete('/materi/{id}', [MateriController::class, 'destroy']);



Route::post('/nilai', [NilaiController::class, 'store']);
Route::get('/nilai', [NilaiController::class, 'getScores']);


Route::post('/sertifikat', [SertifikatController::class, 'store']);
Route::get('/sertifikat', [SertifikatController::class, 'index']);
Route::get('/sertifikat/exists', [SertifikatController::class, 'checkIfExists']);
Route::get('/sertifikat/count', [SertifikatController::class, 'countUserCertificates']);


Route::post('/ttd', [TtdController::class, 'store']);
Route::get('/ttd', [TtdController::class, 'index']);
Route::post('/ttd/update', [TtdController::class, 'update']);
Route::delete('/ttd/{id}', [TtdController::class, 'destroy']);


// In your routes/api.php file
Route::get('/daftar/{user_id}', [DaftarController::class, 'getUserTrainings']);
Route::get('/daftar-all', [DaftarController::class, 'getAllDaftar']);
Route::post('/daftar', [DaftarController::class, 'store']);
Route::patch('/daftar/{id}/status', [DaftarController::class, 'updateStatus']);
Route::delete('/daftar/{id}', [DaftarController::class, 'destroy']);


Route::post('/pengajuan', [PengajuanController::class, 'store']);
Route::get('/pengajuan', [PengajuanController::class, 'index']); // Untuk melihat semua pengajuan
Route::get('/pengajuan/{id}', [PengajuanController::class, 'show']); // Untuk melihat detail pengajuan berdasarkan ID
Route::put('/pengajuan/{id}', [PengajuanController::class, 'update']);
Route::delete('/pengajuan/{id}', [PengajuanController::class, 'destroy']); // Route for deleting a pengajuan


