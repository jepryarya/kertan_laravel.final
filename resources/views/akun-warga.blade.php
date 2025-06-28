<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Daftar Akun Warga</title>
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

  <!-- Header -->
  <div class="navbar d-flex justify-content-between align-items-center">
    <img src="gambar/logo_kertan2.png" alt="Logo Kertan" class="logo-img" style="height: 40px;" />
    <h5>Form Akun Warga</h5>
    <i class="bi bi-person-circle icon-user" id="profileIcon" title="Profil"></i>
  </div>

  <!-- Sidebar Profil -->
  <div id="sidebarProfile" class="sidebar-profile" aria-hidden="true" aria-label="Sidebar Profil">
    <button class="close-btn" id="closeSidebarProfile" aria-label="Tutup Sidebar Profil">×</button>
<a href="{{ route('akun.satpam') }}" class="menu-item">Tambah Data Satpam</a>
<a href="{{ route('akun.warga') }}" class="menu-item">Tambah Data Warga</a>
<a href="{{ route('akun.warga.tampilkan') }}" class="menu-item">Tampilkan Akun Warga</a>
  </div>

  <!-- Isi Konten Form -->
  <div class="container mt-5" style="max-width: 500px;">
    <h3 class="mb-4 text-primary">Form Daftar Akun Warga</h3>

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

    <form action="{{ route('akun.warga.simpan') }}" method="POST">
      @csrf
      <div class="mb-3">
        <label for="name" class="form-label">Nama Lengkap</label>
        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required autofocus />
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required />
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required minlength="6" />
      </div>

      <div class="mb-3">
        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required minlength="6" />
      </div>

      <button type="submit" class="btn btn-primary w-100">Daftar Akun</button>
    </form>

    <a href="{{ route('form.warga') }}" class="btn btn-secondary w-100 mt-3">Isi Data Warga</a>
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