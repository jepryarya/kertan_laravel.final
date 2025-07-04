<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Form Data Warga</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f2f9ff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        .navbar {
            background-color: #0d6efd;
            color: white;
            padding: 20px 20px;
        }

        .navbar h5 {
            margin: 0 auto;
            color: white;
        }

        .icon-user {
            font-size: 45px;
            color: white;
            cursor: pointer;
        }

        .sidebar-profile {
            height: 100vh;
            width: 0;
            position: fixed;
            top: 0;
            right: 0;
            background-color: #0d6efd;
            overflow-x: hidden;
            transition: 0.4s;
            padding-top: 60px;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.3);
            z-index: 1050;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .sidebar-profile.open {
            width: 250px;
        }

        .sidebar-profile button.close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 30px;
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            outline: none;
        }

        .sidebar-profile a.menu-item {
            background-color: #e6f1ff;
            color: #0d6efd;
            border: none;
            border-radius: 10px;
            padding: 15px;
            font-weight: 600;
            cursor: pointer;
            margin: 0 15px;
            transition: background-color 0.3s ease;
            text-align: center;
            user-select: none;
            text-decoration: none;
            display: block;
        }

        .sidebar-profile a.menu-item:hover {
            background-color: #d0e8ff;
        }

        /* Gaya untuk pesan error */
        .error-message {
            color: red;
            font-size: 0.875em;
            margin-top: 5px;
        }

        /* Gaya untuk input yang tidak valid */
        .is-invalid {
            border-color: red !important;
            color: red !important;
        }
    </style>
