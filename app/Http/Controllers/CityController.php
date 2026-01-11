<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ZipApiService;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CitiesExport;

class CityController extends Controller
{
    protected $api;

    public function __construct(ZipApiService $api)
    {
        $this->api = $api;
    }

    public function index(Request $request)
    {
        $counties = $this->api->getCounties(); 
        
        $selectedCountyId = $request->query('county_id');
        $selectedLetter = $request->query('letter');
        
        $letters = [];
        $cities = [];

        if ($selectedCountyId) {
            $allCitiesInCounty = collect($this->api->getCitiesByCounty($selectedCountyId));
            
            $letters = $allCitiesInCounty->map(function($city) {
                return strtoupper(substr($city['name'], 0, 1));
            })->unique()->sort()->values();

            if ($selectedLetter) {
                $cities = $allCitiesInCounty->filter(function($city) use ($selectedLetter) {
                    return str_starts_with(strtoupper($city['name']), $selectedLetter);
                });
            }
        }

        return view('cities.index', compact('counties', 'letters', 'cities', 'selectedCountyId', 'selectedLetter'));
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
