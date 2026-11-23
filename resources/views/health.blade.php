<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Health Check - {{ $aplikasi }}</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #080e1b;
            color: #f1f5f9;
            font-family: system-ui, -apple-system, sans-serif;
            padding: 20px;
        }
        .card {
            width: 100%;
            max-width: 520px;
            background: #101a2b;
            border: 1px solid rgba(148, 163, 184, 0.18);
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.15);
        }
        .title {
            font-size: 18px;
            font-weight: 700;
            margin: 0;
        }
        .badge {
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }
        .badge-ok {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(52, 211, 153, 0.35);
        }
        .badge-err {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 1px solid rgba(248, 113, 113, 0.35);
        }
        .row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid rgba(148, 163, 184, 0.08);
            font-size: 14px;
        }
        .label { color: #94a3b8; }
        .value { font-weight: 600; text-align: right; }
        .msg {
            margin-top: 16px;
            padding: 12px;
            border-radius: 10px;
            background: #0b1322;
            font-size: 13px;
            color: #cbd5e1;
        }
        .actions {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }
        .btn {
            flex: 1;
            text-align: center;
            padding: 10px 14px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
        }
        .btn-primary {
            background: #2563eb;
            color: #ffffff;
        }
        .btn-secondary {
            background: #1e293b;
            color: #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h1 class="title">Status Pemantau Sistem</h1>
            <span class="badge {{ $sehat ? 'badge-ok' : 'badge-err' }}">
                {{ $sehat ? '● HEALTHY (200)' : '● UNHEALTHY (503)' }}
            </span>
        </div>

        <div class="row">
            <span class="label">Aplikasi</span>
            <span class="value">{{ $aplikasi }}</span>
        </div>
        <div class="row">
            <span class="label">Environment</span>
            <span class="value">{{ strtoupper($environment) }}</span>
        </div>
        <div class="row">
            <span class="label">Koneksi Database</span>
            <span class="value">{{ $database['status'] }} ({{ strtoupper($database['driver']) }})</span>
        </div>
        <div class="row">
            <span class="label">Latensi Query</span>
            <span class="value">{{ $database['latensi_ms'] }} ms</span>
        </div>
        <div class="row">
            <span class="label">Waktu Pemeriksaan</span>
            <span class="value">{{ $waktu }}</span>
        </div>

        <div class="msg">{{ $database['pesan'] }}</div>

        <div class="actions">
            <a href="{{ url('/health') }}" class="btn btn-primary">Muat Ulang Cek</a>
            <a href="{{ url('/') }}" class="btn btn-secondary">Buka Peta Publik</a>
        </div>
    </div>
</body>
</html>