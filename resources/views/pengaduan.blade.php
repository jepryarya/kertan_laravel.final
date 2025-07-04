<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengaduan Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}"> {{-- Tambahkan ini untuk AJAX --}}
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 30px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        }
        .card-header {
            background-color: #007bff;
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 1.25rem; /* Menambahkan padding agar judul tidak terlalu mepet */
        }
        .table thead {
            background-color: #e9ecef;
        }
        .badge {
            padding: 0.5em 0.7em;
            font-size: 0.85em;
            font-weight: 600;
        }
        .badge-menunggu { background-color: #ffc107; color: #343a40; }
        .badge-diproses { background-color: #17a2b8; color: white; }
        .badge-selesai { background-color: #28a745; color: white; }
        .badge-ditolak { background-color: #dc3545; color: white; }

        /* Gaya untuk thumbnail gambar di tabel */
        .thumbnail-img {
            max-width: 60px; /* Ukuran thumbnail yang lebih kecil */
            max-height: 60px;
            object-fit: cover; /* Memastikan gambar tidak terdistorsi */
            border-radius: 4px;
            cursor: pointer; /* Menandakan bisa diklik */
        }
        .thumbnail-icon {
            font-size: 2em; /* Ukuran ikon untuk PDF */
            color: #dc3545; /* Warna ikon PDF */
            cursor: pointer;
        }
        .attachment-link {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }

        /* Gaya untuk Toast Notification */
        .fixed-bottom-toast {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1050;
            width: auto;
            min-width: 300px;
            text-align: center;
            padding: 1rem;
            border-radius: 0.5rem;
        }

        /* --- Gaya dari contoh kedua untuk konsistensi --- */
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
        .table-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        .table thead th {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
        .table tbody tr:hover {
            background-color: #e2f2ff;
        }
        /* Menggunakan badge yang sudah ada, tapi disesuaikan jika perlu */
        .status-badge {
            padding: 0.4em 0.8em;
            border-radius: 0.375rem;
            font-weight: bold;
            display: inline-block;
        }
    </style>
</head>
<body>
    {{-- Navbar seperti di contoh kedua --}}
    <div class="custom-navbar">
        <div class="navbar-logo">
            {{-- Pastikan path gambar logo Anda benar --}}
            <img src="{{ asset('gambar/logo_kertan2.png') }}" alt="Logo Kertan">
        </div>
        <div class="navbar-title-wrapper">
            <div class="navbar-title-text">Melayani Warga Adalah Kewajiban Bagi Seluruh Kepengurusan Ke-RTAN</div>
        </div>
    </div>

    {{-- Navigasi dengan radio button filter status --}}
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container d-flex align-items-center justify-content-between">
            <a class="navbar-brand" href="#">Manajemen Pengaduan Warga</a>
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
                <label class="radio">
                    <input type="radio" name="radio" onclick="window.location.href='{{ route('pengajuan.index') }}'" {{ Route::currentRouteName() == 'pengajuan.index' ? 'checked' : '' }}>
                    <span class="name">Data Pengajuan</span>
                </label>
                <label class="radio">
                    <input type="radio" name="radio" onclick="window.location.href='{{ route('laporan.tamu.index') ?? '#' }}'" {{ Route::currentRouteName() == 'laporan.tamu.index' ? 'checked' : '' }}>
                    <span class="name">Lihat Tamu</span>
                </label>
            </div>>
        </div>
    </nav>

    <div class="container py-5">
        {{-- BAGIAN DAFTAR PENGADUAN --}}
        <div class="card table-container"> {{-- Menggunakan table-container untuk style yang lebih baik --}}
            <div class="card-header text-center">
                <h4 class="mb-0">Daftar Pengaduan</h4>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($pengaduans->isEmpty())
                    <div class="alert alert-info text-center" role="alert">
                        Belum ada pengaduan yang masuk.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Pelapor</th> {{-- Tambah kolom pelapor --}}
                                    <th scope="col">Kategori</th>
                                    <th scope="col">Isi Pengaduan Singkat</th>
                                    <th scope="col">Foto</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Tanggal</th>
                                    <th scope="col">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pengaduans as $pengaduan)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $pengaduan->warga->nama ?? 'N/A' }}</td> {{-- Asumsi relasi warga --}}
                                    <td>{{ $pengaduan->kategori }}</td>
                                    <td>{{ Str::limit($pengaduan->isi_pengaduan, 50) }}</td>
                                    <td>
                                        @if($pengaduan->foto_laporan_path)
                                            @php
                                                $extension = pathinfo($pengaduan->foto_laporan_path, PATHINFO_EXTENSION);
                                                $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                            @endphp

                                            @if ($isImage)
                                                <a href="{{ Storage::url($pengaduan->foto_laporan_path) }}" target="_blank" title="Lihat Gambar Lampiran">
                                                    <img src="{{ Storage::url($pengaduan->foto_laporan_path) }}" alt="Foto Laporan" class="thumbnail-img">
                                                </a>
                                            @else
                                                <a href="{{ Storage::url($pengaduan->foto_laporan_path) }}" target="_blank" class="attachment-link" title="Lihat Dokumen Lampiran">
                                                    <i class="fas fa-file-pdf thumbnail-icon"></i>
                                                </a>
                                            @endif
                                        @else
                                            Tidak ada
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill badge-{{ Str::slug($pengaduan->status) }}">
                                            {{ ucfirst($pengaduan->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $pengaduan->created_at->format('d M Y H:i') }}</td>
                                    <td>
                                        {{-- Tombol Edit yang Membuka Modal --}}
                                        <button type="button" class="btn btn-sm btn-info text-white me-1 btn-edit-pengaduan"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editPengaduanModal"
                                            data-id="{{ $pengaduan->id }}"
                                            data-kategori="{{ $pengaduan->kategori }}"
                                            data-isi-pengaduan="{{ $pengaduan->isi_pengaduan }}"
                                            data-status="{{ $pengaduan->status }}"
                                            data-foto-laporan-path="{{ $pengaduan->foto_laporan_path }}">
                                            <i class="fas fa-edit"></i> Detail / Edit
                                        </button>

                                        <form action="{{ route('pengaduan.destroy_admin', $pengaduan->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengaduan ini? Tindakan ini tidak dapat dibatalkan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="fas fa-trash-alt"></i> Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- MODAL UNTUK EDIT PENGADUAN (tidak berubah secara fungsional) --}}
    <div class="modal fade" id="editPengaduanModal" tabindex="-1" aria-labelledby="editPengaduanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPengaduanModalLabel">Detail & Edit Pengaduan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editPengaduanForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" id="pengaduanId" name="pengaduan_id">

                        <div class="mb-3">
                            <label for="modalKategori" class="form-label">Kategori</label>
                            <input type="text" class="form-control" id="modalKategori" name="kategori" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="modalIsiPengaduan" class="form-label">Isi Pengaduan</label>
                            <textarea class="form-control" id="modalIsiPengaduan" name="isi_pengaduan" rows="5" required></textarea>
                            <div id="isiPengaduanError" class="text-danger" style="display: none;"></div>
                        </div>

                        <div class="mb-3">
                            <label for="modalStatus" class="form-label">Status</label>
                            <select class="form-select" id="modalStatus" name="status" required>
                                <option value="menunggu">Menunggu</option>
                                <option value="diproses">Diproses</option>
                                <option value="selesai">Selesai</option>
                                <option value="ditolak">Ditolak</option>
                            </select>
                            <div id="statusError" class="text-danger" style="display: none;"></div>
                        </div>

                        {{-- CONTAINER UNTUK MENAMPILKAN FOTO LAPORAN SAAT INI --}}
                        <div class="mb-3" id="fotoLaporanContainer" style="display: none;">
                            <label class="form-label">Foto Laporan Saat Ini:</label>
                            <div id="modalFotoLaporanPreview">
                                <a id="modalFotoLaporanLink" href="#" target="_blank" class="btn btn-sm btn-secondary mb-2" style="display: none;">Lihat Foto/Dokumen</a>
                                <img id="modalFotoLaporanImage" src="" alt="Foto Laporan" class="img-fluid rounded" style="max-width: 100%; height: auto; display: none;">
                            </div>
                            <button type="button" class="btn btn-info btn-sm mt-2" id="printImageBtn">Cetak Gambar Lampiran</button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toast functions (unchanged)
            function showToast(message, type) {
                const toastContainer = document.createElement('div');
                toastContainer.className = `alert alert-${type} alert-dismissible fade show fixed-bottom-toast`;
                toastContainer.setAttribute('role', 'alert');
                toastContainer.innerHTML = `
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
                document.body.appendChild(toastContainer);
                setTimeout(() => {
                    const bsAlert = bootstrap.Alert.getInstance(toastContainer);
                    if (bsAlert) {
                        bsAlert.close();
                    } else {
                        toastContainer.remove();
                    }
                }, 5000);
            }

            // --- LOGIC UNTUK EDIT PENGADUAN --- (Tidak banyak perubahan, hanya penyesuaian tampilan)
            const editPengaduanModal = document.getElementById('editPengaduanModal');
            const editPengaduanForm = document.getElementById('editPengaduanForm');
            const pengaduanIdInput = document.getElementById('pengaduanId');
            const modalKategoriInput = document.getElementById('modalKategori');
            const modalIsiPengaduanTextarea = document.getElementById('modalIsiPengaduan');
            const modalStatusSelect = document.getElementById('modalStatus');
            const fotoLaporanContainer = document.getElementById('fotoLaporanContainer');
            const modalFotoLaporanLink = document.getElementById('modalFotoLaporanLink');
            const modalFotoLaporanImage = document.getElementById('modalFotoLaporanImage');
            const printImageBtn = document.getElementById('printImageBtn');

            editPengaduanModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const kategori = button.getAttribute('data-kategori');
                const isiPengaduan = button.getAttribute('data-isi-pengaduan');
                const status = button.getAttribute('data-status');
                const fotoLaporanPath = button.getAttribute('data-foto-laporan-path');

                editPengaduanForm.action = `/data-pengaduan/${id}`; // Sesuaikan dengan route Anda

                pengaduanIdInput.value = id;
                modalKategoriInput.value = kategori;
                modalIsiPengaduanTextarea.value = isiPengaduan;
                modalStatusSelect.value = status;

                document.getElementById('isiPengaduanError').style.display = 'none';
                document.getElementById('statusError').style.display = 'none';
                document.getElementById('isiPengaduanError').textContent = '';
                document.getElementById('statusError').textContent = '';

                if (fotoLaporanPath && fotoLaporanPath !== 'null') {
                    const fullFotoUrl = `/storage/${fotoLaporanPath}`; // Assuming storage link is set
                    fotoLaporanContainer.style.display = 'block';

                    const imageExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.webp'];
                    const extension = fotoLaporanPath.split('.').pop().toLowerCase();
                    const isImage = imageExtensions.includes(extension);

                    if (isImage) {
                        modalFotoLaporanImage.src = fullFotoUrl;
                        modalFotoLaporanImage.style.display = 'block';
                        modalFotoLaporanLink.style.display = 'none';
                        printImageBtn.style.display = 'inline-block';
                    } else {
                        modalFotoLaporanLink.href = fullFotoUrl;
                        modalFotoLaporanLink.textContent = `Lihat Dokumen (${extension.toUpperCase()})`;
                        modalFotoLaporanLink.style.display = 'inline-block';
                        modalFotoLaporanImage.style.display = 'none';
                        printImageBtn.style.display = 'none';
                    }
                } else {
                    fotoLaporanContainer.style.display = 'none';
                    modalFotoLaporanImage.style.display = 'none';
                    modalFotoLaporanLink.style.display = 'none';
                    printImageBtn.style.display = 'none';
                }
            });

            editPengaduanForm.addEventListener('submit', function(event) {
                event.preventDefault();

                const formData = new FormData(this);
                const url = this.action;

                document.getElementById('isiPengaduanError').style.display = 'none';
                document.getElementById('statusError').style.display = 'none';

                fetch(url, {
                    method: 'POST', // Menggunakan POST karena @method('PUT') akan menanganinya
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json' // Penting agar Laravel mengembalikan JSON untuk validasi
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        // Jika respons bukan 2xx (misal 422 untuk validasi), kita perlu tangani
                        return response.json().then(errorData => {
                            throw errorData; // Lempar data error untuk ditangkap di .catch
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(editPengaduanModal);
                        modal.hide();
                        showToast(data.message, 'success');
                        setTimeout(() => {
                            window.location.reload(); // Reload page to reflect changes after toast is seen
                        }, 1000); // Tunggu sebentar agar toast terlihat
                    } else {
                        showToast(data.message || 'Terjadi kesalahan saat memperbarui.', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (error.errors) {
                        if (error.errors.isi_pengaduan) {
                            document.getElementById('isiPengaduanError').textContent = error.errors.isi_pengaduan[0];
                            document.getElementById('isiPengaduanError').style.display = 'block';
                        }
                        if (error.errors.status) {
                            document.getElementById('statusError').textContent = error.errors.status[0];
                            document.getElementById('statusError').style.display = 'block';
                        }
                        showToast('Gagal memperbarui pengaduan. Cek kembali form.', 'danger');
                    } else {
                        showToast(error.message || 'Terjadi kesalahan jaringan atau server.', 'danger');
                    }
                });
            });

            printImageBtn.addEventListener('click', function() {
                const imageUrl = modalFotoLaporanImage.src;
                const isImageVisible = modalFotoLaporanImage.style.display === 'block';

                if (imageUrl && isImageVisible) {
                    const printWindow = window.open('', '_blank');
                    printWindow.document.write(`
                        <html>
                        <head>
                            <title>Cetak Lampiran Gambar</title>
                            <style>
                                @page {
                                    size: auto;
                                    margin: 0mm;
                                }
                                body {
                                    margin: 0;
                                    padding: 0;
                                    display: flex;
                                    justify-content: center;
                                    align-items: center;
                                    min-height: 100vh;
                                    background-color: #f0f0f0;
                                }
                                img {
                                    max-width: 100vw;
                                    max-height: 100vh;
                                    object-fit: contain;
                                    display: block;
                                }
                            </style>
                        </head>
                        <body>
                            <img src="${imageUrl}" onload="window.print(); window.close();" alt="Lampiran Pengaduan">
                        </body>
                        </html>
                    `);
                    printWindow.document.close();
                } else {
                    alert('Tidak ada gambar yang dapat dicetak. Pastikan lampiran adalah file gambar.');
                }
            });

            // --- LOGIC UNTUK FILTER STATUS (BARU) ---
            const statusFilterRadios = document.querySelectorAll('input[name="pengaduan_filter_status"]');

            statusFilterRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    const selectedStatus = this.value;
                    let url = '{{ route("pengaduan.index_admin") }}'; // Sesuaikan dengan route indeks pengaduan Anda

                    // Jika status bukan 'all', tambahkan parameter query
                    if (selectedStatus !== 'all') {
                        url += `?status=${selectedStatus}`;
                    }

                    // Arahkan browser ke URL baru
                    window.location.href = url;
                });
            });
        });
    </script>
</body>
</html>