<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KERTAN</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-image: url("{{ asset('gambar/bg login.gif') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .login-container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            width: 320px;
            text-align: center;
            backdrop-filter: blur(2px);
        }
        .logo {
            margin-bottom: 25px;
        }
        .logo img {
            max-width: 180px;
            height: auto;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        input {
            width: 100%;
            padding: 12px;
            box-sizing: border-box;
            border: 2px solid #3498db;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s;
        }
        input:focus {
            outline: none;
            border-color: #2980b9;
            box-shadow: 0 0 8px rgba(52, 152, 219, 0.6);
        }
        button {
            width: 100%;
            padding: 14px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
            transition: background-color 0.3s;
            margin-top: 10px;
        }
        button:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }
        .register-link {
            margin-top: 25px;
            font-size: 14px;
            color: #555;
        }
        .register-link a {
            color: #3498db;
            text-decoration: none;
            font-weight: 600;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
        .error-message {
            color: #e74c3c;
            margin-bottom: 20px;
            padding: 12px;
            background-color: rgba(231, 76, 60, 0.1);
            border-radius: 6px;
            border-left: 4px solid #e74c3c;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo"> 
            <img src="{{ asset('gambar/logo_kertan.png') }}" alt="Logo KERTAN">
        </div>
        
        @if ($errors->has('akses'))
            <div class="error-message">
                {{ $errors->first('akses') }}
            </div>
        @endif

        <form action="/login" method="POST">
            @csrf
            <div class="form-group">
                <label for="email">Alamat Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Kata Sandi:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Masuk</button>
        </form>

      
    </div>
</body>
</html>