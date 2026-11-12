<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SyncPapdTransportLocations extends Command
{
    protected $signature = 'papd:sync-transport-locations';

    protected $description = 'Import global airports and Indonesian transport locations from public datasets';

    public function handle()
    {
        $airportCount = $this->syncAirports();
        $this->info("Imported or updated {$airportCount} global airports from OurAirports.");

        $osmCount = 0;
        foreach ([
            'train' => 'nwr(area.id)["railway"="station"];nwr(area.id)["railway"="halt"];',
            'bus' => 'nwr(area.id)["amenity"="bus_station"];nwr(area.id)["public_transport"="station"]["bus"="yes"];nwr(area.id)["public_transport"="station"]["station"="bus"];',
            'port' => 'nwr(area.id)["landuse"="port"];nwr(area.id)["seamark:type"="harbour"];nwr(area.id)["amenity"="ferry_terminal"];nwr(area.id)["ferry"="terminal"];',
        ] as $type => $selector) {
            $count = $this->syncOsmLocations($type, $selector);
            $osmCount += $count;
            $this->info("Imported or updated {$count} Indonesian {$type} locations from OpenStreetMap.");
        }

        $legacyCodes = DB::table('transport_locations')
            ->where(function ($query) {
                $query->where('code', 'like', 'OSM-%')
                    ->orWhere(function ($query) {
                        $query->where('type', 'airport')->where('code', 'like', 'OA%');
                    })
                    ->orWhere(function ($query) {
                        $query->where('type', 'train')->where('code', 'like', 'TR%');
                    })
                    ->orWhere(function ($query) {
                        $query->where('type', 'bus')->where('code', 'like', 'BS%');
                    })
                    ->orWhere(function ($query) {
                        $query->where('type', 'port')->where('code', 'like', 'PT%');
                    });
            })
            ->update(['is_active' => false, 'updated_at' => now()]);
        if ($legacyCodes > 0) {
            $this->info("Deactivated {$legacyCodes} legacy long OpenStreetMap location codes.");
        }

        $this->info("Transport location sync complete. Total records processed: " . ($airportCount + $osmCount) . '.');
        $this->line('OpenStreetMap data is available under the Open Database License (ODbL).');

        return Command::SUCCESS;
    }

    private function syncAirports()
    {
        $path = tempnam(sys_get_temp_dir(), 'papd-airports-');
        if ($path === false) {
            throw new \RuntimeException('Unable to create a temporary file for the airport dataset.');
        }

        try {
            $response = Http::timeout(300)
                ->withOptions(['sink' => $path])
                ->withHeaders(['User-Agent' => 'IT-Helpdesk-PAPD-Location-Sync'])
                ->get('https://davidmegginson.github.io/ourairports-data/airports.csv');

            if (!$response->successful()) {
                throw new \RuntimeException('OurAirports download failed with HTTP ' . $response->status() . '.');
            }

            $file = fopen($path, 'r');
            if ($file === false) {
                throw new \RuntimeException('Unable to read the downloaded OurAirports dataset.');
            }

            try {
                $headers = fgetcsv($file);
                if (!$headers || !in_array('id', $headers, true) || !in_array('type', $headers, true)) {
                    throw new \RuntimeException('The OurAirports CSV has an unexpected format.');
                }

                $existingLocations = DB::table('transport_locations')
                    ->where('type', 'airport')
                    ->where('is_active', true)
                    ->get(['code', 'name', 'city']);
                $usedCodes = [];
                $codesByNameAndCity = [];
                foreach ($existingLocations as $location) {
                    if ($this->isLegacyLocationCode('airport', $location->code)) {
                        continue;
                    }

                    $usedCodes[$location->code] = true;
                    $codesByNameAndCity[$this->locationKey($location->name, $location->city)] = $location->code;
                }

                $batch = [];
                $count = 0;
                while (($values = fgetcsv($file)) !== false) {
                    if (count($values) !== count($headers)) {
                        continue;
                    }

                    $airport = array_combine($headers, $values);
                    if (!in_array($airport['type'], [
                        'small_airport',
                        'medium_airport',
                        'large_airport',
                        'heliport',
                        'seaplane_base',
                    ], true)) {
                        continue;
                    }

                    $name = trim($airport['name'] ?? '');
                    if ($name === '') {
                        continue;
                    }

                    $iata = strtoupper(trim($airport['iata_code'] ?? ''));
                    $city = trim($airport['municipality'] ?? '') ?: $name;
                    if (preg_match('/^[A-Z0-9]{2,10}$/', $iata)) {
                        $code = $iata;
                    } else {
                        $locationKey = $this->locationKey($name, $city);
                        if (isset($codesByNameAndCity[$locationKey])) {
                            $code = $codesByNameAndCity[$locationKey];
                        } else {
                            $code = $this->uniqueNameCode('airport', $name, $usedCodes);
                            $usedCodes[$code] = true;
                            $codesByNameAndCity[$locationKey] = $code;
                        }
                    }

                    $batch[] = [
                        'code' => substr($code, 0, 32),
                        'name' => mb_substr($name, 0, 150),
                        'city' => mb_substr($city, 0, 100),
                        'type' => 'airport',
                        'country' => trim($airport['iso_country'] ?? '') ?: null,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    if (count($batch) === 500) {
                        $this->upsertLocations($batch);
                        $count += count($batch);
                        $batch = [];
                    }
                }

                if ($batch) {
                    $this->upsertLocations($batch);
                    $count += count($batch);
                }

                return $count;
            } finally {
                fclose($file);
            }
        } finally {
            if (is_file($path)) {
                unlink($path);
            }
        }
    }

    private function syncOsmLocations($type, $selector)
    {
        $query = '[out:json][timeout:240];'
            . 'area["ISO3166-1"="ID"]["admin_level"="2"]->.id;('
            . $selector
            . ');out center tags;';

        $response = Http::timeout(300)
            ->asForm()
            ->withHeaders(['User-Agent' => 'IT-Helpdesk-PAPD-Location-Sync'])
            ->post('https://overpass-api.de/api/interpreter', ['data' => $query]);

        if (!$response->successful()) {
            throw new \RuntimeException("OpenStreetMap Overpass request for {$type} locations failed with HTTP " . $response->status() . '.');
        }

        $payload = $response->json();
        if (!is_array($payload) || !isset($payload['elements']) || !is_array($payload['elements'])) {
            throw new \RuntimeException("OpenStreetMap returned an invalid response for {$type} locations.");
        }

        $existingLocations = DB::table('transport_locations')
            ->where('type', $type)
            ->where('is_active', true)
            ->get(['code', 'name', 'city']);
        $usedCodes = [];
        $codesByNameAndCity = [];
        foreach ($existingLocations as $location) {
            if ($this->isLegacyLocationCode($type, $location->code)) {
                continue;
            }

            $usedCodes[$location->code] = true;
            $codesByNameAndCity[$this->locationKey($location->name, $location->city)] = $location->code;
        }

        $batch = [];
        foreach ($payload['elements'] as $element) {
            $tags = $element['tags'] ?? [];
            $name = trim($tags['name'] ?? '');
            if ($name === '' || !isset($element['type'], $element['id'])) {
                continue;
            }

            $city = trim(
                $tags['addr:city']
                ?? $tags['addr:town']
                ?? $tags['addr:village']
                ?? $tags['is_in:city']
                ?? $tags['addr:district']
                ?? $tags['addr:province']
                ?? $tags['addr:state']
                ?? ''
            );
            if ($city === '') {
                $city = $name;
            }

            $locationKey = $this->locationKey($name, $city);
            if (isset($codesByNameAndCity[$locationKey])) {
                $code = $codesByNameAndCity[$locationKey];
            } else {
                $code = $this->uniqueNameCode($type, $name, $usedCodes);
                $usedCodes[$code] = true;
                $codesByNameAndCity[$locationKey] = $code;
            }

            $batch[] = [
                'code' => $code,
                'name' => mb_substr($name, 0, 150),
                'city' => mb_substr($city, 0, 100),
                'type' => $type,
                'country' => 'Indonesia',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        foreach (array_chunk($batch, 500) as $chunk) {
            $this->upsertLocations($chunk);
        }

        return count($batch);
    }

    private function isLegacyLocationCode($type, $code)
    {
        if (stripos($code, 'OSM-') === 0) {
            return true;
        }

        $prefixes = [
            'airport' => 'OA',
            'train' => 'TR',
            'bus' => 'BS',
            'port' => 'PT',
        ];

        return isset($prefixes[$type]) && strpos($code, $prefixes[$type]) === 0;
    }

    private function uniqueNameCode($type, $name, array $usedCodes)
    {
        $prefixes = [
            'airport' => 'BD',
            'train' => 'ST',
            'bus' => 'TRM',
            'port' => 'PL',
        ];

        if (!isset($prefixes[$type])) {
            throw new \RuntimeException("Unable to generate a location code for {$type}.");
        }

        $words = preg_split('/[^A-Z0-9]+/', strtoupper(\Illuminate\Support\Str::ascii($name)), -1, PREG_SPLIT_NO_EMPTY);
        $genericWords = ['AIRPORT', 'BANDARA', 'BUS', 'HALTE', 'HARBOUR', 'HARBOR', 'INTERNATIONAL', 'PELABUHAN', 'PORT', 'STASIUN', 'STATION', 'TERMINAL'];
        $words = array_values(array_filter($words, function ($word) use ($genericWords) {
            return !in_array($word, $genericWords, true);
        }));
        $initials = implode('', array_map(function ($word) {
            return substr($word, 0, 1);
        }, array_slice($words, 0, 5)));
        $baseCode = $prefixes[$type] . '-' . ($initials ?: 'LOC');
        $baseCode = substr($baseCode, 0, 10);
        $code = $baseCode;
        $suffix = 1;

        while (isset($usedCodes[$code])) {
            $ending = '-' . $suffix++;
            $code = substr($baseCode, 0, 10 - strlen($ending)) . $ending;
        }

        return $code;
    }

    private function locationKey($name, $city)
    {
        return strtolower(trim($name)) . '|' . strtolower(trim($city));
    }

    private function upsertLocations(array $locations)
    {
        $uniqueLocations = [];
        foreach ($locations as $location) {
            $uniqueLocations[$location['code']] = $location;
        }

        DB::table('transport_locations')->upsert(
            array_values($uniqueLocations),
            ['code'],
            ['name', 'city', 'type', 'country', 'is_active', 'updated_at']
        );
    }
}
