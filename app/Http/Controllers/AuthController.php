<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AkunUser;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showWelcomePage()
    {
        return view('welcome');
    }
 
public function login(Request $request) 
{
    $validated = $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    if (Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']])) {
        $user = Auth::user();

        if ($user->role === 'admin1') {
            return redirect('/beranda')->with('message', 'Login berhasil sebagai Admin 1');
        }

        // Kalau ada role lain, arahkan ke tempat lain atau beri pesan
        return redirect('/welcome')->withErrors(['akses' => 'Role tidak diizinkan masuk ke sistem.']);
    }

    return redirect('/welcome')->withErrors(['error' => 'Kredensial tidak valid']);
}


 //  public function registerAdmin1(Request $request)
//{
 //   $validated = $request->validate([
 //       'name' => 'required|string|max:255',
 //       'email' => 'required|email|unique:akun_user,email',
 //       'password' => 'required|string|min:6|confirmed',
 //   ]);

 //   $validated['role'] = 'admin1';
 //   $validated['password'] = Hash::make($validated['password']);

 //   AkunUser::create($validated);

 //   return redirect('/welcome')->with('success', 'Registrasi berhasil! Silakan //login.');
//}


    public function logout()
    {
        Auth::logout();
        return redirect('/welcome')->with('message', 'Logout berhasil');
    }

    public function apiLogin(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            $user = AkunUser::where('email', $validated['email'])->first();

            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email atau password salah'
                ], 401);
            }

            // Periksa apakah role pengguna diizinkan untuk login API (admin2 atau user)
            if (!in_array($user->role, ['admin2', 'user'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role tidak diizinkan untuk login melalui aplikasi ini.'
                ], 403);
            }
            
            // Buat token Sanctum baru untuk pengguna
            $token = $user->createToken('ionic-auth-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat login',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menangani permintaan logout API.
     * Menghapus token Sanctum pengguna saat ini.
     */
    public function apiLogout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logout berhasil!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengembalikan data pengguna yang sedang terotentikasi.
     */
    public function getAuthenticatedUser(Request $request)
    {
        try {
            $user = $request->user();

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pengguna',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
