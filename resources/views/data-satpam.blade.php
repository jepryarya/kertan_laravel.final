<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Data Satpam</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    .custom-navbar {
      background-color: #007bff;
      color: white;
      padding: 1rem;
      position: relative;
      height: 70px;
      display: flex;
      align-items: center;
    }

    .custom-navbar img {
      height: 50px;
    }

    .navbar-logo {
      margin-right: 1rem;
    }

    .navbar-title-wrapper {
      overflow: hidden;
      white-space: nowrap;
      flex: 1;
      position: relative;
    }

    .navbar-title-text {
      display: inline-block;
      padding-left: 100%;
      animation: scrollText 10s linear infinite;
      font-size: 1.5rem;
      font-weight: bold;
    }

    @keyframes scrollText {
      0% { transform: translateX(0%); }
      100% { transform: translateX(-100%); }
    }

    .radio-inputs {
      position: relative;
      display: flex;
      flex-wrap: wrap;
      border-radius: 1rem;
      background: linear-gradient(145deg, #e6e6e6, #ffffff);
      box-sizing: border-box;
      box-shadow:
        5px 5px 15px rgba(0, 0, 0, 0.15),
        -5px -5px 15px rgba(255, 255, 255, 0.8);
      padding: 0.5rem;
      font-size: 14px;
      gap: 0.5rem;
      margin-left: auto;
    }

    .radio-inputs .radio {
      flex: 1 1 auto;
      text-align: center;
      position: relative;
    }

    .radio-inputs .radio input {
      display: none;
    }

    .radio-inputs .radio .name {
      display: flex;
      cursor: pointer;
      align-items: center;
      justify-content: center;
      border-radius: 0.7rem;
      border: none;
      padding: 0.7rem 0.8rem;
      color: #2d3748;
      font-weight: 500;
      font-family: inherit;
      background: linear-gradient(145deg, #ffffff, #e6e6e6);
      box-shadow:
        3px 3px 6px rgba(0, 0, 0, 0.1),
        -3px -3px 6px rgba(255, 255, 255, 0.7);
      transition: all 0.2s ease;
    }

    .radio-inputs .radio input:checked + .name {
      background: linear-gradient(145deg, #3b82f6, #2563eb);
      color: white;
      font-weight: 600;
      text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
      box-shadow:
        inset 2px 2px 5px rgba(0, 0, 0, 0.2),
        inset -2px -2px 5px rgba(255, 255, 255, 0.1),
        3px 3px 8px rgba(59, 130, 246, 0.3);
      transform: translateY(2px);
    }

    @media (max-width: 768px) {
      .radio-inputs {
        width: 100%;
        max-width: 100%;
        justify-content: center;
      }
      .navbar-title-text {
        font-size: 1.2rem;
      }
      .custom-navbar img {
        height: 40px;
      }
    }
  </style>
</head>
<body>

  <div class="custom-navbar">
    <div class="navbar-logo">
      <img src="{{ asset('gambar/logo_kertan2.png') }}" alt="Logo Kertan">
    </div>
    <div class="navbar-title-wrapper">
      <div class="navbar-title-text">Melayani Warga Adalah Kewajiban Bagi Seluruh Kepengurusan Ke-RTAN</div>
    </div>
  </div>

  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container d-flex align-items-center justify-content-between">
      <a class="navbar-brand" href="#">Tabel Data Satpam</a>
      <div class="radio-inputs">
        <label class="radio">
          <input type="radio" name="radio" onclick="window.location.href='{{ route('beranda') }}'" {{ Route::currentRouteName() == 'beranda' ? 'checked' : '' }}>
          <span class="name">Beranda</span>
        </label>
        <label class="radio">
          <input type="radio" name="radio" onclick="window.location.href='{{ route('data.warga') }}'" {{ Route::currentRouteName() == 'data.warga' ? 'checked' : '' }}>
          <span class="name">Data Warga</span>
        </label>
        <label class="radio">
          <input type="radio" name="radio" onclick="window.location.href='{{ route('data.satpam') }}'" {{ Route::currentRouteName() == 'data.satpam' ? 'checked' : '' }}>
          <span class="name">Data Satpam</span>
        </label>
      </div>
    </div>
  </nav>

  <div class="container mt-4">
    <h2 class="mb-4">Data Satpam</h2>

    <table class="table table-bordered table-striped table-hover">
      <thead class="table-dark">
        <tr>
          <th>No</th>
          <th>Nama</th>
          <th>Shift</th>
          <th>No HP</th>
          <th>Foto</th> <th>Dibuat</th>
          <th>Diperbarui</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($satpam as $index => $data)
        <tr>
          <td>{{ $index + 1 }}</td>
          <td>{{ $data->nama }}</td>
          <td>{{ $data->shift }}</td>
          <td>{{ $data->no_hp }}</td>
          <td>
            @if($data->foto_satpam)
              <img src="{{ asset('storage/' . $data->foto_satpam) }}" alt="Foto Satpam" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
            @else
              Tidak Ada Foto
            @endif
          </td> <td>{{ $data->created_at ? $data->created_at->format('d-m-Y H:i') : '-' }}</td>
          <td>{{ $data->updated_at ? $data->updated_at->format('d-m-Y H:i') : '-' }}</td>
          <td>
            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $data->id }}">
              Edit
            </button>

            <form action="{{ route('satpam.destroy', $data->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
            </form>

            <div class="modal fade" id="editModal{{ $data->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $data->id }}" aria-hidden="true">
              <div class="modal-dialog">
                {{-- Tambahkan enctype untuk upload file --}}
                <form action="{{ route('satpam.update', $data->id) }}" method="POST" enctype="multipart/form-data">
                  @csrf
                  @method('PUT')
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="editModalLabel{{ $data->id }}">Edit Data Satpam</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                      <div class="mb-3">
                        <label for="nama{{ $data->id }}" class="form-label">Nama</label>
                        <input type="text" class="form-control" name="nama" id="nama{{ $data->id }}" value="{{ $data->nama }}" required>
                      </div>
                      <div class="mb-3">
                        <label for="shift{{ $data->id }}" class="form-label">Shift</label>
                        {{-- Ganti input text menjadi select untuk shift --}}
                        <select class="form-select" name="shift" id="shift{{ $data->id }}" required>
                            <option value="pagi" {{ $data->shift == 'pagi' ? 'selected' : '' }}>Pagi</option>
                            <option value="siang" {{ $data->shift == 'siang' ? 'selected' : '' }}>Siang</option>
                            <option value="malam" {{ $data->shift == 'malam' ? 'selected' : '' }}>Malam</option>
                        </select>
                      </div>
                      <div class="mb-3">
                        <label for="no_hp{{ $data->id }}" class="form-label">No HP</label>
                        <input type="text" class="form-control" name="no_hp" id="no_hp{{ $data->id }}" value="{{ $data->no_hp }}" required>
                      </div>

                      {{-- Tambahkan input untuk akun_user_id --}}
                      <div class="mb-3">
                        <label for="akun_user_id{{ $data->id }}" class="form-label">Akun User</label>
                        <select class="form-select" name="akun_user_id" id="akun_user_id{{ $data->id }}" required>
                            @foreach($akunUsers as $akun)
                                <option value="{{ $akun->id }}" {{ $data->akun_user_id == $akun->id ? 'selected' : '' }}>
                                    {{ $akun->name }} (ID: {{ $akun->id }})
                                </option>
                            @endforeach
                        </select>
                      </div>

                      {{-- Tambahkan field upload foto --}}
                      <div class="mb-3">
                        <label for="foto_satpam{{ $data->id }}" class="form-label">Foto Satpam</label>
                        @if($data->foto_satpam)
                          <div class="mb-2">
                            <img src="{{ asset('storage/' . $data->foto_satpam) }}" alt="Foto Saat Ini" style="width: 100px; height: 100px; object-fit: cover; border-radius: 5px;">
                            <small class="text-muted d-block">Foto saat ini</small>
                          </div>
                        @else
                          <small class="text-muted d-block mb-2">Belum ada foto yang diunggah.</small>
                        @endif
                        <input class="form-control" type="file" name="foto_satpam" id="foto_satpam{{ $data->id }}">
                        <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah foto.</small>
                      </div>

                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                      <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" class="text-center">Tidak ada data satpam</td> </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <footer class="bg-light text-center py-3 mt-5">
    <div class="container">
      <span class="text-muted">© {{ date('Y') }} Sistem Pendataan Warga</span>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>