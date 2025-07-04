<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KertanController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/beranda', [KertanController::class, 'beranda'])
    ->middleware('cekrole:admin1') // jika memang khusus admin1
    ->name('beranda');
<<<<<<< HEAD
    
=======
>>>>>>> 535ea1bba039034241405b95dc5c1a39bb663298

// Halaman welcome setelah login
Route::get('/welcome', [AuthController::class, 'showWelcomePage'])->name('welcome');

// Proses login
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Form registrasi admin1
//Route::get('/register-admin1', function () {
   // return view('register-admin1');
//})->name('register.admin1');

// Proses registrasi admin1
//Route::post('/register-admin1', [AuthController::class, 'registerAdmin1'])->name('register.admin1.post');

// Proses logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Halaman dashboard RT (admin1)
Route::get('/Rt', function () {
    return view('beranda');
})->middleware('cekrole:admin1');

Route::get('/data-warga', [KertanController::class, 'tampilkanWarga'])->name('data.warga');
Route::get('/data-satpam', [KertanController::class, 'tampilkanSatpam'])->name('data.satpam');

// ====================== Tambahan Fitur Baru =======================

// Form data warga
Route::get('/form-warga', [KertanController::class, 'createWarga'])->name('form.warga');
Route::post('/simpan-warga', [KertanController::class, 'storeWarga'])->name('warga.simpan');

// Form pendaftaran akun warga
Route::get('/daftar-akun', [KertanController::class, 'createAkunWarga'])->name('akun.warga');
Route::post('/daftar-akun', [KertanController::class, 'storeAkunWarga'])->name('akun.warga.simpan');
Route::get('/akun-user', [KertanController::class, 'showWargaAccounts'])->name('akun.warga.tampilkan');
Route::put('/akun_warga/{id}', [KertanController::class, 'updateWargaAccount'])->name('akun.warga.update');
Route::delete('/akun_warga/{id}', [KertanController::class, 'destroyWargaAccount'])->name('akun.warga.destroy');


// Form data satpam
Route::get('/form-satpam', [KertanController::class, 'createSatpam'])->name('form.satpam');
Route::post('/simpan-satpam', [KertanController::class, 'storeSatpam'])->name('satpam.simpan');

// Manajemen data satpam
Route::get('/satpam/{id}/edit', [KertanController::class, 'editSatpam'])->name('satpam.edit');
Route::put('/satpam/{id}', [KertanController::class, 'updateSatpam'])->name('satpam.update');
Route::delete('/satpam/{id}', [KertanController::class, 'destroySatpam'])->name('satpam.destroy');

// Manajemen data warga
Route::get('/data-warga/{id}/edit', [KertanController::class, 'editWarga'])->name('data-warga.edit');
Route::put('/data-warga/{id}', [KertanController::class, 'updateWarga'])->name('data-warga.update');
Route::delete('/data-warga/{id}', [KertanController::class, 'destroyWarga'])->name('data-warga.destroy');

// Form pendaftaran akun satpam
Route::get('/daftar-akun-satpam', [KertanController::class, 'createAkunSatpam'])->name('akun.satpam');
Route::post('/daftar-akun-satpam', [KertanController::class, 'storeAkunSatpam'])->name('akun.satpam.simpan');
Route::get('/akun-admin2', [KertanController::class, 'tampilkanAkunAdmin2'])->name('akun.admin2');
Route::get('/admin2/{id}/edit', [KertanController::class, 'editAkunAdmin2'])->name('akun.admin2.edit'); // GET untuk form edit
Route::put('/admin2/{id}', [KertanController::class, 'updateAkunAdmin2'])->name('akun.admin2.update'); // PUT/PATCH untuk update data
Route::delete('/admin2/{id}', [KertanController::class, 'destroyAkunAdmin2'])->name('akun.admin2.destroy'); // DELETE untuk menghapus



Route::get('/form-rt', [KertanController::class, 'create'])->name('rt.create');
Route::post('/rt/simpan', [KertanController::class, 'simpan'])->name('rt.simpan');
Route::post('/rt/hapus', [KertanController::class, 'hapusDataRt'])->name('rt.hapus');


// Rute Laporan
Route::get('/tamu-laporan', [KertanController::class, 'showGuestReportsPage'])->name('laporan.tamu.index');


// ======================================================================================
// Rute Khusus untuk RT (Admin1) - Mengelola Data Pengajuan Surat Warga (Web Interface)
// ======================================================================================
Route::middleware('cekrole:admin1')->group(function () {
    Route::get('/data-pengajuan-surat', [KertanController::class, 'indexPengajuan'])->name('pengajuan.index');
    Route::get('/data-pengajuan-surat/{id}/edit', [KertanController::class, 'editPengajuan'])->name('pengajuan.edit');
    Route::put('/data-pengajuan-surat/{id}', [KertanController::class, 'updatePengajuan'])->name('pengajuan.update');
    Route::delete('/data-pengajuan-surat/{id}', [KertanController::class, 'destroyPengajuan'])->name('pengajuan.destroy');
   // ======================================================================================
    // Rute untuk Pengelolaan Pengaduan oleh Admin1 (RT) - Fokus Lihat & Edit
    // ======================================================================================
    // Menampilkan daftar semua pengaduan
    Route::get('/data-pengaduan', [KertanController::class, 'indexPengaduanAdmin'])->name('pengaduan.index_admin');
    // Menampilkan form untuk mengedit pengaduan
    Route::get('/data-pengaduan/{id}/edit', [KertanController::class, 'editPengaduanAdmin'])->name('pengaduan.edit_admin');
    // Memproses update pengaduan (misalnya, mengubah status)
    Route::put('/data-pengaduan/{id}', [KertanController::class, 'updatePengaduanAdmin'])->name('pengaduan.update_admin');
    // Menghapus pengaduan
    Route::delete('/data-pengaduan/{id}', [KertanController::class, 'destroyPengaduanAdmin'])->name('pengaduan.destroy_admin');

});