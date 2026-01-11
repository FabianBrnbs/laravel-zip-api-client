<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Városok Export</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        @page { margin: 100px 25px; }
        header { position: fixed; top: -60px; left: 0px; right: 0px; height: 50px; border-bottom: 1px solid #ddd; }
        footer { position: fixed; bottom: -60px; left: 0px; right: 0px; height: 50px; border-top: 1px solid #ddd; text-align: center; line-height: 35px; }
        .logo { float: left; width: 150px; font-weight: bold; color: #4f46e5; }
        .title { float: right; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f3f4f6; }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <!-- Ide teheti a logót: <img src="{{ public_path('logo.png') }}" width="100"> -->
            MAGYAR VÁROSOK
        </div>
        <div class="title">
            Exportálás dátuma: {{ date('Y.m.d H:i') }}
        </div>
    </header>

    <footer>
        Generálva a ZipApp alkalmazás által - Oldal: <span class="pagenum"></span>
    </footer>

    <main>
        <h2>Találati lista</h2>
        <table>
            <thead>
                <tr>
                    <th>Település</th>
                    <th>Irányítószám</th>
                    <th>Megye</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cities as $city)
                <tr>
                    <td>{{ $city['settlement']['name'] }}</td>
                    <td>{{ $city['code'] }}</td>
                    <td>{{ $city['settlement']['county']['name'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </main>
</body>
</html>
