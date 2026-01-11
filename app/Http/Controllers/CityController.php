<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ZipApiService;
 // DomPDF Facade
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CitiesExport; // Ezt majd létrehozzuk

class CityController extends Controller
{
    protected $api;

    public function __construct(ZipApiService $api)
    {
        $this->api = $api;
    }

    public function index(Request $request)
    {
        $counties = $this->api->getCounties(); // Feltételezve, hogy az API visszaadja a megyéket
        
        $selectedCountyId = $request->query('county_id');
        $selectedLetter = $request->query('letter');
        
        $letters = [];
        $cities = [];

        if ($selectedCountyId) {
            // Itt kérjük le az összes várost a megyéből, hogy kinyerjük a betűket
            // VAGY ha az API támogatja, akkor csak a betűket.
            // Egyszerűsítés: Lekérjük a városokat és Laravel Collectionnel szűrünk.
            $allCitiesInCounty = collect($this->api->getCitiesByCounty($selectedCountyId));
            
            // Egyedi kezdőbetűk kinyerése
            $letters = $allCitiesInCounty->map(function($city) {
                return strtoupper(substr($city['name'], 0, 1));
            })->unique()->sort()->values();

            if ($selectedLetter) {
                // Ha van betű is választva, szűrjük a listát
                $cities = $allCitiesInCounty->filter(function($city) use ($selectedLetter) {
                    return str_starts_with(strtoupper($city['name']), $selectedLetter);
                });
            }
        }

        return view('cities.index', compact('counties', 'letters', 'cities', 'selectedCountyId', 'selectedLetter'));
    }

    public function exportPdf(Request $request)
    {
        // --- EZT A RÉSZT KELL BEMÁSOLNI (vagy ehhez hasonlót) ---
        $selectedCountyId = $request->query('county_id');
        $selectedLetter = $request->query('letter');
        $cities = [];

        if ($selectedCountyId) {
            $allCitiesInCounty = collect($this->api->getCitiesByCounty($selectedCountyId));
            if ($selectedLetter) {
                $cities = $allCitiesInCounty->filter(function($city) use ($selectedLetter) {
                    return str_starts_with(strtoupper($city['name']), $selectedLetter);
                });
            } else {
                $cities = $allCitiesInCounty; // Ha nincs betű szűrés, akkor mind
            }
        }
        // ---------------------------------------------------------

        $data = [
            'title' => 'Város Lista',
            'cities' => $cities,
            'logo' => public_path('img/logo.png')
        ];

        $pdf = PDF::loadView('exports.cities_pdf', $data);
        return $pdf->download('varosok.pdf');
    }
}
