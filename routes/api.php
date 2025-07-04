<?php

use App\Http\Middleware\IonicRole;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KertanController;
use App\Http\Controllers\AuthController; // Pastikan ini diimpor dengan benar

// Rute untuk Login API
Route::post('/login', [AuthController::class, 'apiLogin']);




// Rute yang dilindungi oleh Sanctum dan middleware IonicRole (untuk admin2 dan user)
Route::middleware(['auth:sanctum', 'ionic.role:admin2,user'])->group(function () {
    // Rute API yang sudah ada untuk users, warga, satpam, dll.
    Route::get('/users', [KertanController::class, 'index']);
    Route::get('/users/{id}', [KertanController::class, 'show']);
    Route::put('/users/{id}', [KertanController::class, 'update']);
    Route::delete('/users/{id}', [KertanController::class, 'destroy']);
    Route::get('/satpam-data', [KertanController::class, 'tampilkanSatpam']);
    Route::get('/api-satpam-data', [KertanController::class, 'getSatpamDataApi']); // Rute baru untuk API


    // Rute API untuk logout dan mendapatkan info pengguna yang terotentikasi
    // ***** PERBAIKAN DI SINI *****
    Route::post('/logout', [AuthController::class, 'apiLogout']); // Mengarah ke AuthController
    Route::get('/user', [AuthController::class, 'getAuthenticatedUser']); // Mengarah ke AuthController

    // >>>>>> PINDAHKAN INI KE SINI AGAR BISA DIAKSES OLEH user DAN admin2 <<<<<<
    Route::get('/warga-data', [KertanController::class, 'tampilkanWarga']);
});

// Rute khusus untuk admin2 (hanya rute yang hanya boleh diakses admin2)
Route::middleware(['auth:sanctum', 'ionic.role:admin2'])->group(function () {
    Route::post('/registrasi-tamu', [KertanController::class, 'storeRegistrasiTamu']);
    Route::get('/data-tamu', [KertanController::class, 'getDataTamu']);
    Route::get('/data-tamu/{id}', [KertanController::class, 'showDataTamu']);
    Route::put('/data-tamu/{id}', [KertanController::class, 'updateDataTamu'])->name('data-tamu.update');
    Route::delete('/data-tamu/{id}', [KertanController::class, 'deleteDataTamu']);
    Route::put('/data-tamu/{id}/status', [KertanController::class, 'updateTamuStatus']);

    // Route::get('/warga-data', [KertanController::class, 'tampilkanWarga']); // Sudah ada di atas, bisa dihapus/dikomentari
});

// Rute khusus untuk user
Route::middleware(['auth:sanctum', 'ionic.role:user'])->group(function () {
    Route::post('/simpan-warga', [KertanController::class, 'storeWarga']);
});

// Rute Laporan (perlu diperiksa apakah ini juga dilindungi atau tidak)
// Jika rute ini juga butuh autentikasi, masukkan ke dalam grup middleware 'auth:sanctum'
// Saat ini, mereka tidak dilindungi.
Route::prefix('tamu-laporan')->group(function () {
    Route::get('/harian/{tanggal?}', [KertanController::class, 'getLaporanHarian']);
    Route::get('/mingguan', [KertanController::class, 'getRekapMingguanTamu']);
    Route::get('/bulanan-rekap/{year?}', [KertanController::class, 'getRekapBulananTamu']);
    Route::get('/tahunan/{tahun?}', [KertanController::class, 'getLaporanTahunan']);
});

// Grup rute yang seharusnya untuk warga/user (di bawah auth:sanctum dan ionic.role:user)
// Ini adalah duplikasi dari grup user di atas, sebaiknya digabungkan
Route::middleware('auth:sanctum')->group(function () {
    Route::middleware(IonicRole::class . ':user')->group(function () {
        Route::post('/warga/ajukan-surat', [KertanController::class, 'storePengajuanApiWarga']);
        Route::get('/warga/pengajuan-saya', [KertanController::class, 'indexMyPengajuansApiWarga']);
        Route::get('/warga/pengajuan-saya/{id}', [KertanController::class, 'showMyPengajuanApiWarga']);
        // 1. Mengirim pengaduan baru
        Route::post('/warga/ajukan-pengaduan', [KertanController::class, 'storePengaduan']);
        // 2. Melihat daftar pengaduan yang sudah diajukan oleh user yang bersangkutan
        Route::get('/warga/pengaduan-saya', [KertanController::class, 'indexMyPengaduan']);
        // 3. Melihat detail pengaduan tertentu yang sudah diajukan oleh user yang bersangkutan
        Route::get('/warga/pengaduan-saya/{id}', [KertanController::class, 'showMyPengaduan']);
        Route::get('/rt', [KertanController::class, 'getRtData']);
         Route::get('/warga-profile', [KertanController::class, 'showprofilwarga']);
         Route::put('/warga-profile', [KertanController::class, 'updateprofilwarga']);
    });


});

