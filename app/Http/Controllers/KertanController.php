<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AkunUser;
use App\Models\Warga;
use App\Models\Tamu; // Pastikan model Tamu sudah ada
use App\Models\Satpam;
use App\Models\Rt;
use App\Models\PengajuanSurat;
use App\Models\pengaduan; //
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;


class KertanController extends Controller
{
public function beranda()
    {
        // Mendapatkan instance pengguna yang sedang login
        $user = Auth::user();

        // Data lain yang ingin kamu tampilkan di beranda
        $warga = Warga::all();
        $satpam = Satpam::select('nama', 'no_hp', 'shift')->get();

        // Mengirimkan data pengguna ke view
        return view('beranda', compact('warga', 'satpam', 'user'));
    }

    public function tampilkanWarga(Request $request)
    {
        $warga = Warga::all();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($warga);
        }

        return view('data-warga', compact('warga'));
    }

       public function tampilkanSatpam()
    {
        $satpam = Satpam::all(); // Mengambil semua data satpam

        // Ini adalah bagian PENTING yang harus ditambahkan/diubah
        // Pastikan model AkunUser sudah diimpor di bagian atas file
        $akunUsers = AkunUser::where('role', 'admin2')->get(); // Ambil semua akun user dengan role 'admin2'
                                                               // Sesuaikan 'admin2' jika role yang relevan berbeda

        // Kirimkan kedua variabel ke view
        return view('data-satpam', compact('satpam', 'akunUsers'));
    }

    public function index()
    {
        $users = AkunUser::all();
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:akun_user,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $user = AkunUser::create($validated);

        return response()->json($user);
    }

    public function show(string $id)
    {
        $user = AkunUser::find($id);
        if (!$user) return response()->json(['error' => 'User not found'], 404);
        return response()->json($user);
    }

    public function update(Request $request, string $id)
    {
        $user = AkunUser::find($id);
        if (!$user) return response()->json(['error' => 'User not found'], 404);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:akun_user,email,' . $user->id,
            'role' => 'required|in:admin1,admin2,user'
        ]);

        $user->update($validated);
        return response()->json($user);
    }

    public function destroy(string $id)
    {
        $user = AkunUser::find($id);
        if (!$user) return response()->json(['error' => 'User not found'], 404);

        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }


    // ============================
    // Fungsi untuk Warga
    // ============================

    public function createWarga()
    {
        $akunUsers = AkunUser::where('role', 'user')->get();
        return view('form-warga', compact('akunUsers'));
    }
    

    public function storeWarga(Request $request)
    {
        // Log semua data request untuk debugging
        Log::info('Masuk ke storeWarga', $request->all());

        // 1. Validasi data yang masuk dari request
        $validatedData = $request->validate([
            'nama' => 'required|string|max:100',
            'nik' => 'required|string|max:16|unique:warga,nik', // Tambahkan unique jika NIK harus unik
            'kk' => 'required|string|max:16',
            'alamat_rumah' => 'required|string',
            'no_rumah' => 'required|string|max:10',
            'no_hp' => 'required|string|max:20',
            'akun_user_id' => 'required|exists:akun_user,id', // Memastikan akun_user_id ada di tabel akun_user
            'foto_rumah' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validasi foto: gambar, tipe tertentu, maks 2MB
            'jumlah_anggota_keluarga' => 'nullable|integer|min:0', // Validasi jumlah anggota: integer, minimal 0
        ]);

        Log::info('Data validasi berhasil', $validatedData);

        $imagePath = null; // Inisialisasi path gambar

        // 2. Tangani unggahan file foto_rumah
        if ($request->hasFile('foto_rumah')) {
            try {
                // Simpan gambar ke direktori 'public/photos/warga'
                // Nama file akan di-generate otomatis oleh Laravel untuk keunikan
                $imagePath = $request->file('foto_rumah')->store('photos/warga', 'public');
                Log::info('Foto rumah berhasil diunggah', ['path' => $imagePath]);
            } catch (\Exception $e) {
                Log::error('Gagal mengunggah foto rumah: ' . $e->getMessage());
                // Tambahkan penanganan error jika unggahan gagal
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['message' => 'Gagal mengunggah foto rumah.', 'error' => $e->getMessage()], 500);
                }
                return redirect()->back()->withInput()->withErrors(['foto_rumah' => 'Gagal mengunggah foto rumah.']);
            }
        }

        // 3. Persiapkan data untuk pembuatan record Warga
        $wargaData = [
            'nama' => $validatedData['nama'],
            'nik' => $validatedData['nik'],
            'kk' => $validatedData['kk'],
            'alamat_rumah' => $validatedData['alamat_rumah'],
            'no_rumah' => $validatedData['no_rumah'],
            'no_hp' => $validatedData['no_hp'],
            'akun_user_id' => $validatedData['akun_user_id'],
            'foto_rumah' => $imagePath, // Path gambar yang disimpan (bisa null)
            'jumlah_anggota_keluarga' => $validatedData['jumlah_anggota_keluarga'] ?? null, // Ambil dari validatedData, default null jika tidak ada
        ];

        try {
            // 4. Buat record Warga baru di database
            $warga = Warga::create($wargaData);
            Log::info('Data warga berhasil disimpan', $warga->toArray());
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan data warga ke database: ' . $e->getMessage());
            // Hapus foto jika sudah terlanjur diunggah tapi data warga gagal disimpan
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
                Log::info('Foto rumah dihapus karena penyimpanan data warga gagal', ['path' => $imagePath]);
            }
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Gagal menyimpan data warga.', 'error' => $e->getMessage()], 500);
            }
            return redirect()->back()->withInput()->withErrors(['gagal' => 'Gagal menyimpan data warga. Silakan coba lagi.']);
        }

        // 5. Beri respon berdasarkan tipe request (AJAX/JSON atau standar HTTP)
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Data warga berhasil disimpan!',
                'data' => $warga,
            ]);
        }

        return redirect()->route('form.warga')->with('success', 'Data warga berhasil disimpan!');
    }
    public function createAkunWarga()
    {
        return view('akun-warga');
    }

    public function storeAkunWarga(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:akun_user,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = 'user';

        AkunUser::create($validated);

        return redirect()->route('akun.warga')->with('success', 'Akun warga berhasil didaftarkan!');
    }
       public function showWargaAccounts()
    {
        // Ambil semua user dengan role 'warga'
        $akunWarga = akunUser::where('role', 'user')->get(); // Line 221 yang error sebelumnya
        return view('daftar-akun-warga', compact('akunWarga'));
    }

    public function updateWargaAccount(Request $request, $id)
    {
        $warga = User::findOrFail($id);

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id, // Unique kecuali ID ini
        ];

        // Tambahkan validasi password hanya jika diisi
        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:6';
        }

        $validated = $request->validate($rules);

        try {
            $warga->name = $validated['name'];
            $warga->email = $validated['email'];
            if ($request->filled('password')) {
                $warga->password = Hash::make($validated['password']);
            }
            $warga->save();

            return redirect()->back()->with('success', 'Akun warga berhasil diperbarui!');
        } catch (\Exception $e) {
            Log::error('Gagal memperbarui akun warga: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui akun warga. Silakan coba lagi.');
        }
    }

    public function destroyWargaAccount($id)
    {
        try {
            $warga = akunUser::findOrFail($id);
            $warga->delete();
            return redirect()->back()->with('success', 'Akun warga berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus akun warga: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus akun warga. Silakan coba lagi.');
        }
    }

    public function createSatpam()
    {
        $akunUsers = AkunUser::where('role', 'admin2')->get();
        return view('form-satpam', compact('akunUsers'));
    }

   
    public function storeSatpam(Request $request)
    {
        Log::info('Masuk ke storeSatpam', $request->all());

        // 1. Validasi data input, termasuk foto_satpam
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'no_hp' => 'required|string|max:20',
            'shift' => 'required|in:pagi,siang,malam',
            'akun_user_id' => 'required|exists:akun_user,id',
            'foto_satpam' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Tambahkan validasi ini
        ]);

        Log::info('Data valid', $validated);

        // Inisialisasi path foto
        $fotoPath = null;

        // 2. Proses upload foto jika ada
        if ($request->hasFile('foto_satpam')) {
            $image = $request->file('foto_satpam');
            $fileName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            // Simpan foto ke direktori 'public/fotos_satpam'
            // Pastikan Anda sudah menjalankan 'php artisan storage:link'
            $fotoPath = $image->storeAs('public/fotos_satpam', $fileName);
            // Kita akan menyimpan path relatif ke database
            $fotoPath = str_replace('public/', '', $fotoPath); // Hapus 'public/' dari path
        }

        // 3. Gabungkan data yang divalidasi dengan path foto
        $dataToCreate = $validated;
        if ($fotoPath) {
            $dataToCreate['foto_satpam'] = $fotoPath;
        }

        // 4. Buat record Satpam
        $satpam = Satpam::create($dataToCreate); // Gunakan $dataToCreate

        Log::info('Data satpam berhasil disimpan', $satpam->toArray());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Data satpam berhasil disimpan!',
                'data' => $satpam,
            ]);
        }

        return redirect()->route('form.satpam')->with('success', 'Data satpam berhasil disimpan!');
    }

    public function createAkunSatpam()
    {
        return view('akun-satpam');
    }

    public function storeAkunSatpam(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:akun_user,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = 'admin2';

        AkunUser::create($validated);

        return redirect()->route('akun.satpam')->with('success', 'Akun satpam berhasil didaftarkan!');
    }

    public function tampilkanAkunAdmin2()
    {
        $akunAdmin2 = \App\Models\AkunUser::where('role', 'admin2')->get();
        return view('daftar-akun-satpam', compact('akunAdmin2'));
    }
    public function editAkunAdmin2($id) // Nama metode diubah menjadi 'editAkunAdmin2'
    {
        // Mencari akun admin2 berdasarkan ID. Jika tidak ditemukan, akan otomatis memunculkan 404.
        // Variabel $akunAdmin2 akan berisi satu objek (instance) dari model AkunUser
        // yang sesuai dengan ID yang diberikan dan memiliki peran 'admin2'.
        $akunAdmin2 = AkunUser::where('role', 'admin2')->findOrFail($id);

        // Mengembalikan view untuk form edit dengan data akun
        // Pastikan Anda memiliki file Blade 'resources/views/edit-akun-admin2.blade.php'
        return view('edit-akun-admin2', compact('akunAdmin2'));
    }

    /**
     * Memperbarui akun admin2 di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id ID akun admin2 yang akan diperbarui
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateAkunAdmin2(Request $request, $id) // Nama metode diubah menjadi 'updateAkunAdmin2'
    {
        // Mencari akun admin2 yang akan diperbarui. Jika tidak ditemukan, akan otomatis memunculkan 404.
        // Variabel $akunAdmin2 di sini adalah objek (instance) AkunUser yang akan dimodifikasi
        // dan kemudian disimpan kembali ke database.
        $akunAdmin2 = AkunUser::where('role', 'admin2')->findOrFail($id);

        // Validasi input dari form
        // Menggunakan 'name' untuk validasi agar sesuai dengan '$akunAdmin2->name = $request->name;'
        // Menggunakan 'akun_user' sebagai nama tabel yang terlihat dari gambar untuk unique rule.
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:akun_user,email,' . $id,
            'password' => 'nullable|string|min:8',
        ]);

        // Memperbarui data akun admin2
        $akunAdmin2->name = $request->name;
        $akunAdmin2->email = $request->email;
        if ($request->filled('password')) { // Memeriksa apakah bidang password diisi
            $akunAdmin2->password = bcrypt($request->password); // Enkripsi password jika diisi
        }
        $akunAdmin2->save(); // Menyimpan perubahan ke database

        // Redirect kembali ke halaman daftar akun admin2 dengan pesan sukses
        return redirect()->route('akun.admin2')->with('success', 'Akun admin2 berhasil diperbarui.');
    }

    /**
     * Menghapus akun admin2 dari database.
     *
     * @param  int  $id ID akun admin2 yang akan dihapus
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyAkunAdmin2($id) // Nama metode diubah menjadi 'destroyAkunAdmin2'
    {
        // Mencari akun admin2 yang akan dihapus. Jika tidak ditemukan, akan otomatis memunculkan 404.
        // Variabel $akunAdmin2 di sini adalah objek (instance) AkunUser yang akan dihapus
        // dari database.
        $akunAdmin2 = AkunUser::where('role', 'admin2')->findOrFail($id);
        $akunAdmin2->delete(); // Menghapus akun dari database

        // Redirect kembali ke halaman daftar akun admin2 dengan pesan sukses
        return redirect()->route('akun.admin2')->with('success', 'Akun admin2 berhasil dihapus.');
    }
    // ============================
    // Fungsi untuk RT
    // ============================
    public function create()
    {
        $rt = Rt::first();
        return view('form-rt', compact('rt'));
    }

    public function simpan(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'no_hp' => 'required|string|max:20',
            'periode_mulai' => 'required|integer|min:1900|max:9999',
            'periode_selesai' => 'required|integer|min:1900|max:9999|gte:periode_mulai',
            'foto' => 'nullable|image|mimes:jpeg,png|max:2048',
            'Maps_link' => 'nullable|url|max:2048', // Sudah ada
        ]);

        $rt = Rt::first();

        if (!$rt) {
            $rt = new Rt();
        }

        $rt->nama = $validated['nama'];
        $rt->no_hp = $validated['no_hp'];
        $rt->periode_mulai = $validated['periode_mulai'];
        $rt->periode_selesai = $validated['periode_selesai'];
        $rt->Maps_link = $validated['Maps_link'] ?? null; // Sudah ada

        if ($request->hasFile('foto')) {
            if ($rt->foto && Storage::disk('public')->exists($rt->foto)) {
                Storage::disk('public')->delete($rt->foto);
            }

            $path = $request->file('foto')->store('foto_rt', 'public');
            $rt->foto = $path;
        }

        $rt->save();

        return redirect()->route('rt.create')->with('success', 'Data RT berhasil disimpan!');
    }

    // --- Fungsi baru untuk menghapus data RT ---
    public function hapusDataRt()
    {
        $rt = Rt::first(); // Temukan data RT yang pertama (atau satu-satunya)

        if ($rt) {
            // Hapus file foto terkait jika ada di storage
            if ($rt->foto && Storage::disk('public')->exists($rt->foto)) {
                Storage::disk('public')->delete($rt->foto);
            }

            // Hapus record dari database
            $rt->delete();
            return redirect()->route('rt.create')->with('success', 'Data RT berhasil dihapus!');
        } else {
            // Jika tidak ada data RT yang ditemukan
            return redirect()->route('rt.create')->with('error', 'Tidak ada data RT untuk dihapus.');
        }
    }
     public function getRtData()
    {
        $rt = Rt::first(); // Mengambil data RT yang pertama (asumsi ada satu data utama)

        if ($rt) {
            // Jika ada foto, tambahkan URL lengkapnya agar bisa diakses dari frontend
            if ($rt->foto) {
                $rt->foto_url = asset('storage/' . $rt->foto);
            } else {
                $rt->foto_url = null; // Atau URL placeholder jika diinginkan
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Data RT berhasil diambil.',
                'data' => $rt
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Data RT tidak ditemukan.',
                'data' => null
            ], 404); // Mengembalikan kode status 404 Not Found
        }
    }
    
public function editSatpam($id)
{
    $satpam = Satpam::findOrFail($id);
    // Pastikan ini mengambil data yang benar untuk dropdown Akun User
    $akunUsers = AkunUser::where('role', 'admin2')->get(); // Atau sesuaikan query-nya
    return view('satpam.edit', compact('satpam', 'akunUsers'));
}

       public function updateSatpam(Request $request, $id)
    {
        Log::info('Masuk ke updateSatpam', $request->all());

        // 1. Validasi data input, termasuk foto_satpam dan akun_user_id
        $request->validate([
            'nama' => 'required|string|max:100', // Sesuaikan max length dengan database jika perlu
            'no_hp' => 'required|string|max:20',
            'shift' => 'required|in:pagi,siang,malam',
            'akun_user_id' => 'required|exists:akun_user,id', // Tambahkan validasi ini
            'foto_satpam' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Tambahkan validasi ini
        ]);

        $satpam = Satpam::findOrFail($id);
        Log::info('Data satpam ditemukan untuk diupdate', $satpam->toArray());

        // Inisialisasi path foto saat ini dari database
        $currentFotoPath = $satpam->foto_satpam;

        // 2. Proses upload foto baru jika ada
        if ($request->hasFile('foto_satpam')) {
            // Hapus foto lama jika ada
            if ($currentFotoPath && Storage::disk('public')->exists($currentFotoPath)) {
                Storage::disk('public')->delete($currentFotoPath);
                Log::info('Foto lama berhasil dihapus', ['path' => $currentFotoPath]);
            }

            $image = $request->file('foto_satpam');
            $fileName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $newFotoPath = $image->storeAs('public/fotos_satpam', $fileName);
            $newFotoPath = str_replace('public/', '', $newFotoPath); // Hapus 'public/' dari path

            // Perbarui path foto di model
            $satpam->foto_satpam = $newFotoPath;
            Log::info('Foto baru diunggah dan path diperbarui', ['new_path' => $newFotoPath]);
        }
        // Jika tidak ada foto baru diunggah, biarkan foto_satpam tetap null atau path yang sudah ada.
        // Tidak perlu ada else di sini, karena jika fotoPath tidak diperbarui, nilai di DB akan tetap.

        // 3. Perbarui data lainnya
        $satpam->nama = $request->nama;
        $satpam->shift = $request->shift;
        $satpam->no_hp = $request->no_hp;
        $satpam->akun_user_id = $request->akun_user_id; // Tambahkan ini
        $satpam->save();

        Log::info('Data satpam berhasil diperbarui', $satpam->toArray());

        return redirect()->route('data.satpam')->with('success', 'Data satpam berhasil diperbarui!');
    }

    public function destroySatpam($id)
    {
        $satpam = Satpam::findOrFail($id);

        // Hapus foto terkait jika ada sebelum menghapus record
        if ($satpam->foto_satpam && Storage::disk('public')->exists($satpam->foto_satpam)) {
            Storage::disk('public')->delete($satpam->foto_satpam);
            Log::info('Foto satpam berhasil dihapus saat destroy', ['path' => $satpam->foto_satpam]);
        }

        $satpam->delete();

        Log::info('Data satpam berhasil dihapus', ['id' => $id]);
        return redirect()->route('data.satpam')->with('success', 'Data satpam berhasil dihapus!');
    }
    public function editWarga($id)
    {
        // Mencari data warga berdasarkan ID.
        // Jika tidak ditemukan, Laravel akan secara otomatis menampilkan halaman 404.
        $warga = Warga::findOrFail($id);

        // Mengembalikan view 'datawarga.edit' dan mengirimkan data warga.
        // Pastikan kamu punya file view di 'resources/views/datawarga/edit.blade.php'
        return view('datawarga.edit', compact('warga'));
    }

    /**
     * Memperbarui data warga di database.
     *
     * @param \Illuminate\Http\Request $request Objek request yang berisi data formulir.
     * @param int $id ID dari data warga yang akan diperbarui.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateWarga(Request $request, $id)
    {
        // Mencari data warga berdasarkan ID.
        $warga = Warga::findOrFail($id);

        // --- Bagian Validasi Data ---
        // Kita akan melakukan validasi terhadap setiap input dari formulir
        // berdasarkan tipe data dan aturan yang kamu berikan untuk kolom tabel.
        $request->validate([
            'nama' => 'required|string|max:100',
            'nik' => 'required|string|max:16',
            'kk' => 'required|string|max:16',
            'alamat_rumah' => 'required|string', // Kolom TEXT tidak perlu max length
            'no_rumah' => 'required|string|max:10',
            'no_hp' => 'required|string|max:20',
            'foto_rumah' => 'nullable|string|max:255', // Kolom ini 'nullable' di DB, jadi bisa tidak diisi
            'jumlah_anggota_keluarga' => 'nullable|integer', // Kolom ini 'nullable' dan tipe data INT
            // 'akun_user_id' tidak perlu divalidasi di sini karena ini adalah foreign key dan tidak diupdate langsung dari formulir warga.
            // Biasanya diatur saat pembuatan data warga atau saat mengelola akun user.
        ]);

        // --- Bagian Pembaruan Data ---
        // Setelah validasi berhasil, kita perbarui data warga dengan data dari request.
        $warga->update([
            'nama' => $request->nama,
            'nik' => $request->nik,
            'kk' => $request->kk,
            'alamat_rumah' => $request->alamat_rumah,
            'no_rumah' => $request->no_rumah,
            'no_hp' => $request->no_hp,
            'foto_rumah' => $request->foto_rumah,
            'jumlah_anggota_keluarga' => $request->jumlah_anggota_keluarga,
            // created_at dan updated_at dihandle otomatis oleh Laravel
            // akun_user_id tidak diupdate di sini
        ]);

        // Setelah berhasil update, arahkan kembali ke halaman daftar warga
        // dengan pesan sukses.
        return redirect()->route('data.warga')->with('success', 'Data warga berhasil diperbarui!');
    }
      public function destroyWarga($id)
    {
        try {
            $warga = Warga::findOrFail($id); // Cari warga berdasarkan ID, jika tidak ditemukan akan 404

            // Hapus foto rumah jika ada
            if ($warga->foto_rumah) {
                Storage::delete($warga->foto_rumah);
                Log::info('Foto rumah berhasil dihapus', ['path' => $warga->foto_rumah]);
            }

            $warga->delete(); // Hapus data warga dari database

            Log::info('Data warga berhasil dihapus', ['id' => $id, 'nama' => $warga->nama]);

            // Redirect kembali ke halaman data warga dengan pesan sukses
            return redirect()->route('data.warga')->with('success', 'Data warga berhasil dihapus!');

        } catch (\Exception $e) {
            // Log error jika terjadi kegagalan
            Log::error('Gagal menghapus data warga: ' . $e->getMessage(), ['id' => $id]);

            // Redirect kembali dengan pesan error
            return redirect()->route('data.warga')->with('error', 'Gagal menghapus data warga: ' . $e->getMessage());
        }
    }
    public function getSatpamDataApi() // Metode ini akan mengembalikan profil satpam berdasarkan user yang login
    {
        // Mendapatkan ID user yang sedang login dari token Sanctum
        $loggedInUserId = Auth::user()->id;

        // Mencari data satpam yang akun_user_id-nya sama dengan ID user yang login
        // Gunakan first() karena kita hanya mengharapkan satu profil satpam per akun user
        $satpam = Satpam::where('akun_user_id', $loggedInUserId)->first();

        if (!$satpam) {
            // Jika data satpam tidak ditemukan untuk user ini, kembalikan respons 404 Not Found
            return response()->json(['message' => 'Profil satpam tidak ditemukan untuk akun ini.'], 404);
        }

        // Jika ada foto_satpam, tambahkan base URL 'storage/' agar dapat diakses dari frontend
        // Ini mengubah path relatif (misal: fotos_satpam/namafile.jpg) menjadi URL lengkap (misal: http://your-app.com/storage/fotos_satpam/namafile.jpg)
        if ($satpam->foto_satpam) {
            $satpam->foto_satpam = url('storage/' . $satpam->foto_satpam);
        }

        // Mengembalikan data satpam dalam format JSON, di dalam kunci 'data'
        return response()->json(['data' => $satpam]);
    }

    public function storeRegistrasiTamu(Request $request)
    {
        \Log::info('StoreRegistrasiTamu: Request received.');
        \Log::info('StoreRegistrasiTamu: Request has file foto_ktp: ' . ($request->hasFile('foto_ktp') ? 'true' : 'false'));
        \Log::info('StoreRegistrasiTamu: All request data: ' . json_encode($request->all()));
        \Log::info('StoreRegistrasiTamu: All request files: ' . json_encode($request->files->all()));

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:100',
            'no_identitas' => 'required|string|max:50|unique:tamu_pendatang,no_identitas',
            'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240', // max 10MB
            'alamat_asal' => 'required|string',
            'ke_rumah' => 'required|string|max:100',
            'alasan_kunjungan' => 'required|string',
        ]);

        if ($validator->fails()) {
            \Log::error('StoreRegistrasiTamu: Validation failed.', ['errors' => $validator->errors()->all()]);
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $photoPath = null;

        if ($request->hasFile('foto_ktp')) {
            $uploadedFile = $request->file('foto_ktp');

            $extension = $uploadedFile->getClientOriginalExtension();
            if (empty($extension)) {
                $extension = 'jpeg';
            }

            $fileName = uniqid('ktp_') . '.' . $extension;

            $targetDirectory = storage_path('app/public/foto_ktp');
            $fullPath = $targetDirectory . '/' . $fileName;

            if (!file_exists($targetDirectory)) {
                try {
                    mkdir($targetDirectory, 0777, true);
                    \Log::info('DEBUG: Folder ktp_photos dibuat di: ' . $targetDirectory);
                } catch (\Exception $e) {
                    \Log::error('DEBUG: Gagal membuat folder ktp_photos: ' . $e->getMessage());
                    return response()->json([
                        'message' => 'Gagal membuat folder penyimpanan foto.',
                        'error_detail' => $e->getMessage()
                    ], 500);
                }
            }

            try {
                $uploadedFile->move($targetDirectory, $fileName);
                $photoPath = 'foto_ktp/' . $fileName;
                \Log::info('Foto KTP disimpan di: ' . $photoPath . ' (metode move)');
            } catch (\Exception $e) {
                \Log::error('StoreRegistrasiTamu: Gagal menyimpan FOTO KTP ke disk (metode move): ' . $e->getMessage());
                return response()->json([
                    'message' => 'Gagal menyimpan foto KTP ke server.',
                    'error_upload' => $e->getMessage(),
                    'target_dir' => $targetDirectory,
                    'file_name' => $fileName
                ], 500);
            }
        }

        try {
            $tamu = Tamu::create([
                'nama' => $request->nama,
                'no_identitas' => $request->no_identitas,
                'foto_ktp' => $photoPath,
                'alamat_asal' => $request->alamat_asal,
                'ke_rumah' => $request->ke_rumah,
                'alasan_kunjungan' => $request->alasan_kunjungan,
                'waktu_masuk' => now(),
                'status' => 'masuk',
            ]);

            \Log::info('StoreRegistrasiTamu: Record Tamu berhasil dibuat di DB. ID: ' . $tamu->id);

            return response()->json([
                'message' => 'Registrasi tamu berhasil!',
                'data' => $tamu,
                'foto_ktp_path' => $photoPath
            ], 201);
        } catch (\Exception $e) {
            \Log::error('StoreRegistrasiTamu: Gagal menyimpan data tamu ke DB: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menyimpan data tamu.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getDataTamu(Request $request)
    {
        try {
            $tamus = Tamu::select(
                'id', 'nama', 'no_identitas', 'alamat_asal', 'ke_rumah',
                'alasan_kunjungan', 'waktu_masuk', 'waktu_keluar', 'status',
                'foto_ktp',
                'created_at', 'updated_at'
            )->get();

            $tamus->map(function ($tamu) {
                if ($tamu->foto_ktp) {
                    $tamu->foto_ktp_url = Storage::url($tamu->foto_ktp);
                } else {
                    $tamu->foto_ktp_url = null;
                }
                return $tamu;
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Daftar tamu berhasil diambil.',
                'data' => $tamus
            ], 200);

        } catch (\Exception $e) {
            Log::error('Gagal mengambil daftar tamu: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil daftar tamu.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function showDataTamu($id)
    {
        try {
            $tamu = Tamu::findOrFail($id);

            if ($tamu->foto_ktp) {
                $tamu->foto_ktp_url = Storage::url($tamu->foto_ktp);
            } else {
                $tamu->foto_ktp_url = null;
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Detail tamu berhasil diambil.',
                'data' => $tamu
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("Tamu dengan ID {$id} tidak ditemukan: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Data tamu tidak ditemukan.',
                'error' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil detail tamu: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil detail tamu.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateTamuStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Masuk,Keluar',
        ]);

        $tamu = Tamu::find($id);

        if (!$tamu) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tamu tidak ditemukan.'
            ], 404);
        }

        $tamu->status = $request->input('status');

        if ($tamu->status === 'Keluar' && is_null($tamu->waktu_keluar)) {
            $tamu->waktu_keluar = Carbon::now();
        }
        if ($tamu->status === 'Masuk') {
            $tamu->waktu_keluar = null;
        }

        $tamu->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Status tamu berhasil diperbarui.',
            'data' => $tamu
        ]);
    }

    public function deleteDataTamu($id)
    {
        try {
            $tamu = Tamu::findOrFail($id);

            if ($tamu->foto_ktp) {
                Storage::disk('public')->delete($tamu->foto_ktp);
                Log::info("Foto KTP dihapus: " . $tamu->foto_ktp);
            }

            $tamu->delete();

            Log::info("Data tamu berhasil dihapus. ID: " . $id);
            return response()->json([
                'status' => 'success',
                'message' => 'Data tamu berhasil dihapus!'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("Tamu dengan ID {$id} tidak ditemukan untuk dihapus: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Data tamu tidak ditemukan.',
                'error' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            Log::error('Gagal menghapus data tamu: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menghapus data tamu.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function tampilkandataSatpam()
    {
        $satpam = AkunUser::where('role', 'admin2')->get();
        return response()->json($satpam);
    }

    // Metode API Laporan Tamu yang sudah ada (mengembalikan JSON)
    public function getLaporanHarian(Request $request, $tanggal = null)
    {
        try {
            $targetDate = $tanggal ? Carbon::parse($tanggal) : Carbon::today();
            \Log::info('Server Time: ' . Carbon::now()->toDateTimeString());
            \Log::info('GET_LAPORAN_HARIAN_REQUEST: Target tanggal: ' . $targetDate->toDateString());
            \Log::info('GET_LAPORAN_HARIAN_CONTROLLER: Request date parameter: ' . ($tanggal ?? 'null'));

            $rekapHarian = Tamu::whereDate('waktu_masuk', $targetDate)
                ->select(
                    'id',
                    'nama as nama_tamu',
                    'no_identitas',
                    'alamat_asal',
                    'ke_rumah',
                    'alasan_kunjungan',
                    'waktu_masuk',
                    'waktu_keluar',
                    'status',
                    'foto_ktp'
                )
                ->orderBy('waktu_masuk', 'asc')
                ->get();

            \Log::info('GET_LAPORAN_HARIAN_RESULT: Jumlah data ditemukan: ' . $rekapHarian->count());
            \Log::info('GET_LAPORAN_HARIAN_DATA: Data JSON: ' . $rekapHarian->toJson());

            $rekapHarian->map(function ($tamu) {
                if ($tamu->foto_ktp) {
                    $tamu->foto_ktp_url = Storage::url($tamu->foto_ktp);
                } else {
                    $tamu->foto_ktp_url = null;
                }
                return $tamu;
            });

            if ($rekapHarian->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Tidak ada data tamu untuk tanggal ' . $targetDate->format('d M Y'),
                    'data' => []
                ], 200);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Rekap harian tamu berhasil diambil untuk tanggal ' . $targetDate->format('d M Y'),
                'data' => $rekapHarian,
                'total_tamu' => $rekapHarian->count()
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Gagal mengambil rekap harian tamu: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil rekap harian tamu.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getRekapMingguanTamu(Request $request)
    {
        try {
            $endDate = Carbon::now()->endOfDay();
            $startDate = Carbon::now()->subWeeks(8)->startOfWeek(Carbon::MONDAY);

            if ($request->query('start_date')) {
                $startDate = Carbon::parse($request->query('start_date'))->startOfDay();
            }
            if ($request->query('end_date')) {
                $endDate = Carbon::parse($request->query('end_date'))->endOfDay();
            }

            \Log::info('Rekap Mingguan Request: Start Date = ' . $startDate->toDateString() . ', End Date = ' . $endDate->toDateString());

            $rekapMingguan = Tamu::selectRaw('
                YEARWEEK(waktu_masuk, 1) as year_week_combined,
                WEEK(waktu_masuk, 1) as minggu_ke,
                COUNT(*) as total_tamu_mingguan
            ')
            ->whereBetween('waktu_masuk', [$startDate, $endDate])
            ->groupBy('year_week_combined', 'minggu_ke')
            ->orderBy('year_week_combined', 'asc')
            ->get();

            \Log::info('Rekap Mingguan Query Result Count: ' . $rekapMingguan->count());
            \Log::info('Rekap Mingguan Raw Query Result: ' . $rekapMingguan->toJson());

            $formattedRekap = $rekapMingguan->map(function ($item) {
                return [
                    'minggu_ke' => (int)$item->minggu_ke,
                    'total_tamu_mingguan' => (int)$item->total_tamu_mingguan,
                ];
            });

            \Log::info('Rekap Mingguan Formatted Data: ' . json_encode($formattedRekap));

            if ($formattedRekap->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Tidak ada data tamu untuk rentang mingguan yang diminta.',
                    'data' => []
                ], 200);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Rekap mingguan tamu berhasil diambil.',
                'data' => $formattedRekap
            ], 200);

        } catch (\Exception $e) {
            Log::error('Gagal mengambil rekap mingguan tamu: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil rekap mingguan tamu.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getRekapBulananTamu(Request $request, $year = null)
    {
        try {
            $targetYear = $year ?? Carbon::now()->year;

            Log::info("GET_REKAP_BULANAN_API: Menerima permintaan untuk tahun: " . $targetYear);

            $rekapBulanan = Tamu::query()
                ->select(
                    DB::raw('MONTH(waktu_masuk) as bulan_angka'),
                    DB::raw('COUNT(*) as total_tamu_masuk')
                )
                ->whereYear('waktu_masuk', $targetYear)
                ->groupBy(DB::raw('YEAR(waktu_masuk)'), DB::raw('MONTH(waktu_masuk)'))
                ->orderBy(DB::raw('YEAR(waktu_masuk)'), 'asc')
                ->orderBy(DB::raw('MONTH(waktu_masuk)'), 'asc')
                ->get();

            Log::info("GET_REKAP_BULANAN_API: Query result count: " . $rekapBulanan->count());
            Log::info("GET_REKAP_BULANAN_API: Raw query result: " . $rekapBulanan->toJson());

            $formattedRekap = $rekapBulanan->map(function ($item) {
                $date = Carbon::create(null, $item->bulan_angka, 1);
                $monthName = $date->translatedFormat('F');

                return [
                    'bulan_angka' => (int)$item->bulan_angka,
                    'bulan_nama' => $monthName,
                    'total_tamu_masuk' => (int)$item->total_tamu_masuk,
                ];
            });

            Log::info("GET_REKAP_BULANAN_API: Formatted data: " . json_encode($formattedRekap));

            if ($formattedRekap->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Tidak ada data tamu untuk tahun ' . $targetYear . '.',
                    'data' => []
                ], 200);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Rekap bulanan tamu berhasil diambil untuk tahun ' . $targetYear . '.',
                'data' => $formattedRekap
            ], 200);

        } catch (\Exception $e) {
            Log::error('Gagal mengambil rekap bulanan tamu: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil rekap bulanan tamu.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getLaporanTahunan(Request $request, $tahun = null)
    {
        try {
            $targetYear = $tahun ?? Carbon::now()->year;

            $rekapTahunan = Tamu::selectRaw('
                YEAR(waktu_masuk) as year,
                COUNT(*) as total_tamu_masuk
            ')
            ->whereYear('waktu_masuk', $targetYear)
            ->groupBy('year')
            ->orderBy('year', 'asc')
            ->get();

            if ($rekapTahunan->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Tidak ada data tamu untuk tahun ' . $targetYear . '.',
                    'data' => []
                ], 200);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Rekap tahunan tamu berhasil diambil untuk tahun ' . $targetYear . '.',
                'data' => $rekapTahunan
            ], 200);

        } catch (\Exception $e) {
            Log::error('Gagal mengambil rekap tahunan tamu: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil rekap tahunan tamu.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // ==============================================================
    // FUNGSI BARU UNTUK HALAMAN LAPORAN TAMU (Full Page Reload)
    // ==============================================================

    /**
     * Menampilkan halaman utama laporan tamu dengan semua data yang sudah difilter.
     * Menggunakan parameter dari query string.
     */
    public function showGuestReportsPage(Request $request)
    {
        // ----------------------------------------------------
        // 1. Ambil Parameter Filter dari Request
        // ----------------------------------------------------
        // 'daily_date' untuk filter harian
        $selectedDailyDate = $request->input('daily_date', Carbon::today()->toDateString());

        // 'monthly_year' dan 'monthly_month' untuk filter bulanan
        $selectedMonthlyYear = $request->input('monthly_year', Carbon::now()->year);
        $selectedMonthlyMonth = $request->input('monthly_month', Carbon::now()->month);

        // 'yearly_year' untuk filter tahunan
        $selectedYearlyYear = $request->input('yearly_year', Carbon::now()->year);


        // ----------------------------------------------------
        // 2. Ambil Data Laporan Berdasarkan Filter
        //    (Menggunakan metode helper private)
        // ----------------------------------------------------

        // Laporan Harian
        $laporanHarian = $this->getDailyReportData($selectedDailyDate);

        // Rekap Mingguan (tidak ada filter langsung dari user di halaman ini,
        // jadi ambil data umum, misalnya 8 minggu terakhir)
        $rekapMingguan = $this->getWeeklyReportDataForPage(); // Perhatikan nama metode, berbeda dari API

        // Rekap Bulanan
        $rekapBulanan = $this->getMonthlyReportDataForPage($selectedMonthlyYear, $selectedMonthlyMonth); // Perhatikan nama metode

        // Rekap Tahunan
        $rekapTahunan = $this->getYearlyReportDataForPage($selectedYearlyYear); // Perhatikan nama metode


        // ----------------------------------------------------
        // 3. Hitung Total untuk Footer Tabel (jika diperlukan)
        // ----------------------------------------------------
        $totalTamuMingguan = collect($rekapMingguan['data'])->sum('total_tamu_mingguan');
        $totalTamuBulanan = collect($rekapBulanan['data'])->sum('total_tamu_masuk');
        $totalTamuTahunan = collect($rekapTahunan['data'])->sum('total_tamu_masuk');


        // ----------------------------------------------------
        // 4. Kirim Data ke View Blade
        // ----------------------------------------------------
        return view('tamu-laporan', compact(
            'laporanHarian',
            'rekapMingguan',
            'rekapBulanan',
            'rekapTahunan',
            'totalTamuMingguan',
            'totalTamuBulanan',
            'totalTamuTahunan',
            'selectedDailyDate',    // Penting: Kirim kembali nilai filter agar input di view tetap terisi
            'selectedMonthlyYear',
            'selectedMonthlyMonth',
            'selectedYearlyYear'
        ));
    }

    /**
     * Helper Method (untuk halaman): Mengambil laporan tamu harian.
     * Mengembalikan array data untuk view.
     */
    private function getDailyReportData($tanggal)
    {
        $date = Carbon::parse($tanggal)->toDateString();

        $data = Tamu::whereDate('waktu_masuk', $date)
                    ->orderBy('waktu_masuk', 'asc')
                    ->get();

        $totalTamu = $data->count();

        return [
            'data' => $data,
            'total_tamu' => $totalTamu
        ];
    }

    /**
     * Helper Method (untuk halaman): Mengambil rekap tamu mingguan.
     * Mengembalikan array data untuk view.
     */
    private function getWeeklyReportDataForPage()
    {
        // Anda bisa menyesuaikan rentang minggu di sini
        $endDate = Carbon::now();
        $startDate = $endDate->copy()->subWeeks(8)->startOfWeek(Carbon::MONDAY); // Contoh: 9 minggu terakhir termasuk minggu ini

        $rekap = Tamu::selectRaw('
            YEARWEEK(waktu_masuk, 1) as year_week_combined,
            WEEK(waktu_masuk, 1) as minggu_ke,
            COUNT(*) as total_tamu_mingguan
        ')
        ->whereBetween('waktu_masuk', [$startDate, $endDate])
        ->groupBy('year_week_combined', 'minggu_ke')
        ->orderBy('year_week_combined', 'asc')
        ->get();

        // Tambahkan nama bulan dan tahun untuk minggu agar lebih informatif (opsional)
        $rekap->map(function ($item) {
            $year = substr($item->year_week_combined, 0, 4);
            $weekNum = substr($item->year_week_combined, 4, 2);
            // Ini adalah pendekatan perkiraan untuk mendapatkan tanggal awal minggu
            // Lebih akurat jika menyimpan tanggal mulai setiap minggu di DB atau mengandalkan Carbon yang lebih kompleks
            $startOfWeek = Carbon::now()->setISODate($year, $weekNum)->startOfWeek(Carbon::MONDAY);
            $endOfWeek = $startOfWeek->copy()->endOfWeek(Carbon::SUNDAY);
            $item->periode_minggu = 'Minggu ke-' . $item->minggu_ke . ' (' . $startOfWeek->format('d M') . ' - ' . $endOfWeek->format('d M Y') . ')';
            return $item;
        });

        return [
            'data' => $rekap
        ];
    }

    /**
     * Helper Method (untuk halaman): Mengambil rekap tamu bulanan untuk tahun dan bulan tertentu.
     * Mengembalikan array data untuk view.
     */
    private function getMonthlyReportDataForPage($year, $month)
    {
        $query = Tamu::selectRaw('MONTH(waktu_masuk) as bulan_num, COUNT(*) as total_tamu_masuk')
                      ->whereYear('waktu_masuk', $year);

        if ($month && $month != 'all') { // Izinkan 'all' untuk melihat semua bulan
            $query->whereMonth('waktu_masuk', $month);
        }

        $rekap = $query->groupBy('bulan_num')
                       ->orderBy('bulan_num', 'asc')
                       ->get();

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        foreach ($rekap as $item) {
            $item->bulan_nama = $months[$item->bulan_num] ?? 'Tidak Dikenal';
        }

        $totalTamuBulanan = $rekap->sum('total_tamu_masuk');

        return [
            'data' => $rekap,
            'total_tamu_bulanan' => $totalTamuBulanan
        ];
    }

    /**
     * Helper Method (untuk halaman): Mengambil laporan tamu tahunan.
     * Mengembalikan array data untuk view.
     */
    private function getYearlyReportDataForPage($tahun)
    {
        $year = $tahun;

        $rekap = Tamu::selectRaw('YEAR(waktu_masuk) as tahun, COUNT(*) as total_tamu_masuk')
                        ->whereYear('waktu_masuk', $year)
                        ->groupBy('tahun')
                        ->orderBy('tahun', 'asc')
                        ->get();

        $totalTamuTahunan = $rekap->sum('total_tamu_masuk');

        return [
            'data' => $rekap,
            'total_tamu_tahunan' => $totalTamuTahunan
        ];
    }
    
    

  /**
     * Get the authenticated user details.
     * This method is typically called for /api/user endpoint.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request)
    {
        // Mengembalikan data user yang sedang login
        // Pastikan properti 'name', 'email', 'role' ada di objek user Anda.
        // Jika ada properti lain seperti 'nik' atau 'address' di tabel user
        // dan Anda ingin mengirimkannya, sertakan di sini.
        // Karena kita sepakat frontend tidak membutuhkan nik/address,
        // maka pastikan hanya properti yang dibutuhkan yang dikirim.

        $user = $request->user(); // Ini akan mendapatkan user yang terautentikasi

        if ($user) {
            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    // Jika Anda memiliki kolom 'nik' atau 'address' di tabel 'users'
                    // dan ingin mengirimkannya (meskipun tidak dipakai di frontend), Anda bisa menambahkannya:
                    // 'nik' => $user->nik,
                    // 'address' => $user->address,
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'User not authenticated or not found.'
        ], 401); // Unauthorized
    }


      public function storePengajuanApiWarga(Request $request)
    {
        try {
            $user = Auth::user(); // Mendapatkan user yang sedang login via Sanctum
            // Perbaikan: Lebih aman memeriksa kedua kondisi ($user dan $user->warga)
            if (!$user || !($warga = $user->warga)) {
                return response()->json(['message' => 'Unauthorized atau profil warga tidak ditemukan.'], 401);
            }

            // 1. Validasi Input (termasuk file 'attachment')
            $request->validate([
                'jenis_surat' => 'required|string|in:nikah,domisili,ktp,kk,lainnya',
                'keterangan' => 'nullable|string',
                'attachment' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // Validasi file: wajib, file, tipe, max 5MB
            ], [
                'attachment.required' => 'Lampiran wajib diunggah.',
                'attachment.file' => 'Input attachment harus berupa file.',
                'attachment.mimes' => 'Format file yang didukung: PDF, JPG, JPEG, PNG.',
                'attachment.max' => 'Ukuran file maksimal 5MB.',
            ]);

            $fotoSuratPath = null; // Inisialisasi variabel untuk jalur foto

            // 2. Handle File Upload
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $extension = $file->getClientOriginalExtension();
                $fileName = 'surat_' . time() . '_' . uniqid() . '.' . $extension;
                
                // Pastikan uploadPath sesuai dengan folder yang Anda buat di public
                $uploadPath = 'surat'; // Sesuai dengan image_81ae8c.png

                // Pastikan direktori ada sebelum menyimpan (opsional jika sudah dibuat manual, tapi aman jika ada)
                if (!Storage::disk('public')->exists($uploadPath)) {
                    Storage::disk('public')->makeDirectory($uploadPath);
                    Log::info("Directory '$uploadPath' created by storePengajuanApiWarga.");
                }

                // Simpan file ke storage
                $fotoSuratPath = Storage::disk('public')->putFileAs($uploadPath, $file, $fileName);
                
                // Jika putFileAs gagal, $fotoSuratPath bisa jadi null atau false
                if (!$fotoSuratPath) {
                    Log::error('Failed to upload file for pengajuan surat.');
                    return response()->json(['message' => 'Gagal mengunggah file lampiran.'], 500);
                }

            } else {
                // Ini seharusnya tidak terpanggil jika 'attachment' adalah required, tapi sebagai fallback
                Log::warning('No attachment file found in storePengajuanApiWarga, despite validation.');
                return response()->json(['message' => 'Lampiran file tidak ditemukan.'], 400);
            }

            // 3. Simpan Data Pengajuan ke Database
            $pengajuan = PengajuanSurat::create([
                'warga_id' => $warga->id,
                'jenis_surat' => $request->jenis_surat,
                'keterangan' => $request->keterangan,
                'status' => 'menunggu', // Status awal
                'foto_surat_path' => $fotoSuratPath, // Ini yang akan menyimpan jalur file ke DB
            ]);

            return response()->json([
                'message' => 'Pengajuan surat berhasil diajukan.',
                'data' => $pengajuan // Model akan otomatis menambahkan attachment_url karena accessor yang sudah diatur
            ], 201); // 201 Created

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in storePengajuanApiWarga: ' . $e->getMessage(), ['errors' => $e->errors()]);
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422); // Unprocessable Entity
        } catch (\Exception $e) {
            Log::error('Error in storePengajuanApiWarga for user ID ' . ($user->id ?? 'N/A') . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);
            // Jika terjadi error saat menyimpan ke DB setelah file diupload, coba hapus file yang diupload.
            if ($fotoSuratPath && Storage::disk('public')->exists($fotoSuratPath)) {
                Storage::disk('public')->delete($fotoSuratPath);
                Log::info("Uploaded file '$fotoSuratPath' deleted due to DB error.");
            }
            return response()->json(['message' => 'Terjadi kesalahan server saat mengajukan surat. Silakan coba lagi.'], 500);
        }
    }

    // =====================================================================
    // FUNGSI UNTUK API WARGA (MOBILE APP - IONIC) - Sesuai dengan bagian yang Anda berikan
    // =====================================================================

    /**
     * Melihat daftar pengajuan surat yang dibuat oleh warga yang sedang login via API.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexMyPengajuansApiWarga(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user || !($warga = $user->warga)) {
                return response()->json(['message' => 'Unauthorized atau profil warga tidak ditemukan.'], 401);
            }

            $warga_id = $warga->id;

            $pengajuans = PengajuanSurat::where('warga_id', $warga_id)
                                        ->latest()
                                        ->get(); // Saat get(), accessor akan bekerja untuk setiap model di koleksi

            return response()->json([
                'message' => 'Daftar pengajuan surat Anda.',
                'data' => $pengajuans // $pengajuans sekarang akan menyertakan 'attachment_url'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error fetching pengajuans for user ID ' . (Auth::id() ?? 'N/A') . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);
            return response()->json(['message' => 'Terjadi kesalahan server saat memuat riwayat pengajuan.'], 500);
        }
    }

    /**
     * Melihat detail pengajuan surat spesifik yang dibuat oleh warga yang sedang login via API.
     * @param Request $request
     * @param int $id ID pengajuan surat
     * @return \Illuminate\Http\JsonResponse
     */
    public function showMyPengajuanApiWarga(Request $request, $id)
    {
        try {
            $user = Auth::user();
            if (!$user || !($warga = $user->warga)) {
                return response()->json(['message' => 'Unauthorized atau profil warga tidak ditemukan.'], 401);
            }

            $warga_id = $warga->id;

            $pengajuan = PengajuanSurat::where('id', $id)
                                        ->where('warga_id', $warga_id)
                                        ->first(); // Saat first(), accessor juga akan bekerja

            if (!$pengajuan) {
                return response()->json(['message' => 'Pengajuan tidak ditemukan atau bukan milik Anda.'], 404);
            }

            return response()->json([
                'message' => 'Detail pengajuan surat.',
                'data' => $pengajuan // $pengajuan sekarang akan menyertakan 'attachment_url'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error fetching single pengajuan ID ' . $id . ' for user ID ' . (Auth::id() ?? 'N/A') . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);
            return response()->json(['message' => 'Terjadi kesalahan server saat memuat detail pengajuan.'], 500);
        }
    }

    // ====================================================================
    // CONTROLLER UNTUK RT (role: admin1) VIA WEB (Untuk Laravel Blade) - Sesuai dengan bagian yang Anda berikan
    // ====================================================================

    /**
     * Menampilkan daftar semua pengajuan surat untuk RT (web interface).
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function indexPengajuan(Request $request)
    {
        // Perbaikan di sini: Menghapus `.akunUser` karena relasi PengajuanSurat adalah ke Warga
        // `PengajuanSurat::with('warga')->latest()->get();` sudah benar dan akan memuat data warga
        $pengajuans = PengajuanSurat::with('warga')->latest()->get();

        // Perbaikan: Pastikan nama view ('pengajuan-surat') sesuai dengan file blade Anda.
        // Jika file Anda `data-pengajuan-surat.blade.php`, maka di sini harus `data-pengajuan-surat`.
        return view('pengajuan-surat', compact('pengajuans'));
    }

    // Fungsi editPengajuan ini bisa dipertahankan untuk debug atau dihapus jika tidak lagi digunakan
    // karena Anda sekarang mengedit via modal di halaman yang sama.
    public function editPengajuan(Request $request, $id)
    {
        $pengajuan = PengajuanSurat::with('warga')->findOrFail($id);
        return view('pengajuan.edit', compact('pengajuan'));
    }

    /**
     * Memperbarui status dan keterangan pengajuan surat oleh RT (web interface).
     * @param Request $request
     * @param int $id ID pengajuan surat
     * @return \Illuminate\Http\JsonResponse // UBAH TIPE KEMBALIAN KE JsonResponse - Sesuai permintaan Anda
     */
    
    public function updatePengajuan(Request $request, $id)
    {
        try {
            $pengajuan = PengajuanSurat::findOrFail($id);

            $validatedData = $request->validate([
                'status' => 'required|string|in:menunggu,disetujui,ditolak',
                'keterangan' => 'nullable|string',
                'file_lampiran_baru' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf|max:2048', // Tambahkan validasi untuk file baru
            ]);

            $pengajuan->status = $validatedData['status'];
            $pengajuan->keterangan = $validatedData['keterangan'];

            // Logika untuk mengelola file lampiran baru
            if ($request->hasFile('file_lampiran_baru')) {
                // Hapus file lama jika ada
                if ($pengajuan->foto_surat_path && Storage::disk('public')->exists($pengajuan->foto_surat_path)) {
                    Storage::disk('public')->delete($pengajuan->foto_surat_path);
                }

                // Simpan file baru
                $path = $request->file('file_lampiran_baru')->store('surat', 'public');
                $pengajuan->foto_surat_path = $path; // Simpan path baru ke kolom database
            }

            $pengajuan->save();

            return response()->json(['success' => true, 'message' => 'Pengajuan berhasil diperbarui.']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error updating pengajuan (ID: ' . $id . '): ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all(),
            ]);
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui pengajuan: ' . $e->getMessage()], 500);
        }
    }
    public function destroyPengajuan(Request $request, $id)
    {
        try { // Perbaikan: Tambahkan try-catch untuk destroy
            $pengajuan = PengajuanSurat::findOrFail($id);
            $pengajuan->delete();

            // Ini tetap redirect karena form hapus tidak menggunakan AJAX di HTML yang Anda berikan
            return redirect()->route('pengajuan.index')->with('success', 'Pengajuan berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error deleting pengajuan (ID: ' . $id . '): ' . $e->getMessage(), [
                'exception' => $e,
            ]);
            return redirect()->route('pengajuan.index')->with('error', 'Gagal menghapus pengajuan. Terjadi kesalahan server.');
        }
    }


    public function storePengaduan(Request $request)
    {
        $fotoLaporanPath = null; // Inisialisasi variabel untuk jalur foto

        try {
            $user = Auth::user(); // Get the authenticated user via Sanctum
            // Check if user is authenticated and has a linked 'warga' profile
            if (!$user || !($warga = $user->warga)) {
                return response()->json(['message' => 'Tidak terautentikasi atau profil warga tidak ditemukan.'], 401);
            }

            // 1. Validasi input untuk pengaduan, termasuk foto_laporan
            $request->validate([
                'kategori' => 'required|string|max:255',
                'isi_pengaduan' => 'required|string',
                'foto_laporan' => 'nullable|image|mimes:jpg,jpeg,png|max:5120', // Validasi file foto: opsional, gambar, tipe, max 5MB
            ], [
                'kategori.required' => 'Kategori pengaduan wajib diisi.',
                'isi_pengaduan.required' => 'Isi pengaduan wajib diisi.',
                'foto_laporan.image' => 'Lampiran harus berupa gambar.',
                'foto_laporan.mimes' => 'Format gambar yang didukung: JPG, JPEG, PNG.',
                'foto_laporan.max' => 'Ukuran gambar maksimal 5MB.',
            ]);

            // 2. Handle File Upload untuk foto_laporan
            if ($request->hasFile('foto_laporan')) {
                $file = $request->file('foto_laporan');
                $extension = $file->getClientOriginalExtension();
                $fileName = 'pengaduan_' . time() . '_' . uniqid() . '.' . $extension;

                // Tentukan folder penyimpanan, contoh: 'pengaduan_laporan'
                $uploadPath = 'pengaduan_laporan';

                // Pastikan direktori ada sebelum menyimpan
                if (!Storage::disk('public')->exists($uploadPath)) {
                    Storage::disk('public')->makeDirectory($uploadPath);
                    Log::info("Directory '$uploadPath' created for pengaduan_laporan.");
                }

                // Simpan file ke storage
                $fotoLaporanPath = Storage::disk('public')->putFileAs($uploadPath, $file, $fileName);

                // Jika putFileAs gagal
                if (!$fotoLaporanPath) {
                    Log::error('Failed to upload file for pengaduan photo.');
                    return response()->json(['message' => 'Gagal mengunggah foto laporan.'], 500);
                }
            }

            // 3. Create new Pengaduan in the database
            $pengaduan = Pengaduan::create([
                'warga_id' => $warga->id, // Get warga_id from the authenticated user
                'kategori' => $request->kategori,
                'isi_pengaduan' => $request->isi_pengaduan,
                'foto_laporan_path' => $fotoLaporanPath, // Simpan jalur file (bisa null jika tidak ada foto)
                'status' => 'menunggu', // Initial status for new complaints
            ]);

            return response()->json([
                'message' => 'Pengaduan berhasil diajukan.',
                'data' => $pengaduan // Model akan otomatis menambahkan foto_laporan_url karena accessor
            ], 201); // 201 Created

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in storePengaduan: ' . $e->getMessage(), ['errors' => $e->errors()]);
            // Jika validasi gagal, hapus foto yang mungkin sudah terupload
            if ($fotoLaporanPath && Storage::disk('public')->exists($fotoLaporanPath)) {
                Storage::disk('public')->delete($fotoLaporanPath);
                Log::info("Uploaded photo '$fotoLaporanPath' deleted due to validation error.");
            }
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422); // Unprocessable Entity
        } catch (\Exception $e) {
            Log::error('Error in storePengaduan for user ID ' . ($user->id ?? 'N/A') . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);
            // Jika terjadi error saat menyimpan ke DB setelah file diupload, coba hapus file yang diupload.
            if ($fotoLaporanPath && Storage::disk('public')->exists($fotoLaporanPath)) {
                Storage::disk('public')->delete($fotoLaporanPath);
                Log::info("Uploaded photo '$fotoLaporanPath' deleted due to DB error.");
            }
            return response()->json(['message' => 'Terjadi kesalahan server saat mengajukan pengaduan. Silakan coba lagi.'], 500);
        }
    }

    /**
     * Get a list of complaints submitted by the currently logged-in resident via API.
     * Route: GET /warga/pengaduan-saya
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexMyPengaduan(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user || !($warga = $user->warga)) {
                return response()->json(['message' => 'Tidak terautentikasi atau profil warga tidak ditemukan.'], 401);
            }

            $warga_id = $warga->id;

            // Fetch complaints associated with the logged-in resident
            // foto_laporan_url akan otomatis ditambahkan oleh accessor di model Pengaduan
            $pengaduans = Pengaduan::where('warga_id', $warga_id)
                                   ->latest() // Order by latest complaints first
                                   ->get();

            return response()->json([
                'message' => 'Daftar pengaduan Anda.',
                'data' => $pengaduans
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error fetching complaints for user ID ' . (Auth::id() ?? 'N/A') . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);
            return response()->json(['message' => 'Terjadi kesalahan server saat memuat riwayat pengaduan.'], 500);
        }
    }

    /**
     * Get details of a specific complaint submitted by the currently logged-in resident via API.
     * Route: GET /warga/pengaduan-saya/{id}
     * @param Request $request
     * @param int $id The ID of the complaint
     * @return \Illuminate\Http\JsonResponse
     */
    public function showMyPengaduan(Request $request, $id)
    {
        try {
            $user = Auth::user();
            if (!$user || !($warga = $user->warga)) {
                return response()->json(['message' => 'Tidak terautentikasi atau profil warga tidak ditemukan.'], 401);
            }

            $warga_id = $warga->id;

            // Find the complaint by ID and ensure it belongs to the authenticated resident
            // foto_laporan_url akan otomatis ditambahkan oleh accessor di model Pengaduan
            $pengaduan = Pengaduan::where('id', $id)
                                  ->where('warga_id', $warga_id)
                                  ->first();

            if (!$pengaduan) {
                return response()->json(['message' => 'Pengaduan tidak ditemukan atau bukan milik Anda.'], 404);
            }

            return response()->json([
                'message' => 'Detail pengaduan.',
                'data' => $pengaduan
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error fetching single complaint ID ' . $id . ' for user ID ' . (Auth::id() ?? 'N/A') . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);
            return response()->json(['message' => 'Terjadi kesalahan server saat memuat detail pengaduan.'], 500);
        }
    }
   
    // ====================================================================
    // CONTROLLER UNTUK PENGADUAN (role: admin1/RT) - Sesuai diskusi sebelumnya
    // ====================================================================
    
    public function indexPengaduanAdmin(Request $request)
    {
        $pengaduans = Pengaduan::latest()->get();
        // Mengubah path view agar langsung merujuk ke pengaduan.blade.php
        return view('pengaduan', compact('pengaduans')); //
    }

    /**
     * Menampilkan form untuk mengedit/mengelola pengaduan oleh RT (web interface).
     * Dipanggil oleh route('pengaduan.edit_admin').
     * @param int $id ID pengaduan
     * @return \Illuminate\View\View
     */
    public function editPengaduanAdmin($id)
    {
        $pengaduan = Pengaduan::findOrFail($id);
        // Mengubah path view agar langsung merujuk ke pengaduan-edit.blade.php (asumsi nama file untuk edit)
        return view('pengaduan-edit', compact('pengaduan')); // Asumsi nama view edit pengaduan adalah 'pengaduan-edit'
    }

    /**
     * Memperbarui status dan tanggapan pengaduan oleh RT (web interface).
     * @param Request $request
     * @param int $id ID pengaduan
     * @return \Illuminate\Http\RedirectResponse
     */
   public function updatePengaduanAdmin(Request $request, $id)
    {
        try {
            // Temukan pengaduan berdasarkan ID
            $pengaduan = Pengaduan::findOrFail($id);

            // Validasi data yang masuk
            $request->validate([
                'isi_pengaduan' => 'required|string|max:1000',
                'status' => 'required|in:menunggu,diproses,selesai,ditolak',
                // Jika Anda sebelumnya memiliki input file 'foto_laporan',
                // dan sekarang ingin menghapusnya dari modal, Anda juga harus
                // menghapusnya dari aturan validasi di sini.
                // 'foto_laporan' => 'nullable|image|max:2048', // Hapus baris ini jika tidak ada upload foto baru
            ]);

            // Update isi pengaduan dan status
            $pengaduan->isi_pengaduan = $request->isi_pengaduan;
            $pengaduan->status = $request->status;

            // Jika Anda masih memiliki logika untuk upload foto baru, pertahankan di sini.
            // Jika tidak, hapus bagian ini:
            /*
            if ($request->hasFile('foto_laporan')) {
                // Hapus foto lama jika ada
                if ($pengaduan->foto_laporan_path && Storage::exists($pengaduan->foto_laporan_path)) {
                    Storage::delete($pengaduan->foto_laporan_path);
                }
                $path = $request->file('foto_laporan')->store('pengaduan_photos', 'public');
                $pengaduan->foto_laporan_path = $path;
            }
            */

            $pengaduan->save();

            // Mengembalikan respons JSON untuk AJAX
            return response()->json(['success' => true, 'message' => 'Pengaduan berhasil diperbarui.']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Tangani error validasi
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Tangani error umum lainnya
            \Log::error("Error updating pengaduan by admin: " . $e->getMessage(), ['exception' => $e, 'id' => $id]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan server: ' . $e->getMessage()], 500);
        }
    }

    // ... method-method lain di KertanController Anda ...

    /**
     * Menghapus pengaduan.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function destroyPengaduanAdmin($id)
    {
        try {
            $pengaduan = Pengaduan::findOrFail($id);

            // Hapus file foto terkait jika ada
            if ($pengaduan->foto_laporan_path && Storage::exists($pengaduan->foto_laporan_path)) {
                Storage::delete($pengaduan->foto_laporan_path);
            }

            $pengaduan->delete();

            // Jika ini adalah permintaan AJAX, kembalikan JSON
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Pengaduan berhasil dihapus.']);
            }

            // Jika ini adalah permintaan form biasa, redirect dengan pesan sukses
            return redirect()->route('pengaduan.index_admin')->with('success', 'Pengaduan berhasil dihapus.');

        } catch (\Exception $e) {
            \Log::error("Error deleting pengaduan by admin: " . $e->getMessage(), ['exception' => $e, 'id' => $id]);

            // Jika AJAX, kembalikan JSON error
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal menghapus pengaduan: ' . $e->getMessage()], 500);
            }

            // Jika form biasa, redirect dengan pesan error
            return redirect()->back()->with('error', 'Gagal menghapus pengaduan: ' . $e->getMessage());
        }
    }

       public function showprofilwarga(Request $request)
    {
        // Mendapatkan pengguna yang sedang terautentikasi.
        $user = Auth::user();

        // Jika tidak ada user yang terautentikasi
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Mencari data warga berdasarkan akun_user_id dari pengguna yang terautentikasi.
        $warga = Warga::where('akun_user_id', $user->id)->first();

        // Jika warga tidak ditemukan, kembalikan respons error.
        if (!$warga) {
            return response()->json(['message' => 'Profil warga tidak ditemukan.'], 404);
        }

        $fotoRumahUrl = null;
        if ($warga->foto_rumah) {
            // Kita akan selalu mencoba mengonversi path yang disimpan ke URL publik.
            // Pastikan nilai $warga->foto_rumah yang tersimpan di DB adalah path internal
            // seperti 'public/photos/warga/namafile.png'.
            // Menggunakan Storage::disk('public')->url() untuk eksplisit.
            $fotoRumahUrl = Storage::disk('public')->url($warga->foto_rumah);
        }

        // --- DEBUGGING SEMENTARA ---
        // Ini akan menghentikan eksekusi dan menampilkan nilai $fotoRumahUrl
        // Pastikan Anda menghapus baris ini setelah Anda melihat URL yang benar di browser Anda.
        // dd($fotoRumahUrl);
        // --- AKHIR DEBUGGING ---

        return response()->json([
            'message' => 'Data profil warga berhasil diambil.',
            'data' => [
                'id' => $warga->id,
                'nama' => $warga->nama,
                'nik' => $warga->nik,
                'kk' => $warga->kk,
                'alamat_rumah' => $warga->alamat_rumah,
                'no_rumah' => $warga->no_rumah,
                'no_hp' => $warga->no_hp,
                'akun_user_id' => $warga->akun_user_id,
                'foto_rumah' => $fotoRumahUrl, // Menggunakan URL yang sudah dikonstruksi
                'jumlah_anggota_keluarga' => $warga->jumlah_anggota_keluarga,
                'created_at' => $warga->created_at,
                'updated_at' => $warga->updated_at,
            ]
        ], 200);
    }

    public function updateprofilwarga(Request $request)
    {
        // Mendapatkan pengguna yang sedang terautentikasi.
        $user = Auth::user();

        // Jika tidak ada user yang terautentikasi
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Mencari data warga berdasarkan akun_user_id dari pengguna yang terautentikasi.
        $warga = Warga::where('akun_user_id', $user->id)->first();

        // Jika warga tidak ditemukan, kembalikan respons error.
        if (!$warga) {
            return response()->json(['message' => 'Profil warga tidak ditemukan.'], 404);
        }

        // Melakukan validasi input dari request.
        $validator = Validator::make($request->all(), [
            'nama' => 'sometimes|string|max:255',
            'nik' => 'sometimes|string|max:16|unique:warga,nik,' . $warga->id, // NIK unik, kecuali untuk dirinya sendiri
            'kk' => 'sometimes|string|max:16',
            'alamat_rumah' => 'sometimes|string|max:255',
            'no_rumah' => 'sometimes|string|max:50',
            'no_hp' => 'sometimes|string|max:15',
            'jumlah_anggota_keluarga' => 'sometimes|integer|min:1',
            'foto_rumah' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048', // Maks 2MB
        ]);

        // Jika validasi gagal, kembalikan respons error.
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Mengisi data warga dengan input yang divalidasi.
        $warga->fill($request->only([
            'nama', 'nik', 'kk', 'alamat_rumah', 'no_rumah', 'no_hp', 'jumlah_anggota_keluarga'
        ]));

        // Menangani unggahan foto rumah jika ada.
        if ($request->hasFile('foto_rumah')) {
            // Hapus foto lama jika ada
            // Pastikan Anda menyimpan path internal di database, bukan URL lengkap, agar Storage::delete berfungsi
            if ($warga->foto_rumah && Storage::disk('public')->exists($warga->foto_rumah)) { // Menggunakan disk('public')
                Storage::disk('public')->delete($warga->foto_rumah); // Menggunakan disk('public')
            }

            // Simpan foto baru ke direktori yang sesuai dengan URL yang diharapkan
            $path = $request->file('foto_rumah')->store('photos/warga', 'public'); // Simpan langsung di subfolder 'public' disk
            $warga->foto_rumah = $path; // Simpan path internal di database (misal: 'photos/warga/namafile.png')
        }

        // Menyimpan perubahan ke database.
        $warga->save();

        // Mengembalikan respons sukses dengan data warga yang diperbarui.
        // Penting: Setelah menyimpan, konstruksi ulang URL publik untuk respons
        $responseWarga = $warga->toArray();
        if ($warga->foto_rumah) {
            $responseWarga['foto_rumah'] = Storage::disk('public')->url($warga->foto_rumah);
        }

        return response()->json([
            'message' => 'Profil warga berhasil diperbarui.',
            'data' => $responseWarga
        ], 200);
    }
    

}
