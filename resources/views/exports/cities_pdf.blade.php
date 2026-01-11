<!DOCTYPE html>
<html>
<head>
    <style>
        @page { margin: 100px 25px; }
        header { position: fixed; top: -60px; left: 0px; right: 0px; height: 50px; text-align: center; }
        footer { position: fixed; bottom: -60px; left: 0px; right: 0px; height: 50px; text-align: center; }
    </style>
</head>
<body>
    <header>
        <img src="{{ $logo }}" height="40px">
        <span>Laravel Zip Client - Hivatalos Lista</span>
    </header>

    <footer>
        Oldal: <script type="text/php">echo $PAGE_NUM;</script>
    </footer>

    <main>
        <h1>Városok Listája</h1>
        <table>
            <!-- Táblázat tartalom -->
            @foreach($cities as $city)
                <tr><td>{{ $city['name'] }}</td></tr>
            @endforeach
        </table>
    </main>
</body>
</html>
