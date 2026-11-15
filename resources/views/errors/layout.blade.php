<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('code') — @yield('title') | Infoscreening</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #f8fafc;
            color: #1e293b;
            display: flex;
            flex-direction: column;
        }
        .topbar {
            background: #3A07BB;
            color: #ffffff;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .brand {
            font-weight: 800;
            font-size: 18px;
            letter-spacing: -0.02em;
            color: #ffffff;
            text-decoration: none;
        }
        .main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
        }
        .card {
            width: 100%;
            max-width: 520px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 40px 32px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        }
        .badge {
            display: inline-block;
            background: #f0ebfc;
            color: #3A07BB;
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
            font-weight: 700;
            font-size: 13px;
            padding: 6px 14px;
            border-radius: 9999px;
            margin-bottom: 16px;
        }
        h1 {
            margin: 0 0 12px;
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.3;
        }
        p {
            margin: 0 0 28px;
            font-size: 14px;
            line-height: 1.6;
            color: #475569;
        }
        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: center;
        }
        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #3A07BB;
            color: #ffffff;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            transition: background 0.15s ease;
        }
        .btn-primary:hover { background: #230373; }
        .btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            color: #334155;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            cursor: pointer;
            transition: background 0.15s ease;
        }
        .btn-secondary:hover { background: #f1f5f9; }
        .footer {
            text-align: center;
            padding: 18px 16px;
            font-size: 12px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <header class="topbar">
        <a href="{{ url('/') }}" class="brand">Infoscreening</a>
    </header>
    <main class="main">
        <section class="card" role="main">
            <div class="badge">HTTP @yield('code')</div>
            <h1>@yield('title')</h1>
            <p>@yield('message')</p>
            <div class="actions">
                <a href="{{ url('/') }}" class="btn-primary">Kembali ke Beranda</a>
                @yield('extra_action')
            </div>
        </section>
    </main>
    <footer class="footer">
        &copy; {{ date('Y') }} Infoscreening.id
    </footer>
</body>
</html>
