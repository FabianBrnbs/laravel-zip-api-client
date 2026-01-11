<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // Fontos az API híváshoz
use Barryvdh\DomPDF\Facade\Pdf;      // Fontos a PDF generáláshoz
use Illuminate\Support\Facades\Response;

class CityController extends Controller
{
    /**
     * Fő oldal megjelenítése (Szűrés és Listázás)
     */
    public function index(Request $request)
    {
        // 1. Mindig lekérjük a megyéket a legördülőhöz
        // (Ha az API nem elérhető, üres tömböt ad vissza, hogy ne omoljon össze)
        try {
            $counties = Http::get('http://127.0.0.1:8000/api/counties')->json();
        } catch (\Exception $e) {
            $counties = [];
        }

        $letters = [];
        $cities = [];
        $selectedCountyId = $request->query('county_id');
        $selectedLetter = $request->query('letter');

   
        if ($selectedCountyId) {
            $letters = Http::get("http://127.0.0.1:8000/api/counties/{$selectedCountyId}/letters")->json();
        }

    
        if ($selectedCountyId && $selectedLetter) {
            $cities = $this->getFilteredCities($request);
        }

        return view('cities.index', [
            'counties' => $counties,
            'letters' => $letters,
            'cities' => $cities,
            'selectedCountyId' => $selectedCountyId,
            'selectedLetter' => $selectedLetter
        ]);
    }

    /**
     * CSV Export
     */
    public function exportCsv(Request $request)
    {
        $cities = $this->getFilteredCities($request);
        $filename = "varosok-export-" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($cities) {
            $file = fopen('php://output', 'w');
            
            // CSV Fejléc (BOM a helyes karakterkódoláshoz Excelben)
            fputs($file, "\xEF\xBB\xBF"); 
            fputcsv($file, ['Település', 'Irányítószám', 'Megye'], ';');

            // Adatok
            foreach ($cities as $city) {
                fputcsv($file, [
                    $city['settlement']['name'],
                    $city['code'], 
                    $city['settlement']['county']['name']
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * PDF Export
     */
    public function exportPdf(Request $request)
    {
        $cities = $this->getFilteredCities($request);
        
        // Betöltjük a nézetet és átadjuk az adatokat
        $pdf = Pdf::loadView('cities.pdf', [
            'cities' => $cities,
            'county_id' => $request->query('county_id'),
            'letter' => $request->query('letter')
        ]);

        return $pdf->download('varosok-export.pdf');
    }

    /**
     * Segédfüggvény: A szűrt városok lekérése az API-tól
     * (Hogy ne kelljen duplikálni a kódot az index, csv és pdf metódusokban)
     */
    private function getFilteredCities(Request $request)
    {
        $selectedCountyId = $request->query('county_id');
        $selectedLetter = $request->query('letter');

        if ($selectedCountyId && $selectedLetter) {
            try {
                $response = Http::get('http://127.0.0.1:8000/api/zipcodes/search', [
                    'county_id' => $selectedCountyId,
                    'letter' => $selectedLetter
                ]);
                return $response->json();
            } catch (\Exception $e) {
                return [];
            }
        }

        return [];
    }
}
