<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ZipApiService;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CitiesExport;
use Illuminate\Support\Facades\Http;

class CityController extends Controller
{
    protected $api;

    public function __construct(ZipApiService $api)
    {
        $this->api = $api;
    }

        public function index(Request $request)
    {
        // 1. Mindig lekérjük a megyéket a legördülőhöz
        $counties = Http::get('http://127.0.0.1:8000/api/counties')->json();

        $letters = [];
        $cities = [];
        $selectedCountyId = $request->query('county_id');
        $selectedLetter = $request->query('letter');

        // 2. Ha van kiválasztva megye, lekérjük a betűket
        if ($selectedCountyId) {
            $letters = Http::get("http://127.0.0.1:8000/api/counties/{$selectedCountyId}/letters")->json();
        }

        // 3. Ha van megye ÉS betű is, lekérjük a városokat
        if ($selectedCountyId && $selectedLetter) {
            // A search végpontunkat használjuk paraméterekkel
            $cities = Http::get('http://127.0.0.1:8000/api/zipcodes/search', [
                'county_id' => $selectedCountyId,
                'letter' => $selectedLetter
            ])->json();
        }

        return view('cities.index', [
            'counties' => $counties,
            'letters' => $letters,
            'cities' => $cities,
            'selectedCountyId' => $selectedCountyId,
            'selectedLetter' => $selectedLetter
        ]);
    }

    public function exportPdf(Request $request) 
    {
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
                 $cities = $allCitiesInCounty;
             }
         }

         $data = [
             'title' => 'Város Lista',
             'cities' => $cities,
             // Fontos: tegyen egy logo.png-t a public/img mappába!
             // Ha nincs, kommentelje ki a következő sort:
             'logo' => public_path('img/logo.png') 
         ];
         
         $pdf = PDF::loadView('exports.cities_pdf', $data);
         return $pdf->download('varosok.pdf');

    }

    public function exportCsv(Request $request) 
    {
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
                $cities = $allCitiesInCounty;
            }
        }
        
        return Excel::download(new CitiesExport($cities), 'varosok.csv');
    }
}
