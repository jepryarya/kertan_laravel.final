<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Data Pengajuan Surat Warga - Admin RT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* ... (CSS Anda yang sudah ada, tidak perlu diubah) ... */
        body {
            background-color: #f8f9fa;
        }
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
        /* Gaya untuk tabel yang lebih modern */
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
        .status-badge {
            padding: 0.4em 0.8em;
            border-radius: 0.375rem;
            font-weight: bold;
            display: inline-block;
        }
        .status-menunggu { background-color: #ffc107; color: #343a40; } /* Kuning */
        .status-disetujui { background-color: #28a745; color: white; } /* Hijau */
        .status-ditolak { background-color: #dc3545; color: white; }    /* Merah */

        /* CSS untuk thumbnail gambar di tabel */
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
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
            <a class="navbar-brand" href="#">Data Pengajuan Surat Warga</a>
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
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <h2 class="mb-4">Daftar Pengajuan Surat Warga</h2>

        <div class="table-container">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama Warga</th>
                        <th>Jenis Surat</th>
                        <th>Keterangan</th>
                        <th>Status</th>
                        <th>Diajukan Pada</th>
                        <th>Lampiran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengajuans as $index => $pengajuan)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $pengajuan->warga->nama ?? 'N/A' }}</td>
                            <td>{{ Str::title(str_replace('_', ' ', $pengajuan->jenis_surat)) }}</td>
                            <td>{{ $pengajuan->keterangan ?? '-' }}</td>
                            <td>
                                <span class="status-badge status-{{ $pengajuan->status }}">
                                    {{ Str::title($pengajuan->status) }}
                                </span>
                            </td>
                            <td>{{ $pengajuan->created_at->format('d-m-Y H:i') }}</td>
                            <td>
                                @if ($pengajuan->attachment_url) {{-- Cek apakah ada URL lampiran --}}
                                    @php
                                        $extension = pathinfo($pengajuan->attachment_url, PATHINFO_EXTENSION);
                                        $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                    @endphp

                                    @if ($isImage)
                                        <a href="{{ $pengajuan->attachment_url }}" target="_blank" title="Lihat Gambar Lampiran">
                                            <img src="{{ $pengajuan->attachment_url }}" alt="Lampiran Surat" class="thumbnail-img">
                                        </a>
                                    @else {{-- Asumsikan PDF atau jenis lain jika bukan gambar --}}
                                        <a href="{{ $pengajuan->attachment_url }}" target="_blank" class="attachment-link" title="Lihat Dokumen Lampiran">
                                            <i class="fas fa-file-pdf thumbnail-icon"></i> {{-- Icon PDF dari Font Awesome --}}
                                        </a>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info text-white btn-edit-pengajuan"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editPengajuanModal"
                                    data-id="{{ $pengajuan->id }}"
                                    data-jenis-surat="{{ $pengajuan->jenis_surat }}"
                                    data-keterangan="{{ $pengajuan->keterangan }}"
                                    data-status="{{ $pengajuan->status }}"
                                    data-attachment="{{ $pengajuan->foto_surat_path ?? '' }}"> {{-- Gunakan foto_surat_path untuk modal --}}
                                    Detail / Edit
                                </button>

                                <form action="{{ route('pengajuan.destroy', $pengajuan->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus pengajuan ini? Tindakan ini tidak dapat dibatalkan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada pengajuan surat yang masuk.</td> {{-- Sesuaikan colspan --}}
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <footer class="bg-light text-center py-3 mt-5">
        <div class="container">
            <span class="text-muted">© {{ date('Y') }} Sistem Pendataan Warga</span>
        </div>
    </footer>

    <div class="modal fade" id="editPengajuanModal" tabindex="-1" aria-labelledby="editPengajuanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPengajuanModalLabel">Detail & Edit Pengajuan Surat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editPengajuanForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" id="pengajuanId" name="pengajuan_id">

                        <div class="mb-3">
                            <label for="modalJenisSurat" class="form-label">Jenis Surat</label>
                            <input type="text" class="form-control" id="modalJenisSurat" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="modalKeterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="modalKeterangan" name="keterangan" rows="3"></textarea>
                            <div id="keteranganError" class="text-danger" style="display: none;"></div>
                        </div>

                        <div class="mb-3">
                            <label for="modalStatus" class="form-label">Status</label>
                            <select class="form-select" id="modalStatus" name="status" required>
                                <option value="menunggu">Menunggu</option>
                                <option value="disetujui">Disetujui</option>
                                <option value="ditolak">Ditolak</option>
                            </select>
                            <div id="statusError" class="text-danger" style="display: none;"></div>
                        </div>

                        {{-- CONTAINER UNTUK MENAMPILKAN LAMPIRAN SAAT INI --}}
                        <div class="mb-3" id="attachmentContainer" style="display: none;">
                            <label class="form-label">Lampiran Saat Ini:</label>
                            <div id="modalAttachmentPreview">
                                <a id="modalAttachmentLink" href="#" target="_blank" class="btn btn-sm btn-secondary mb-2" style="display: none;">Lihat Lampiran</a>
                                <img id="modalAttachmentImage" src="" alt="Lampiran Surat" class="img-fluid rounded" style="max-width: 100%; height: auto; display: none;">
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <button type="button" class="btn btn-info" id="printImageBtn">Cetak Gambar Lampiran</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editPengajuanModal = document.getElementById('editPengajuanModal');
            const editPengajuanForm = document.getElementById('editPengajuanForm');
            const pengajuanIdInput = document.getElementById('pengajuanId');
            const modalJenisSuratInput = document.getElementById('modalJenisSurat');
            const modalKeteranganTextarea = document.getElementById('modalKeterangan');
            const modalStatusSelect = document.getElementById('modalStatus');
            const attachmentContainer = document.getElementById('attachmentContainer');
            const modalAttachmentLink = document.getElementById('modalAttachmentLink');
            const modalAttachmentImage = document.getElementById('modalAttachmentImage');
            const printImageBtn = document.getElementById('printImageBtn'); // Nama variabel diubah menjadi printImageBtn

            // Listen for when the modal is about to be shown
            editPengajuanModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const jenisSurat = button.getAttribute('data-jenis-surat');
                const keterangan = button.getAttribute('data-keterangan');
                const status = button.getAttribute('data-status');
                const attachmentPath = button.getAttribute('data-attachment');

                pengajuanIdInput.value = id;
                modalJenisSuratInput.value = jenisSurat.replace(/_/g, ' ').replace(/\b\w/g, s => s.toUpperCase());
                modalKeteranganTextarea.value = keterangan;
                modalStatusSelect.value = status;
                editPengajuanForm.action = `/data-pengajuan-surat/${id}`;

                // Mengatur tampilan lampiran yang sudah ada di MODAL
                if (attachmentPath) {
                    const fullAttachmentUrl = `/storage/${attachmentPath}`; // Path relatif dari public/storage
                    attachmentContainer.style.display = 'block';

                    const imageExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.webp'];
                    const extension = attachmentPath.split('.').pop();
                    const isImage = imageExtensions.some(ext => attachmentPath.toLowerCase().endsWith(ext));

                    if (isImage) {
                        modalAttachmentImage.src = fullAttachmentUrl;
                        modalAttachmentImage.style.display = 'block';
                        modalAttachmentLink.style.display = 'none';
                        printImageBtn.style.display = 'inline-block'; // Tampilkan tombol "Cetak Gambar Lampiran"
                    } else { // Jika bukan gambar (misal PDF)
                        modalAttachmentLink.href = fullAttachmentUrl;
                        modalAttachmentLink.textContent = `Lihat Lampiran (${extension.toUpperCase()})`;
                        modalAttachmentLink.style.display = 'inline-block';
                        modalAttachmentImage.style.display = 'none';
                        printImageBtn.style.display = 'none'; // Sembunyikan tombol ini jika bukan gambar
                    }
                } else {
                    attachmentContainer.style.display = 'none';
                    modalAttachmentImage.style.display = 'none';
                    modalAttachmentLink.style.display = 'none';
                    printImageBtn.style.display = 'none'; // Sembunyikan juga jika tidak ada lampiran
                }

                // Clear previous errors
                document.getElementById('keteranganError').style.display = 'none';
                document.getElementById('statusError').style.display = 'none';
                document.getElementById('keteranganError').textContent = '';
                document.getElementById('statusError').textContent = '';
            });

            // Handle form submission via AJAX
            editPengajuanForm.addEventListener('submit', function(event) {
                event.preventDefault();

                const formData = new FormData(this);
                const url = this.action;

                document.getElementById('keteranganError').style.display = 'none';
                document.getElementById('statusError').style.display = 'none';
                document.getElementById('keteranganError').textContent = '';
                document.getElementById('statusError').textContent = '';

                fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : ''
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(editPengajuanModal);
                        modal.hide();
                        showToast(data.message, 'success');
                        window.location.reload();
                    } else if (data.errors) {
                        if (data.errors.keterangan) {
                            document.getElementById('keteranganError').textContent = data.errors.keterangan[0];
                            document.getElementById('keteranganError').style.display = 'block';
                        }
                        if (data.errors.status) {
                            document.getElementById('statusError').textContent = data.errors.status[0];
                            document.getElementById('statusError').style.display = 'block';
                        }
                        showToast('Gagal memperbarui pengajuan. Cek kembali form.', 'danger');
                    } else {
                        showToast(data.message || 'Terjadi kesalahan saat memperbarui.', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Terjadi kesalahan jaringan atau server.', 'danger');
                });
            });

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
                    toastContainer.classList.remove('show');
                    toastContainer.classList.add('hide');
                    toastContainer.remove();
                }, 5000);
            }

            const style = document.createElement('style');
            style.innerHTML = `
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
            `;
            document.head.appendChild(style);

            // Fungsi untuk mencetak gambar di jendela baru (tanpa memunculkan keterangan)
            printImageBtn.addEventListener('click', function() {
                const imageUrl = modalAttachmentImage.src;
                const isImageVisible = modalAttachmentImage.style.display === 'block';

                if (imageUrl && isImageVisible) {
                    const printWindow = window.open('', '_blank');
                    printWindow.document.write(`
                        <html>
                        <head>
                            <title>Cetak Lampiran Gambar</title>
                            <style>
                                /* Opsional: Atur gaya untuk cetak agar gambar mengisi halaman */
                                @page {
                                    size: auto;
                                    margin: 0mm; /* Hapus margin halaman */
                                }
                                body {
                                    margin: 0;
                                    padding: 0;
                                    display: flex;
                                    justify-content: center;
                                    align-items: center;
                                    min-height: 100vh;
                                    background-color: #f0f0f0; /* Latar belakang untuk tampilan layar */
                                }
                                img {
                                    max-width: 100vw; /* Lebar maksimal viewport */
                                    max-height: 100vh; /* Tinggi maksimal viewport */
                                    object-fit: contain; /* Memastikan gambar tidak terdistorsi */
                                    display: block; /* Menghilangkan spasi ekstra di bawah gambar */
                                }
                            </style>
                        </head>
                        <body>
                            <img src="${imageUrl}" onload="window.print(); window.close();" alt="Lampiran Surat">
                        </body>
                        </html>
                    `);
                    printWindow.document.close();
                    // printWindow.focus(); // Fokuskan jendela baru
                    // printWindow.print(); // Otomatis buka dialog cetak
                    // printWindow.close(); // Tutup jendela setelah cetak (beberapa browser mungkin meminta konfirmasi)
                } else {
                    alert('Tidak ada gambar yang dapat dicetak. Pastikan lampiran adalah file gambar.');
                }
            });
        });
    </script>
</body>
</html>