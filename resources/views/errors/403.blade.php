<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>403 Akses Ditolak</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0;
               min-height: 100vh; display: flex; background: #f3f4f6;
               align-items: center; justify-content: center; }
        .box { background: #fff; padding: 2rem 2.5rem; max-width: 28rem;
               border-radius: 8px; box-shadow: 0 1px 4px rgba(0,0,0,.15); }
        h1 { margin: 0 0.5rem 1rem 0; color: #1e3a8a; }
        a { color: #1d4ed8; }
    </style>
</head>
<body>
    <div class="box">
        <h1>403 Akses Ditolak</h1>
        <p>Anda tidak memiliki hak akses ke halaman atau data ini.</p>
        <p><a href="{{ auth()->check() ? route('dashboard') : url('/') }}">Kembali</a></p>
    </div>
</body>
</html>
