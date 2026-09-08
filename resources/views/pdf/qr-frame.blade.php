<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>QR Code Printable Frame</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            text-align: center;
            background: #ffffff;
            color: #0f172a;
            padding: 40px;
        }
        .card {
            border: 4px solid #0f172a;
            border-radius: 24px;
            padding: 40px;
            display: inline-block;
            margin: 0 auto;
            max-width: 400px;
        }
        .qr-img {
            width: 250px;
            height: 250px;
            margin: 20px 0;
        }
        .name {
            font-size: 24px;
            font-weight: bold;
            margin-top: 10px;
        }
        .title {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 20px;
        }
        .label {
            background: #0284c7;
            color: #ffffff;
            font-weight: bold;
            font-size: 12px;
            padding: 8px 20px;
            border-radius: 20px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="card">
        <span class="label">{{ $label }}</span>
        <br/>
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents($qrImagePath)) }}" class="qr-img"/>
        <div class="name">{{ $profile->name }}</div>
        <div class="title">{{ $profile->designation }} {{ ($profile->designation && $profile->company) ? '•' : '' }} {{ $profile->company }}</div>
    </div>
</body>
</html>
