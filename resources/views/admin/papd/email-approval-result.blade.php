<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Persetujuan Corporate PAPD</title>
    <style>
        body { background: #f5f6fa; color: #333; font-family: Arial, sans-serif; margin: 0; padding: 32px 16px; }
        .card { background: #fff; border-radius: 8px; margin: 0 auto; max-width: 560px; padding: 28px; box-shadow: 0 2px 12px rgba(0, 0, 0, .08); }
        .status { border-radius: 4px; padding: 14px; }
        .success { background: #e8f5e9; color: #256029; }
        .error { background: #ffebee; color: #a32121; }
        dt { font-weight: bold; margin-top: 16px; }
        dd { margin: 4px 0 0; }
    </style>
</head>
<body>
    <main class="card">
        <h1>Persetujuan Corporate PAPD</h1>
        <p class="status {{ $success ? 'success' : 'error' }}">{{ $message }}</p>
        <dl>
            <dt>ID PAPD</dt>
            <dd>{{ $papdRequest->ticket_number }}</dd>
            <dt>Pemohon</dt>
            <dd>{{ $papdRequest->nama_lengkap }}</dd>
        </dl>
    </main>
</body>
</html>
