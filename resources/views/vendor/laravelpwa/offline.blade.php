<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Baglanti Yok</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            font-family: Arial, sans-serif;
            background:
                radial-gradient(circle at top, rgba(245, 158, 11, 0.18), transparent 28%),
                linear-gradient(180deg, #f8fafc 0%, #e2e8f0 100%);
            color: #0f172a;
        }

        .offline-card {
            width: min(100%, 560px);
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 24px;
            padding: 32px 28px;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.12);
            text-align: center;
        }

        .offline-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 64px;
            height: 64px;
            border-radius: 20px;
            background: #0f172a;
            color: #fff;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        h1 {
            margin: 0 0 12px;
            font-size: 28px;
        }

        p {
            margin: 0 0 22px;
            line-height: 1.6;
            color: #475569;
        }

        a {
            display: inline-block;
            padding: 12px 18px;
            border-radius: 12px;
            background: #0f172a;
            color: #fff;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <section class="offline-card">
        <div class="offline-badge">BY</div>
        <h1>Internet baglantisi yok</h1>
        <p>Bilgi Yildizi uygulamasi su anda cihaza baglanamiyor. Baglanti geri geldiginde sayfayi yenileyebilir veya ana sayfaya donebilirsiniz.</p>
        <a href="{{ url('/') }}">Ana sayfaya don</a>
    </section>
</body>
</html>
