<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Form Data Satpam</title>
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

  <div class="navbar d-flex justify-content-between align-items-center">
       <a href="{{ route('beranda') }}">
    <img src="gambar/logo_kertan2.png" alt="Logo Kertan" class="logo-img" style="height: 40px;" />
</a>
    <h5>Form Data Satpam</h5>
    <i class="bi bi-person-circle icon-user" id="profileIcon" title="Profil"></i>
  </div>

  <div id="sidebarProfile" class="sidebar-profile" aria-hidden="true" aria-label="Sidebar Profil">
    <button class="close-btn" id="closeSidebarProfile" aria-label="Tutup Sidebar Profil">×</button>
<a href="{{ route('akun.satpam') }}" class="menu-item">Tambah Data Satpam</a>
<a href="{{ route('akun.warga') }}" class="menu-item">Tambah Data Warga</a>
  </div>

  <div class="container mt-5" style="max-width: 600px;">
    <h3 class="mb-4 text-primary">Form Data Satpam</h3>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    {{-- Tambahkan enctype="multipart/form-data" di sini --}}
    <form action="{{ route('satpam.simpan') }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div class="mb-3">
        <label for="nama" class="form-label">Nama Lengkap</label>
        <input type="text" class="form-control" id="nama" name="nama" value="{{ old('nama') }}" required autofocus maxlength="100" />
      </div>

      <div class="mb-3">
        <label for="no_hp" class="form-label">Nomor HP</label>
        <input type="tel" class="form-control" id="no_hp" name="no_hp" value="{{ old('no_hp') }}" required maxlength="20" />
      </div>

      <div class="mb-3">
        <label for="shift" class="form-label">Shift</label>
        <select class="form-select" id="shift" name="shift" required>
          <option value="" disabled {{ old('shift') ? '' : 'selected' }}>Pilih Shift</option>
          <option value="pagi" {{ old('shift') == 'pagi' ? 'selected' : '' }}>Pagi</option>
          <option value="siang" {{ old('shift') == 'siang' ? 'selected' : '' }}>Siang</option>
          <option value="malam" {{ old('shift') == 'malam' ? 'selected' : '' }}>Malam</option>
        </select>
      </div>

      <div class="mb-3">
        <label for="akun_user_id" class="form-label">Verifikasi Akun Satpam</label>
        <select class="form-select" id="akun_user_id" name="akun_user_id" required>
          <option value="" disabled {{ old('akun_user_id') ? '' : 'selected' }}>Pilih Akun User</option>
          @foreach($akunUsers as $akun)
            <option value="{{ $akun->id }}" {{ old('akun_user_id') == $akun->id ? 'selected' : '' }}>
              {{ $akun->name }} ({{ $akun->email }})
            </option>
          @endforeach
        </select>
      </div>

      {{-- Tambahkan field upload foto di sini --}}
      <div class="mb-3">
        <label for="foto_satpam" class="form-label">Foto Satpam</label>
        <input class="form-control" type="file" id="foto_satpam" name="foto_satpam">
        <small class="form-text text-muted">Maksimal ukuran file 2MB (JPG, PNG, GIF, SVG).</small>
      </div>

      <button type="submit" class="btn btn-primary w-100">Simpan Data Satpam</button>
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
  </script>
</body>
</html>