<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Card</title>

    <style>
        body {
            font-family: Arial;
            text-align: center;
            background: #f4f4f4;
        }

        .card {
            width: 350px;
            margin: auto;
            padding: 20px;
            border-radius: 12px;
            background: white;
            border: 2px dashed #333;
        }

        .title {
            font-size: 20px;
            margin-bottom: 10px;
        }

        .code {
            font-size: 18px;
            font-weight: bold;
            margin: 10px 0;
        }

        .amount {
            font-size: 16px;
            color: green;
        }

        .qr {
            margin-top: 15px;
        }
    </style>
</head>

<body>

<div class="card">

    <div class="title">🎫 بطاقة شحن</div>

    <div class="code">
        {{ $card->code }}
    </div>

    <div class="amount">
        {{ $card->amount }} LYD
    </div>

    <img class="qr" src="data:image/png;base64,{{ $qr }}" width="180">

    <p style="margin-top:10px;">
        امسح QR أو استخدم الكود
    </p>

</div>

</body>
</html>