</head>
<body>

    <div class="navbar d-flex justify-content-between align-items-center">
        <a href="{{ route('beranda') }}">
            <img src="gambar/logo_kertan2.png" alt="Logo Kertan" class="logo-img" style="height: 40px;" />
        </a>
        <h5>Form Data Warga</h5>
        <i class="bi bi-person-circle icon-user" id="profileIcon" title="Profil"></i>
    </div>

    <div id="sidebarProfile" class="sidebar-profile" aria-hidden="true" aria-label="Sidebar Profil">
        <button class="close-btn" id="closeSidebarProfile" aria-label="Tutup Sidebar Profil">×</button>
        <a href="{{ route('akun.satpam') }}" class="menu-item">Tambah Data Satpam</a>
        <a href="{{ route('akun.warga') }}" class="menu-item">Tambah Data Warga</a>
    </div>

    <div class="container mt-5" style="max-width: 600px;">
        <h3 class="mb-4 text-primary">Form Data Warga</h3>

        <div id="message"></div> <form id="wargaForm" method="POST" action="{{ route('warga.simpan') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="nama" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" id="nama" name="nama" required>
            </div>
            <div class="mb-3">
                <label for="nik" class="form-label">NIK</label>
                <input type="text" class="form-control" id="nik" name="nik" required pattern="\d{16}" title="NIK harus 16 digit angka">
                <span id="nikErrorMessage" class="error-message"></span>
            </div>
            <div class="mb-3">
                <label for="kk" class="form-label">No KK</label>
                <input type="text" class="form-control" id="kk" name="kk" required pattern="\d{16}" title="No KK harus 16 digit angka">
                <span id="kkErrorMessage" class="error-message"></span>
            </div>
            <div class="mb-3">
                <label for="alamat_rumah" class="form-label">Alamat Rumah</label>
                <input type="text" class="form-control" id="alamat_rumah" name="alamat_rumah" required>
            </div>
            <div class="mb-3">
                <label for="no_rumah" class="form-label">No Rumah</label>
                <input type="text" class="form-control" id="no_rumah" name="no_rumah" required>
            </div>
            <div class="mb-3">
                <label for="no_hp" class="form-label">No HP</label>
                <input type="text" class="form-control" id="no_hp" name="no_hp" required>
            </div>
            
            {{-- Kolom baru untuk foto_rumah --}}
            <div class="mb-3">
                <label for="foto_rumah" class="form-label">Foto Rumah</label>
                <input type="file" class="form-control" id="foto_rumah" name="foto_rumah" accept="image/*">
                {{-- 'accept="image/*"' akan membatasi pilihan file hanya pada gambar --}}
            </div>

            {{-- Kolom baru untuk jumlah_anggota_keluarga --}}
            <div class="mb-3">
                <label for="jumlah_anggota_keluarga" class="form-label">Jumlah Anggota Keluarga</label>
                <input type="number" class="form-control" id="jumlah_anggota_keluarga" name="jumlah_anggota_keluarga" min="0">
                {{-- 'min="0"' memastikan nilai tidak negatif --}}
            </div>

            <div class="mb-3">
                <label for="akun_user_id" class="form-label">Pilih Akun Warga</label>
                <select name="akun_user_id" id="akun_user_id" class="form-select" required>
                    <option value="">-- Pilih Akun Warga --</option>
                    @foreach($akunUsers as $akun)
                        <option value="{{ $akun->id }}">{{ $akun->name }} ({{ $akun->email }})</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100" id="submitButton">Simpan Data Warga</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const profileIcon = document.getElementById('profileIcon');
        const sidebarProfile = document.getElementById('sidebarProfile');
        const closeSidebarProfileBtn = document.getElementById('closeSidebarProfile');

        profileIcon.addEventListener('click', () => {
            sidebarProfile.classList.add('open');
            sidebarProfile.setAttribute('aria-hidden', 'false');
        });

        closeSidebarProfileBtn.addEventListener('click', () => {
            sidebarProfile.classList.remove('open');
            sidebarProfile.setAttribute('aria-hidden', 'true');
        });

        const nikInput = document.getElementById('nik');
        const kkInput = document.getElementById('kk');
        const nikErrorMessage = document.getElementById('nikErrorMessage');
        const kkErrorMessage = document.getElementById('kkErrorMessage');
        const submitButton = document.getElementById('submitButton');
        const wargaForm = document.getElementById('wargaForm');
        const messageDiv = document.getElementById('message'); // Tambahkan ini

        function validateIdNumber(inputElement, errorMessageElement) {
            const value = inputElement.value;
            const isNumber = /^\d*$/.test(value);
            const isValidLength = value.length === 16;

            if (!isNumber) {
                inputElement.classList.add('is-invalid');
                errorMessageElement.textContent = 'Hanya angka yang diizinkan.';
                return false;
            } else if (!isValidLength) {
                inputElement.classList.add('is-invalid');
                errorMessageElement.textContent = `Harus 16 digit. Saat ini ${value.length} digit.`;
                return false;
            } else {
                inputElement.classList.remove('is-invalid');
                errorMessageElement.textContent = '';
                return true;
            }
        }

        function checkFormValidity() {
            const isNikValid = validateIdNumber(nikInput, nikErrorMessage);
            const isKkValid = validateIdNumber(kkInput, kkErrorMessage);
            
            const otherRequiredFields = wargaForm.querySelectorAll('input[required]:not(#nik):not(#kk), select[required]');
            let allOtherFieldsFilled = true;
            otherRequiredFields.forEach(field => {
                if (!field.value.trim()) {
                    allOtherFieldsFilled = false;
                }
            });

            submitButton.disabled = !(isNikValid && isKkValid && allOtherFieldsFilled);
        }

        nikInput.addEventListener('input', () => {
            validateIdNumber(nikInput, nikErrorMessage);
            checkFormValidity();
        });

        kkInput.addEventListener('input', () => {
            validateIdNumber(kkInput, kkErrorMessage);
            checkFormValidity();
        });

        wargaForm.querySelectorAll('input[required], select[required]').forEach(field => {
            field.addEventListener('input', checkFormValidity);
            field.addEventListener('change', checkFormValidity);
        });

        document.addEventListener('DOMContentLoaded', checkFormValidity);

        // MODIFIKASI PENTING DI SINI: MENGGANTI BEHAVIOR SUBMIT FORM
        wargaForm.addEventListener('submit', async function(event) {
            event.preventDefault(); // Mencegah form disubmit secara tradisional

            // Lakukan validasi akhir sebelum mengirim
            if (!validateIdNumber(nikInput, nikErrorMessage) || !validateIdNumber(kkInput, kkErrorMessage)) {
                alert('Mohon perbaiki kesalahan pada NIK atau No KK sebelum melanjutkan.');
                return; // Berhenti jika validasi gagal
            }

            // Memastikan semua field yang required terisi sebelum mengirim
            const otherRequiredFields = wargaForm.querySelectorAll('input[required]:not(#nik):not(#kk), select[required]');
            let allOtherFieldsFilled = true;
            otherRequiredFields.forEach(field => {
                if (!field.value.trim()) {
                    allOtherFieldsFilled = false;
                }
            });

            if (!allOtherFieldsFilled) {
                alert('Mohon lengkapi semua kolom yang wajib diisi.');
                return;
            }

            // Tampilkan pesan loading atau nonaktifkan tombol submit sementara
            submitButton.disabled = true;
            submitButton.textContent = 'Menyimpan...';
            messageDiv.innerHTML = ''; // Bersihkan pesan sebelumnya

            try {
                const formData = new FormData(this); // Mengambil semua data dari form, termasuk file
                const response = await fetch(this.action, {
                    method: this.method,
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest' // Menandakan request AJAX
                    }
                });

                const data = await response.json(); // Mengasumsikan server merespons dengan JSON

                if (response.ok) { // Jika respons HTTP adalah 2xx (sukses)
                    messageDiv.innerHTML = `<div class="alert alert-success alert-dismissible fade show" role="alert">
                                                ${data.message || 'Data warga berhasil disimpan!'}
                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                            </div>`;
                    wargaForm.reset(); // Mengosongkan form setelah sukses
                    checkFormValidity(); // Periksa kembali validitas form untuk menonaktifkan tombol submit
                } else { // Jika respons HTTP adalah error (misal 4xx, 5xx)
                    let errorMessage = 'Terjadi kesalahan saat menyimpan data.';
                    if (data && data.message) {
                        errorMessage = data.message;
                    } else if (data && data.errors) {
                        // Jika ada validasi error dari server
                        errorMessage += '<br><ul>';
                        for (const key in data.errors) {
                            errorMessage += `<li>${data.errors[key].join(', ')}</li>`;
                        }
                        errorMessage += '</ul>';
                    }
                    messageDiv.innerHTML = `<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                ${errorMessage}
                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                            </div>`;
                }
            } catch (error) {
                console.error('Error:', error);
                messageDiv.innerHTML = `<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            Terjadi masalah jaringan atau server. Silakan coba lagi.
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>`;
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = 'Simpan Data Warga';
            }
        });
    </script>
</body>
</html>