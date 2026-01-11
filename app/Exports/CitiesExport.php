<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class CitiesExport implements FromCollection, WithHeadings
{
    protected $cities;

    public function __construct($cities)
    {
        // Ha tömböt kapunk, konvertáljuk kollekcióvá
        $this->cities = $cities instanceof Collection ? $cities : collect($cities);
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Itt adjuk vissza az adatokat. 
        // Ha az API válaszában tömbök vannak, akkor mappelhetjük őket, 
        // hogy csak a szükséges mezők kerüljenek a CSV-be.
        return $this->cities->map(function($city) {
            return [
                'name' => $city['name'],
                'zip_code' => $city['zip_code'] ?? '', // Ha van ilyen mező
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Város neve',
            'Irányítószám',
        ];
    }
}
