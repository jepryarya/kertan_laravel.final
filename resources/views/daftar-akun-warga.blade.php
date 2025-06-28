<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tabel Akun Warga</title>
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
    </style>
</head>
<body>

    <nav class="navbar d-flex justify-content-between align-items-center" role="navigation" aria-label="Main navigation">
        <a href="{{ route('beranda') }}" aria-label="Beranda">
            <img src="{{ asset('gambar/logo_kertan2.png') }}" alt="Logo Kertan" class="logo-img" style="height: 40px;" />
        </a>
        <h5>Tabel Akun Warga</h5>
        <button class="btn btn-link icon-user" id="profileIcon" aria-label="Profil pengguna" title="Profil">
            <i class="bi bi-person-circle"></i>
        </button>
    </nav>

    <aside id="sidebarProfile" class="sidebar-profile" aria-hidden="true" aria-label="Sidebar Profil">
        <button class="close-btn" id="closeSidebarProfile" aria-label="Tutup Sidebar Profil">×</button>
        <a href="{{ route('akun.satpam') }}" class="menu-item">Tambah Akun Satpam</a>
        <a href="{{ route('akun.warga') }}" class="menu-item">Tambah Akun Warga</a>
        <a href="{{ route('akun.admin2') }}" class="menu-item">Tampilkan Akun Satpam</a>
       
    </aside>

    <main class="container mt-5" role="main">
        <h3 class="mb-4 text-primary">Tabel Akun Warga</h3>
        
        @if(session('success'))
            <div class="alert alert-success" role="alert">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-hover bg-white">
                <thead class="table-primary">
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Nama Lengkap</th>
                        <th scope="col">Email</th>
                        <th scope="col">Tanggal Daftar</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Asumsi Anda meneruskan variabel $akunWarga dari controller --}}
                    @forelse ($akunWarga as $index => $warga_account)
                        <tr>
                            <th scope="row">{{ $index + 1 }}</th>
                            <td>{{ $warga_account->name }}</td>
                            <td>{{ $warga_account->email }}</td>
                            <td>{{ $warga_account->created_at->format('d M Y') }}</td>
                            <td>
                                {{-- Tombol edit dan hapus, sesuaikan rute dan modal jika dibutuhkan --}}
                                <button type="button" class="btn btn-warning btn-sm me-2 edit-btn"
                                        data-bs-toggle="modal" data-bs-target="#editWargaModal"
                                        data-id="{{ $warga_account->id }}"
                                        data-name="{{ $warga_account->name }}"
                                        data-email="{{ $warga_account->email }}">
                                    Edit
                                </button>
                                
                                <form action="{{ route('akun.warga.destroy', $warga_account->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Belum ada akun warga.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>

    {{-- Modal Edit Akun Warga (opsional, tambahkan jika Anda ingin fungsi edit) --}}
    <div class="modal fade" id="editWargaModal" tabindex="-1" aria-labelledby="editWargaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editWargaModalLabel">Edit Akun Warga</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editWargaForm" method="POST">
                    @csrf
                    @method('PUT') {{-- Metode PUT untuk update --}}
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                        <div class="mb-3 password-toggle-group">
                            <label for="edit_password" class="form-label">Password (kosongkan jika tidak ingin diubah)</label>
                            <input type="password" class="form-control" id="edit_password" name="password">
                            <span class="toggle-password" id="togglePasswordWarga">
                                <i class="bi bi-eye-fill"></i>
                            </span>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
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

        // Logika untuk mengisi modal edit akun warga
        document.addEventListener('DOMContentLoaded', function () {
            const editWargaModal = document.getElementById('editWargaModal');
            if (editWargaModal) { // Pastikan modal ada sebelum menambahkan event listener
                editWargaModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const id = button.getAttribute('data-id');
                    const name = button.getAttribute('data-name');
                    const email = button.getAttribute('data-email');

                    const modalForm = editWargaModal.querySelector('#editWargaForm');
                    const modalNameInput = editWargaModal.querySelector('#edit_name');
                    const modalEmailInput = editWargaModal.querySelector('#edit_email');
                    const modalPasswordInput = editWargaModal.querySelector('#edit_password');

                    modalNameInput.value = name;
                    modalEmailInput.value = email;
                    modalPasswordInput.value = ''; // Kosongkan password setiap kali modal dibuka

                    // Sesuaikan action form modal dengan rute update untuk akun warga
                    modalForm.action = `/akun_warga/${id}`;
                });
            }

            // Logika untuk toggle password visibility di modal edit warga
            const togglePasswordWarga = document.getElementById('togglePasswordWarga');
            const passwordInputWarga = document.getElementById('edit_password');

            if (togglePasswordWarga && passwordInputWarga) {
                togglePasswordWarga.addEventListener('click', function () {
                    const type = passwordInputWarga.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInputWarga.setAttribute('type', type);
                    
                    this.querySelector('i').classList.toggle('bi-eye-fill');
                    this.querySelector('i').classList.toggle('bi-eye-slash-fill');
                });
            }
        });
    </script>
</body>
</html>
