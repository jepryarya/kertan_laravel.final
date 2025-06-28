<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Beranda RT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* Existing styles remain unchanged */
        body {
            background-color: #f2f9ff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
            padding-bottom: 100px;
        }
        .header-container {
            background-color: #0d6efd;
            color: white;
            padding: 0;
            display: flex;
            flex-direction: column;
            width: 100%;
        }
        .header-top {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            background-color: #0d6efd;
            height: 60px;
        }
        .header-top img {
            height: 40px;
            margin-right: 15px;
        }
        .header-title-wrapper {
            overflow: hidden;
            white-space: nowrap;
            flex: 1;
            position: relative;
        }
        .header-title-text {
            display: inline-block;
            padding-left: 100%;
            animation: scrollText 10s linear infinite;
            font-size: 1.2rem;
            font-weight: 600;
            color: white;
        }
        @keyframes scrollText {
            0% { transform: translateX(0%); }
            100% { transform: translateX(-100%); }
        }
        .header-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: #0d6efd;
            height: 50px;
        }
        .left-section {
            display: flex;
            align-items: center;
        }
        .current-time {
            color: white;
            font-size: 0.9rem;
            font-weight: 400;
            margin-right: 20px;
        }
        .right-section {
            display: flex;
            align-items: center;
        }
        .search-container {
            position: relative;
            width: 200px;
            border-radius: 25px;
            background: linear-gradient(135deg, #e6f0fa 0%, #d0e1f9 100%);
            padding: 5px;
            display: flex;
            align-items: center;
            margin-right: 20px;
        }
        .search-container::before {
            content: "";
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(135deg, #f0f4f9 0%, #e0e8f5 100%);
            border-radius: 27px;
            z-index: -1;
        }
        .search-container::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, #a3ceff 0%, #d3e8ff 100%);
            border-radius: 25px;
            z-index: -2;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .input {
            padding: 5px 15px;
            width: 100%;
            background: transparent;
            border: none;
            color: #666;
            font-size: 16px;
            border-radius: 20px;
        }
        .input:focus {
            outline: none;
            color: #333;
        }
        .search__icon {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
        }
        .icon-user {
            font-size: 45px;
            color: white;
            cursor: pointer;
        }
        .summary-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 20px 0;
            flex-wrap: wrap;
        }
        .summary-card {
            background: linear-gradient(135deg, #e6f0fa 0%, #d0e1f9 100%);
            border-radius: 15px;
            padding: 15px 25px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            min-width: 200px;
            transition: transform 0.3s ease;
        }
        .summary-card:hover {
            transform: translateY(-5px);
        }
        .summary-card h5 {
            margin: 0;
            font-size: 1.1rem;
            color: #0d6efd;
            font-weight: 600;
        }
        .summary-card p {
            margin: 5px 0 0;
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
        }
        h4.section-title {
            margin: 30px 0 15px 20px;
            font-weight: 700;
            color: #0d6efd;
        }
        .data-count {
            margin-left: 20px;
            font-size: 14px;
            color: #555;
            font-weight: 700;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .carousel-inner {
            padding: 10px 0 20px 0;
        }
        .card {
            width: 290px;
            height: 404px;
            background: linear-gradient(10deg, rgba(38,146,255,1) 0%, rgba(0,63,255,1) 100%);
            border-radius: 16px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10.5px);
            -webkit-backdrop-filter: blur(10.5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            margin: 10px;
        }
        .top {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10.5px);
            -webkit-backdrop-filter: blur(10.5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            width: 290px;
            height: 300px;
            position: relative;
        }
        .top .title {
            color: #fff;
            font-weight: bolder;
            font-size: x-large;
            margin-left: 10px;
            margin-top: 90%;
        }
        .card .desc {
            color: #fff;
            opacity: 75%;
            font-size: small;
            font-weight: lighter;
            margin-left: 10px;
            margin-top: 2%;
        }
        .card-image {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
        }
        .carousel-item .d-flex {
            justify-content: center;
            gap: 15px;
        }
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        .action-button {
            background-color: #e6f1ff;
            border-radius: 10px;
            padding: 20px;
            width: 150px;
            text-align: center;
            font-weight: 500;
            color: #0d6efd;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
            user-select: none;
        }
        .action-button:hover {
            background-color: #d0e8ff;
            transform: translateY(-3px);
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
            gap: 10px;
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
        .sidebar-profile a.menu-item.edit-profil-rt {
            background-color: #28a745;
            color: white;
        }
        .sidebar-profile a.menu-item.edit-profil-rt:hover {
            background-color: #218838;
        }
        .sidebar-profile form.logout-form {
            margin-top: 0px;
        }
        .sidebar-profile form.logout-form button.menu-item {
            background-color: #dc3545;
            color: white;
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
            width: 220px;
        }
        .sidebar-profile form.logout-form button.menu-item:hover {
            background-color: #c82333;
            color: white;
        }
        .mt-auto {
            margin-top: auto !important;
        }
        .settings-panel {
            position: fixed;
            bottom: 50px;
            right: 20px;
            background-color: #0d6efd;
            width: 160px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            padding: 8px;
            display: none;
            flex-direction: column;
            gap: 8px;
            z-index: 10;
            color: white;
            user-select: none;
        }
        .settings-panel.open {
            display: flex;
        }
        .settings-panel button.menu-item {
            background-color: #e6f1ff;
            color: #0d6efd;
            border: none;
            border-radius: 10px;
            padding: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-align: center;
        }
        .settings-panel button.menu-item:hover {
            background-color: #d0e8ff;
        }
        .settings-icon {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #e6f1ff;
            border-radius: 50%;
            padding: 10px;
            cursor: pointer;
            z-index: 1100;
            user-select: none;
        }
        .lihat-button {
            border: none;
            color: #fff;
            background-image: linear-gradient(30deg, #0400ff, #4ce3f7);
            border-radius: 20px;
            background-size: 100% auto;
            font-family: inherit;
            font-size: 17px;
            padding: 0.6em 1.5em;
            margin-left: 20px;
            margin-top: 10px;
            cursor: pointer;
        }
        .lihat-button:hover {
            background-position: right center;
            background-size: 200% auto;
            -webkit-animation: pulse 2s infinite;
            animation: pulse512 1.5s infinite;
        }
        @keyframes pulse512 {
            0% { box-shadow: 0 0 0 0 #05d666; }
            70% { box-shadow: 0 0 0 10px rgb(218 103 68 / 0%); }
            100% { box-shadow: 0 0 0 0 rgb(218 103 68 / 0%); }
        }
        @media (max-width: 768px) {
            .header-top {
                flex-direction: row;
                justify-content: space-between;
            }
            .header-title-text {
                font-size: 1rem;
            }
            .header-bottom {
                flex-direction: column;
                align-items: flex-start;
            }
            .left-section {
                margin-bottom: 10px;
            }
            .right-section {
                align-self: flex-end;
            }
            .search-container {
                width: 150px;
                margin-bottom: 10px;
            }
            .icon-user {
                font-size: 35px;
            }
            .carousel-item .d-flex {
                flex-wrap: wrap;
                justify-content: center;
            }
        }
        .radio-inputs {
            position: relative;
            display: flex;
            flex-wrap: nowrap;
            border-radius: 1rem;
            background: linear-gradient(145deg, #e6e6e6, #ffffff);
            box-sizing: border-box;
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.15), -5px -5px 15px rgba(255, 255, 255, 0.8);
            padding: 0.5rem;
            width: 300px;
            font-size: 14px;
            gap: 0.5rem;
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
            padding: 0.7rem 0;
            color: #2d3748;
            font-weight: 500;
            font-family: inherit;
            background: linear-gradient(145deg, #ffffff, #e6e6e6);
            box-shadow: 3px 3px 6px rgba(0, 0, 0, 0.1), -3px -3px 6px rgba(255, 255, 255, 0.7);
            transition: all 0.2s ease;
            overflow: hidden;
            width: 100%;
            height: 40px;
            font-size: 16px;
        }
        .radio-inputs .radio input:checked + .name {
            background: linear-gradient(145deg, #0d6efd, #0056b3);
            color: white;
            font-weight: 600;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
            box-shadow: inset 2px 2px 5px rgba(0, 0, 0, 0.2), inset -2px -2px 5px rgba(255, 255, 255, 0.1), 3px 3px 8px rgba(13, 110, 253, 0.3);
            transform: translateY(2px);
        }
        .radio-inputs .radio:hover .name {
            background: linear-gradient(145deg, #f0f0f0, #ffffff);
            transform: translateY(-1px);
            box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.1), -4px -4px 8px rgba(255, 255, 255, 0.8);
        }
        .radio-inputs .radio:hover input:checked + .name {
            transform: translateY(1px);
        }
        .radio-inputs .radio input:checked + .name {
            animation: select 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .radio-inputs .radio .name::before,
        .radio-inputs .radio .name::after {
            content: "";
            position: absolute;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            opacity: 0;
            pointer-events: none;
        }
        .radio-inputs .radio input:checked + .name::before,
        .radio-inputs .radio input:checked + .name::after {
            animation: particles 0.8s ease-out forwards;
        }
        .radio-inputs .radio .name::before {
            background: #60a5fa;
            box-shadow: 0 0 6px #60a5fa;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
        }
        .radio-inputs .radio .name::after {
            background: #93c5fd;
            box-shadow: 0 0 8px #93c5fd;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
        }
        .radio-inputs .radio .name::after {
            content: "";
            position: absolute;
            inset: 0;
            z-index: -1;
            background: radial-gradient(circle at var(--x, 50%) var(--y, 50%), rgba(59, 130, 246, 0.3) 0%, transparent 50%);
            opacity: 0;
            transition: opacity 0.3s;
        }
        .radio-inputs .radio input:checked + .name::after {
            opacity: 1;
            animation: sparkle-bg 1s ease-out forwards;
        }
        .radio-inputs .radio input:checked + .name {
            overflow: visible;
        }
        .radio-inputs .radio input:checked + .name::before {
            box-shadow: 0 0 6px #60a5fa, 10px -10px 0 #60a5fa, -10px -10px 0 #60a5fa;
            animation: multi-particles-top 0.8s ease-out forwards;
        }
        .radio-inputs .radio input:checked + .name::after {
            box-shadow: 0 0 8px #93c5fd, 10px 10px 0 #93c5fd, -10px 10px 0 #93c5fd;
            animation: multi-particles-bottom 0.8s ease-out forwards;
        }
        @keyframes select {
            0% { transform: scale(0.95) translateY(2px); }
            50% { transform: scale(1.05) translateY(-1px); }
            100% { transform: scale(1) translateY(2px); }
        }
        @keyframes multi-particles-top {
            0% { opacity: 1; transform: translateX(-50%) translateY(0) scale(1); }
            40% { opacity: 0.8; }
            100% { opacity: 0; transform: translateX(-50%) translateY(-20px) scale(0); box-shadow: 0 0 6px transparent, 20px -20px 0 transparent, -20px -20px 0 transparent; }
        }
        @keyframes multi-particles-bottom {
            0% { opacity: 1; transform: translateX(-50%) translateY(0) scale(1); }
            40% { opacity: 0.8; }
            100% { opacity: 0; transform: translateX(-50%) translateY(20px) scale(0); box-shadow: 0 0 8px transparent, 20px 20px 0 transparent, -20px 20px 0 transparent; }
        }
        @keyframes sparkle-bg {
            0% { opacity: 0; transform: scale(0.2); }
            50% { opacity: 1; }
            100% { opacity: 0; transform: scale(2); }
        }
        .radio-inputs .radio .name::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: radial-gradient(circle at var(--x, 50%) var(--y, 50%), rgba(255, 255, 255, 0.5) 0%, transparent 50%);
            opacity: 0;
            transition: opacity 0.3s;
        }
        .radio-inputs .radio input:checked + .name::before {
            animation: ripple 0.8s ease-out;
        }
        @keyframes ripple {
            0% { opacity: 1; transform: scale(0.2); }
            50% { opacity: 0.5; }
            100% { opacity: 0; transform: scale(2.5); }
        }
        .radio-inputs .radio input:checked + .name {
            position: relative;
        }
        .radio-inputs .radio input:checked + .name::after {
            content: "";
            position: absolute;
            inset: -2px;
            border-radius: inherit;
            background: linear-gradient(45deg, rgba(59, 130, 246, 0.5), rgba(37, 99, 235, 0.5));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            animation: border-glow 1.5s ease-in-out infinite alternate;
        }
        @keyframes border-glow {
            0% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        .user-info-display {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 15px 20px;
            margin: 20px 20px 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            color: #333;
            font-size: 1rem;
            text-align: center;
        }
        .user-info-display p {
            margin: 0;
            line-height: 1.5;
        }
        .user-info-display strong {
            color: #0d6efd;
        }

        .fab-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: linear-gradient(45deg, #0d6efd, #0056b3);
            color: white;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            z-index: 1000;
            text-decoration: none;
            border: none;
        }

        .fab-button:hover {
            background: linear-gradient(45deg, #0056b3, #0d6efd);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        @media (max-width: 768px) {
            .fab-button {
                width: 50px;
                height: 50px;
                font-size: 24px;
                bottom: 15px;
                right: 15px;
            }
        }
        
    </style>
</head>
<body>
    <div class="header-container">
        <div class="header-top">
            <img src="{{ asset('gambar/logo_kertan2.png') }}" alt="Logo Kertan" onerror="this.onerror=null;this.src='https://placehold.co/40x40/0d6efd/ffffff?text=Logo';" />
            <div class="header-title-wrapper">
                <div class="header-title-text">Melayani Warga Adalah Kewajiban Bagi Seluruh Kepengurusan Ke-RTAN</div>
            </div>
        </div>
        <div class="header-bottom">
            <div class="left-section">
                <div class="current-time" id="currentDateTime"></div>
            </div>
            <div class="right-section">
                <div class="search-container">
                    <input type="text" class="input" id="searchInput" placeholder="Cari nama..." aria-label="Cari nama warga atau satpam">
                    <div class="search__icon">
                        <i class="bi bi-search"></i>
                    </div>
                </div>
                <i class="bi bi-person-circle icon-user" id="profileIcon" title="Profil" tabindex="0" role="button" aria-pressed="false" aria-label="Buka sidebar profil"></i>
            </div>
        </div>
    </div>

    @if (isset($user))
        <div class="user-info-display">
            <p>Selamat datang, <strong>{{ $user->name }}</strong>!</p>
            <p>Anda login sebagai Admin dengan ID: {{ $user->id }}</p>
        </div>
    @endif

    <div class="summary-container">
        <div class="summary-card">
            <h5>Total Warga</h5>
            <p id="totalWarga">0</p>
        </div>
        <div class="summary-card">
            <h5>Total Satpam</h5>
            <p id="totalSatpam">0</p>
        </div>
    </div>
    <div class="action-buttons" style="display: flex; justify-content: center; gap: 15px; margin-top: 20px; flex-wrap: wrap;">
        <div class="radio-inputs">
            <label class="radio">
            <input name="radio" type="radio" onclick="window.location='{{ route('pengajuan.index') }}'" />
                <span class="name">Data Pengajuan</span>
            </label>
            <label class="radio">
                <input name="radio" type="radio" onclick="window.location='{{ route('laporan.tamu.index') }}'" />
                <span class="name">Lihat Tamu</span>
            </label>
            <label class="radio">
                 <input name="radio" type="radio" onclick="window.location='{{ route('pengaduan.index_admin') }}'" />
                <span class="name">Data Lingkungan</span>
            </label>
        </div>
    </div>

    <h4 class="section-title" id="judulWarga">Data Warga</h4>
    <span class="data-count" id="wargaCount">Warga: 0 Terdaftar</span>
    <div id="carouselWarga" class="carousel slide" data-bs-ride="carousel" aria-label="Carousel Data Warga">
        <div class="carousel-inner" id="carousel-warga-content"></div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselWarga" data-bs-slide="prev" aria-label="Slide sebelumnya">
            <span class="carousel-control-prev-icon bg-dark rounded-circle" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselWarga" data-bs-slide="next" aria-label="Slide berikutnya">
            <span class="carousel-control-next-icon bg-dark rounded-circle" aria-hidden="true"></span>
        </button>
    </div>
    <a href="{{ route('data.warga') }}">
        <button class="lihat-button">LIHAT SEMUA DATA WARGA</button>
    </a>

    <h4 class="section-title" id="judulSatpam">Data Satpam</h4>
    <span class="data-count" id="satpamCount">Satpam: 0 Terdaftar</span>
    <div id="carouselSatpam" class="carousel slide" data-bs-ride="carousel" aria-label="Carousel Data Satpam">
        <div class="carousel-inner" id="carousel-satpam-content"></div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselSatpam" data-bs-slide="prev" aria-label="Slide sebelumnya">
            <span class="carousel-control-prev-icon bg-dark rounded-circle" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselSatpam" data-bs-slide="next" aria-label="Slide berikutnya">
            <span class="carousel-control-next-icon bg-dark rounded-circle" aria-hidden="true"></span>
        </button>
    </div>
    <a href="{{ route('data.satpam') }}">
        <button class="lihat-button">LIHAT SEMUA DATA SATPAM</button>
    </a>

    <div id="sidebarProfile" class="sidebar-profile" aria-hidden="true" aria-label="Sidebar Profil">
        <button class="close-btn" id="closeSidebarProfile" aria-label="Tutup sidebar profil">×</button>
        <a href="{{ route('rt.create') }}" class="menu-item edit-profil-rt">Edit Profil RT</a>
        <a href="{{ route('akun.satpam') }}" class="menu-item">Tambah Data Satpam</a>
        <a href="{{ route('akun.warga') }}" class="menu-item">Tambah Data Warga</a>
        <form action="{{ route('logout') }}" method="POST" class="logout-form">
            @csrf
            <button type="submit" class="menu-item">Logout</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const warga = @json($warga);
        const satpam = @json($satpam);
        const authUserId = {{ auth()->id() }};
        const itemsPerSlide = 4;

        function createSlides(dataArray, containerId, isSatpam = false) {
            const container = document.getElementById(containerId);
            container.innerHTML = '';
            if (!dataArray || dataArray.length === 0) {
                const noDataDiv = document.createElement('div');
                noDataDiv.className = 'carousel-item active';
                noDataDiv.innerHTML = `
                    <div class="d-flex justify-content-center">
                        <div class="card">
                            <div class="top">
                                <div class="title">Tidak ada data</div>
                            </div>
                            <p class="desc">Data tidak ditemukan</p>
                        </div>
                    </div>
                `;
                container.appendChild(noDataDiv);
                return;
            }
            for (let i = 0; i < dataArray.length; i += itemsPerSlide) {
                const slice = dataArray.slice(i, i + itemsPerSlide);
                const activeClass = i === 0 ? 'active' : '';
                const carouselItem = document.createElement('div');
                carouselItem.className = `carousel-item ${activeClass}`;
                const dFlex = document.createElement('div');
                dFlex.className = 'd-flex justify-content-center';
                slice.forEach(person => {
                    const card = document.createElement('div');
                    card.className = 'card';
                    const imageUrl = isSatpam ? '{{ asset("gambar/satpam.jpg") }}' : '{{ asset("gambar/warga.jpg") }}';
                    const fallbackImageUrl = `https://placehold.co/150x150/0d6efd/ffffff?text=${isSatpam ? 'Satpam' : 'Warga'}`;

                    if (!isSatpam) {
                        const alamat = person.alamat_rumah || '';
                        const noRumah = person.no_rumah || '';
                        card.innerHTML = `
                            <div class="top">
                                <img src="${imageUrl}" alt="Warga Image" class="card-image" onerror="this.onerror=null;this.src='${fallbackImageUrl}';" />
                                <div class="title">${person.nama}</div>
                            </div>
                            <p class="desc">${alamat ? `${alamat}, ` : ''}${noRumah ? `No. ${noRumah}<br>` : ''}HP: ${person.no_hp}</p>
                        `;
                    } else {
                        card.innerHTML = `
                            <div class="top">
                                <img src="${imageUrl}" alt="Satpam Image" class="card-image" onerror="this.onerror=null;this.src='${fallbackImageUrl}';" />
                                <div class="title">${person.nama}</div>
                            </div>
                            <p class="desc">HP: ${person.no_hp}<br>Shift: ${person.shift}</p>
                        `;
                    }
                    dFlex.appendChild(card);
                });
                carouselItem.appendChild(dFlex);
                container.appendChild(carouselItem);
            }
        }

        function filterData(searchTerm) {
            const filteredWarga = warga.filter(person => person.nama.toLowerCase().includes(searchTerm.toLowerCase()));
            const filteredSatpam = satpam.filter(person => person.nama.toLowerCase().includes(searchTerm.toLowerCase()));
            createSlides(filteredWarga, 'carousel-warga-content', false);
            createSlides(filteredSatpam, 'carousel-satpam-content', true);
            document.getElementById('wargaCount').textContent = `Warga: ${filteredWarga.length} Terdaftar`;
            document.getElementById('satpamCount').textContent = `Satpam: ${filteredSatpam.length} Terdaftar`;
        }

        createSlides(warga, 'carousel-warga-content', false);
        createSlides(satpam, 'carousel-satpam-content', true);
        document.getElementById('wargaCount').textContent = `Warga: ${warga.length} Terdaftar`;
        document.getElementById('satpamCount').textContent = `Satpam: ${satpam.length} Terdaftar`;
        document.getElementById('totalWarga').textContent = warga.length;
        document.getElementById('totalSatpam').textContent = satpam.length;

        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('input', () => {
            filterData(searchInput.value);
        });

        const profileIcon = document.getElementById('profileIcon');
        const sidebarProfile = document.getElementById('sidebarProfile');
        const closeSidebarProfile = document.getElementById('closeSidebarProfile');

        profileIcon.addEventListener('click', () => {
            const isOpen = sidebarProfile.classList.toggle('open');
            sidebarProfile.setAttribute('aria-hidden', !isOpen);
        });

        closeSidebarProfile.addEventListener('click', () => {
            sidebarProfile.classList.remove('open');
            sidebarProfile.setAttribute('aria-hidden', 'true');
        });

        [profileIcon].forEach(icon => {
            icon.addEventListener('keydown', e => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    icon.click();
                }
            });
        });

        function updateDateTime() {
            const now = new Date();
            const wibOffset = 7 * 60;
            const localOffset = now.getTimezoneOffset();
            const wibTime = new Date(now.getTime() + (wibOffset + localOffset) * 60 * 1000);

            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

            const dayName = days[wibTime.getDay()];
            const date = wibTime.getDate();
            const month = months[wibTime.getMonth()];
            const year = wibTime.getFullYear();
            let hours = wibTime.getHours();
            const minutes = wibTime.getMinutes().toString().padStart(2, '0');
            const seconds = wibTime.getSeconds().toString().padStart(2, '0');
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12 || 12;
            hours = hours.toString().padStart(2, '0');

            const timeString = `${hours}:${minutes}:${seconds} ${ampm} WIB, ${dayName}, ${date} ${month} ${year}`;
            document.getElementById('currentDateTime').textContent = timeString;
        }

        setInterval(updateDateTime, 1000);
        updateDateTime();
    </script>
</body>
</html>