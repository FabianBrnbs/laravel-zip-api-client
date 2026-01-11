<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ZipApiService
{
    protected $baseUrl;

    public function __construct()
    {
        // Feltételezzük, hogy az API a 8000-es porton fut
        $this->baseUrl = 'http://127.0.0.1:8000/api';
    }

    // Segédfüggvény a token csatolásához (ha van)
    protected function getHeaders()
    {
        $headers = ['Accept' => 'application/json'];
        if (Session::has('api_token')) {
            $headers['Authorization'] = 'Bearer ' . Session::get('api_token');
        }
        return $headers;
    }
    
    public function getCounties()
    {
        return Http::withHeaders($this->getHeaders())->get("{$this->baseUrl}/counties")->json();
    }
    
    // Ez a feladatban kért "Kezdőbetűk lekérése adott megyében"
    // Megjegyzés: Ehhez az API-n is kell majd egy végpont, vagy kliens oldalon szűrünk.
    // Mivel "Lekérjük az API-tól" a feladat szövege, feltételezzük, hogy van ilyen végpont,
    // vagy a sima városlistát szűrjük.
    public function getCitiesByCounty($countyId)
    {
        return Http::withHeaders($this->getHeaders())->get("{$this->baseUrl}/settlements", [
            'county_id' => $countyId
        ])->json();
    }
    
    // Városok adott megyében és kezdőbetűvel
    public function getCitiesByLetter($countyId, $letter)
    {
         // Ezt paraméterként küldjük az API-nak
        return Http::withHeaders($this->getHeaders())->get("{$this->baseUrl}/zipcodes/search", [
            'county_id' => $countyId,
            'letter' => $letter
        ])->json();
    }
    
    // CRUD Műveletek (pl. Város létrehozása)
    public function createCity($data)
    {
        return Http::withHeaders($this->getHeaders())->post("{$this->baseUrl}/zipcodes", $data);
    }
    
    // ... Hasonlóan a delete és update metódusok
}
