<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Laporan Tamu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f2f9ff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-bottom: 50px;
        }
        .header-top {
            background-color: #0d6efd;
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .header-top .title {
            font-size: 1.5rem;
            font-weight: 600;
        }
        .card {
            margin-bottom: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #0d6efd;
            color: white;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            padding: 15px 20px;
            font-weight: 600;
            font-size: 1.25rem;
        }
        .table-container {
            overflow-x: auto;
            margin-top: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
            color: #333;
        }
        table th,
        table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table thead th {
            background-color: #e9ecef;
            color: #495057;
            font-weight: 600;
        }
        table tbody tr:hover {
            background-color: #f8f9fa;
        }
        table tfoot td {
            font-weight: 700;
            background-color: #e9ecef;
            border-top: 2px solid #ddd;
        }
        .total-label {
            text-align: right;
            padding-right: 15px;
        }
        .total-value {
            text-align: left;
        }
        .form-control[type="date"], .form-select {
            border-radius: 8px;
            padding: 8px 12px;
            border: 1px solid #ced4da;
        }
        .select-container {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }
        .select-container label {
            margin-bottom: 0;
            font-weight: 500;
            color: #333;
            min-width: 80px;
        }
        .no-data-message {
            text-align: center;
            padding: 20px;
            color: #6c757d;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header-top">
        <button class="btn btn-light" onclick="history.back()" aria-label="Kembali">
            <i class="bi bi-arrow-left"></i>
        </button>
        <div class="title flex-grow-1 text-center me-4">Laporan Tamu</div>
        <div></div>
    </div>

    <div class="container mt-4">
        <h2 class="text-center mb-4 text-primary">Tabel Laporan Tamu</h2>

        {{-- Laporan Harian --}}
        <div class="card">
            <div class="card-header">Laporan Harian</div>
            <div class="card-body">
                <form id="dailyReportForm" action="{{ route('laporan.tamu.index') }}" method="GET">
                    <div class="select-container">
                        <label for="dailyDateInput">Pilih Tanggal:</label>
                        <input type="date" id="dailyDateInput" name="daily_date" class="form-control flex-grow-1"
                               value="{{ $selectedDailyDate }}"
                               onchange="this.form.submit()"> {{-- Submit form saat tanggal berubah --}}
                    </div>
                    {{-- Tambahkan input tersembunyi untuk menjaga nilai filter lain --}}
                    <input type="hidden" name="monthly_year" value="{{ $selectedMonthlyYear }}">
                    <input type="hidden" name="monthly_month" value="{{ $selectedMonthlyMonth }}">
                    <input type="hidden" name="yearly_year" value="{{ $selectedYearlyYear }}">
                </form>

                @if (!empty($laporanHarian['data']))
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama Tamu</th>
                                    <th>Waktu Masuk</th>
                                    <th>Waktu Keluar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($laporanHarian['data'] as $index => $tamu)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $tamu->nama_tamu }}</td>
                                        <td>{{ date('H:i', strtotime($tamu->waktu_masuk)) }}</td>
                                        <td>
                                            @if ($tamu->waktu_keluar)
                                                {{ date('H:i', strtotime($tamu->waktu_keluar)) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="total-label">
                                        Total Keseluruhan Tamu Harian:
                                    </td>
                                    <td class="total-value">{{ $laporanHarian['total_tamu'] ?? 0 }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="no-data-message">
                        Tidak ada data harian untuk tanggal ini.
                    </div>
                @endif
            </div>
        </div>

        {{-- Rekap Mingguan --}}
        <div class="card">
            <div class="card-header">Rekap Mingguan</div>
            <div class="card-body">
                @if (!empty($rekapMingguan['data']))
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Minggu ke-</th>
                                    <th>Total Tamu Masuk</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rekapMingguan['data'] as $item)
                                    <tr>
                                        <td>{{ $item->minggu_ke }}</td>
                                        <td>{{ $item->total_tamu_mingguan }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="total-label">Total Keseluruhan Tamu Mingguan:</td>
                                    <td class="total-value">{{ $totalTamuMingguan ?? 0 }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="no-data-message">
                        Tidak ada data rekap mingguan.
                    </div>
                @endif
            </div>
        </div>

        {{-- Rekap Bulanan --}}
        <div class="card">
            <div class="card-header">Rekap Bulanan</div>
            <div class="card-body">
                <form id="monthlyReportForm" action="{{ route('laporan.tamu.index') }}" method="GET">
                    <div class="select-container">
                        <label for="monthlyYearSelect">Pilih Tahun:</label>
                        <select id="monthlyYearSelect" name="monthly_year" class="form-select" onchange="this.form.submit()">
                            @php
                                $currentYear = date('Y');
                                $startYear = $currentYear - 10;
                                $endYear = $currentYear + 5;
                            @endphp
                            @for ($year = $startYear; $year <= $endYear; $year++)
                                <option value="{{ $year }}" {{ $year == $selectedMonthlyYear ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="select-container">
                        <label for="monthlyMonthSelect">Pilih Bulan:</label>
                        <select id="monthlyMonthSelect" name="monthly_month" class="form-select" onchange="this.form.submit()">
                            @php
                                $months = [
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                ];
                            @endphp
                            @foreach ($months as $num => $name)
                                <option value="{{ $num }}" {{ $num == $selectedMonthlyMonth ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Tambahkan input tersembunyi untuk menjaga nilai filter lain --}}
                    <input type="hidden" name="daily_date" value="{{ $selectedDailyDate }}">
                    <input type="hidden" name="yearly_year" value="{{ $selectedYearlyYear }}">
                </form>

                @if (!empty($rekapBulanan['data']))
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Bulan</th>
                                    <th>Total Tamu Masuk</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rekapBulanan['data'] as $item)
                                    <tr>
                                        <td>{{ $item->bulan_nama }}</td>
                                        <td>{{ $item->total_tamu_masuk }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="total-label">Total Tamu Bulanan:</td>
                                    <td class="total-value">{{ $totalTamuBulanan ?? 0 }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="no-data-message">
                        Tidak ada data rekap bulanan untuk bulan dan tahun ini.
                    </div>
                @endif
            </div>
        </div>

        {{-- Rekap Tahunan --}}
        <div class="card">
            <div class="card-header">Rekap Tahunan</div>
            <div class="card-body">
                <form id="yearlyReportForm" action="{{ route('laporan.tamu.index') }}" method="GET">
                    <div class="select-container">
                        <label for="yearlyYearSelect">Pilih Tahun:</label>
                        <select id="yearlyYearSelect" name="yearly_year" class="form-select" onchange="this.form.submit()">
                            @php
                                $currentYear = date('Y');
                                $startYear = $currentYear - 10;
                                $endYear = $currentYear + 5;
                            @endphp
                            @for ($year = $startYear; $year <= $endYear; $year++)
                                <option value="{{ $year }}" {{ $year == $selectedYearlyYear ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    {{-- Tambahkan input tersembunyi untuk menjaga nilai filter lain --}}
                    <input type="hidden" name="daily_date" value="{{ $selectedDailyDate }}">
                    <input type="hidden" name="monthly_year" value="{{ $selectedMonthlyYear }}">
                    <input type="hidden" name="monthly_month" value="{{ $selectedMonthlyMonth }}">
                </form>

                @if (!empty($rekapTahunan['data']))
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Tahun</th>
                                    <th>Total Tamu Masuk</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rekapTahunan['data'] as $item)
                                    <tr>
                                        <td>{{ $item->tahun }}</td>
                                        <td>{{ $item->total_tamu_masuk }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="total-label">Total Keseluruhan Tamu Tahunan:</td>
                                    <td class="total-value">{{ $totalTamuTahunan ?? 0 }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="no-data-message">
                        Tidak ada data rekap tahunan untuk tahun ini.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fungsi handleRefresh tidak lagi relevan jika tidak ada AJAX.
        // Refresh penuh dilakukan saat form disubmit.
        // Anda bisa menaruh logika ini jika suatu saat ingin memuat ulang seluruh halaman secara paksa.
        function handleRefresh() {
            window.location.reload(); // Memuat ulang halaman penuh
        }
    </script>
</body>
</html>