<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Form Data RT</title>
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


    /* Custom Card Design */
    .card {
      width: 250px;
      height: 380px;
      background: linear-gradient(135deg, #1e3c72, #2a5298);
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
      margin: 0 auto;
      padding: 20px;
      display: flex;
      flex-direction: column;
      align-items: center;
      position: relative;
      overflow: hidden;
    }

    .card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: url('data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"%3E%3Cpath fill="%23ffffff33" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,176C384,192,480,192,576,186.7C672,181,768,171,864,165.3C960,160,1056,160,1152,176C1248,192,1344,224,1392,240L1440,256V320H1392H1344H1248H1152H1056H960H864H768H672H576H480H384H288H192H96H48H0V96Z"%3E%3C/path%3E%3C/svg%3E');
      background-size: cover;
      opacity: 0.1;
      z-index: 1;
    }

    .card .card-header {
      width: 100%;
      height: 20px;
      background: #2a5298;
      border-radius: 5px 5px 0 0;
      margin-bottom: 15px;
    }

    .card .img {
      width: 60px;
      height: 60px;
      background: #ffffff;
      border-radius: 10px;
      margin-bottom: 15px;
      overflow: hidden;
      z-index: 2;
    }

    .card .img img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .card .data-container {
      width: 100%;
      padding: 0 10px;
      text-align: left;
      color: #ffffff;
      font-size: 14px;
      z-index: 2;
    }

    .card .data-container .data-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 10px;
    }

    .card .data-container .data-row .label {
      font-weight: 600;
      color: #e0e7ff;
    }

    .card .data-container .data-row .value {
      font-weight: 400;
      text-align: right;
      flex: 1;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .card .periode {
      text-align: center;
      font-size: 14px;
      font-weight: 400;
      color: #ffffff;
      margin-top: 10px;
      z-index: 2;
    }

    .card .button-placeholder {
      width: 60px;
      height: 20px;
      background: #4a6dc7;
      border-radius: 10px;
      margin-top: 15px;
      z-index: 2;
    }

    .card .button-placeholder:hover {
      background: #3a5aa7;
    }
  </style>
</head>
<body>

  <div class="navbar d-flex justify-content-between align-items-center">
    <a href="{{ route('beranda') }}">
      <img src="gambar/logo_kertan2.png" alt="Logo Kertan" class="logo-img" style="height: 40px;" />
    </a>
    <h5>Form Data RT</h5>
    <i class="bi bi-person-circle icon-user" id="profileIcon" title="Profil"></i>
  </div>

  <div id="sidebarProfile" class="sidebar-profile" aria-hidden="true" aria-label="Sidebar Profil">
    <button class="close-btn" id="closeSidebarProfile" aria-label="Tutup Sidebar Profil">×</button>
    <a href="{{ route('akun.satpam') }}" class="menu-item">Tambah Data Satpam</a>
    <a href="{{ route('akun.warga') }}" class="menu-item">Tambah Data Warga</a>
  </div>

  <div class="container mt-4" style="max-width: 600px;">
    @if(!empty($rt))
      <div class="card">
        <div class="card-header"></div>
        @if(!empty($rt->foto))
          <div class="img">
            <img src="{{ asset('storage/' . $rt->foto) }}" alt="Foto Ketua RT">
          </div>
        @else
          <div class="img"></div>
        @endif
        <div class="data-container">
          <div class="data-row">
            <span class="label">Nama:</span>
            <span class="value">{{ $rt->nama }}</span>
          </div>
          <div class="data-row">
            <span class="label">No HP:</span>
            <span class="value">{{ $rt->no_hp }}</span>
          </div>
          <div class="data-row">
            <span class="label">Jabatan:</span>
            <span class="value">Ketua RT</span>
          </div>
          <div class="data-row">
              <span class="label">Lokasi Maps:</span>
              <span class="value">
                  @if(!empty($rt->Maps_link))
                      <a href="{{ $rt->Maps_link }}" target="_blank" class="text-white text-decoration-none" title="Lihat di Google Maps">Lihat Lokasi</a>
                  @else
                      -
                  @endif
              </span>
          </div>
          <div class="periode">{{ $rt->periode_mulai }} - {{ $rt->periode_selesai }}</div>
        </div>
        <div class="button-placeholder"></div>
      </div>
    @else
      <div class="card">
        <div class="card-header"></div>
        <div class="img"></div>
        <div class="data-container">
          <div class="data-row">
            <span class="label">Nama:</span>
            <span class="value">-</span>
          </div>
          <div class="data-row">
            <span class="label">No HP:</span>
            <span class="value">-</span>
          </div>
          <div class="data-row">
            <span class="label">Jabatan:</span>
            <span class="value">-</span>
          </div>
          <div class="data-row">
            <span class="label">Lokasi Maps:</span>
            <span class="value">-</span>
          </div>
          <div class="periode">-</div>
        </div>
        <div class="button-placeholder"></div>
      </div>
    @endif
  </div>

  <div class="container mt-5" style="max-width: 600px;">
    <h3 class="mb-4 text-primary">Form Data RT</h3>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
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

    <form action="{{ route('rt.simpan') }}" method="POST" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="id" value="{{ $rt->id ?? '' }}">

      <div class="mb-3">
        <label for="nama" class="form-label">Nama Lengkap Ketua RT</label>
        <input type="text" class="form-control" id="nama" name="nama"
          value="{{ old('nama', $rt->nama ?? '') }}" required autofocus maxlength="100">
      </div>

      <div class="mb-3">
        <label for="no_hp" class="form-label">Nomor HP</label>
        <input type="tel" class="form-control" id="no_hp" name="no_hp"
          value="{{ old('no_hp', $rt->no_hp ?? '') }}" required maxlength="20">
      </div>

      <div class="mb-3">
        <label for="periode_mulai" class="form-label">Periode Mulai (Tahun)</label>
        <input type="number" class="form-control" id="periode_mulai" name="periode_mulai"
          value="{{ old('periode_mulai', $rt->periode_mulai ?? '') }}" required min="1900" max="9999">
      </div>

      <div class="mb-3">
        <label for="periode_selesai" class="form-label">Periode Selesai (Tahun)</label>
        <input type="number" class="form-control" id="periode_selesai" name="periode_selesai"
          value="{{ old('periode_selesai', $rt->periode_selesai ?? '') }}" required min="1900" max="9999">
      </div>

      <div class="mb-3">
        <label for="Maps_link" class="form-label">Link Google Maps</label>
        <input type="url" class="form-control" id="Maps_link" name="Maps_link"
          value="{{ old('Maps_link', $rt->Maps_link ?? '') }}" placeholder="Masukkan link Google Maps di sini">
        @error('Maps_link')
            <div class="text-danger mt-1">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3">
        <label for="foto" class="form-label">Foto Ketua RT (jpeg/png, max 2MB)</label>
        <input type="file" class="form-control" id="foto" name="foto" accept="image/jpeg,image/png">
      </div>

      @if(!empty($rt->foto))
      <div class="mb-3">
        <label class="form-label">Foto Saat Ini:</label><br />
        <img src="{{ asset('storage/' . $rt->foto) }}" alt="Foto Ketua RT" style="max-width: 150px; border-radius: 8px;">
      </div>
      @endif

      <button type="submit" class="btn btn-primary w-100">Simpan Data RT</button>
    </form>

    @if(!empty($rt))
    <form action="{{ route('rt.hapus') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data RT ini? Tindakan ini tidak dapat dibatalkan dan akan menghapus juga foto terkait.');" class="mt-3">
        @csrf
        <button type="submit" class="btn btn-danger w-100">Hapus Data RT</button>
    </form>
    @endif
  </div>

  <div class="settings-icon" id="settingsIcon" title="Pengaturan">
    <i class="bi bi-gear-fill fs-5"></i>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const profileIcon = document.getElementById('profileIcon');
    const sidebarProfile = document.getElementById('sidebarProfile');
    const closeSidebarProfileBtn = document.getElementById('closeSidebarProfile');
    const settingsIcon = document.getElementById('settingsIcon');
    const settingsPanel = document.getElementById('settingsPanel');

    profileIcon.addEventListener('click', () => {
      sidebarProfile.classList.add('open');
      sidebarProfile.setAttribute('aria-hidden', 'false');
      settingsPanel.classList.remove('open');
      settingsPanel.setAttribute('aria-hidden', 'true');
    });

    closeSidebarProfileBtn.addEventListener('click', () => {
      sidebarProfile.classList.remove('open');
      sidebarProfile.setAttribute('aria-hidden', 'true');
    });

    settingsIcon.addEventListener('click', () => {
      settingsPanel.classList.toggle('open');
      const isOpen = settingsPanel.classList.contains('open');
      settingsPanel.setAttribute('aria-hidden', !isOpen);
      sidebarProfile.classList.remove('open');
      sidebarProfile.setAttribute('aria-hidden', 'true');
    });
  </script>
</body>
</html>