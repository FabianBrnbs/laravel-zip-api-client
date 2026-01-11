@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Városok Keresése</h2>

    <!-- Megye Választó -->
    <form action="{{ route('cities.index') }}" method="GET">
        <select name="county_id" onchange="this.form.submit()" class="form-control mb-3">
            <option value="">Válasszon megyét...</option>
            @foreach($counties as $county)
                <option value="{{ $county['id'] }}" {{ $selectedCountyId == $county['id'] ? 'selected' : '' }}>
                    {{ $county['name'] }}
                </option>
            @endforeach
        </select>
    </form>

    <!-- Kezdőbetűk -->
    @if(count($letters) > 0)
        <div class="mb-3">
            @foreach($letters as $letter)
                <a href="{{ route('cities.index', ['county_id' => $selectedCountyId, 'letter' => $letter]) }}" 
                   class="btn btn-outline-primary {{ $selectedLetter == $letter ? 'active' : '' }}">
                    {{ $letter }}
                </a>
            @endforeach
        </div>
    @endif

    <!-- Eredmény Lista és Export Gombok -->
    @if(count($cities) > 0)
        <div class="mb-3">
            <a href="{{ route('cities.export.csv', request()->all()) }}" class="btn btn-success">Export CSV</a>
            <a href="{{ route('cities.export.pdf', request()->all()) }}" class="btn btn-danger">Export PDF</a>
        </div>

        <table class="table table-striped">
            <thead><tr><th>Város</th><th>Irányítószám</th></tr></thead>
            <tbody>
                @foreach($cities as $city)
                    <tr>
                        <td>{{ $city['name'] }}</td>
                        <td>{{ $city['zip_code'] ?? 'N/A' }}</td> <!-- Attól függ API mit ad vissza -->
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
