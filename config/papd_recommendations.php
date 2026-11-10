<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default origin
    |--------------------------------------------------------------------------
    | Dipakai sebagai fallback bila kota asal tidak bisa ditentukan dari rute.
    */
    'default_origin' => env('PAPD_DEFAULT_ORIGIN', 'Jakarta'),

    /*
    |--------------------------------------------------------------------------
    | Fallback peta kota -> kode (untuk data lama tanpa kode_asal/kode_tujuan)
    |--------------------------------------------------------------------------
    */
    'city_codes' => [
        'jakarta'   => 'CGK',
        'bandung'   => 'BDO',
        'surabaya'  => 'SUB',
        'denpasar'  => 'DPS',
        'bali'      => 'DPS',
        'medan'     => 'KNO',
        'makassar'  => 'UPG',
        'yogyakarta'=> 'YIA',
        'jogja'     => 'YIA',
        'solo'      => 'SOC',
        'semarang'  => 'SRG',
        'balikpapan'=> 'BPN',
        'palembang' => 'PLM',
        'pekanbaru' => 'PKU',
        'batam'     => 'BTH',
        'padang'    => 'PDG',
        'lombok'    => 'LOP',
        'manado'    => 'MDC',
        'ambon'     => 'AMQ',
        'singapore' => 'SIN',
        'kuala lumpur' => 'KUL',
        'bangkok'   => 'BKK',
        'hong kong' => 'HKG',
        'tokyo'     => 'NRT',
        'seoul'     => 'ICN',
        'jeddah'    => 'JED',
        'madinah'   => 'MED',
        'dubai'     => 'DXB',
        'amsterdam' => 'AMS',
        'london'    => 'LHR',
    ],

    /*
    |--------------------------------------------------------------------------
    | Provider rekomendasi
    |--------------------------------------------------------------------------
    | Placeholder yang tersedia:
    | {origin} {destination} {origin_lower} {destination_lower}
    | {date} {date_dmy} {date_ymd} {date_yymmdd}
    | {return_date} {return_dmy} {return_ymd} {return_yymmdd}
    | {adults} {cabin} {city} {rooms}
    |
    | Catatan: format URL tiap provider dapat berubah, cukup sesuaikan di sini.
    */
    'providers' => [

        'traveloka' => [
            'label'        => 'Traveloka',
            'type'         => 'flight',
            'icon'         => 'fe fe-send',
            'enabled'      => true,
            'url_template' => 'https://www.traveloka.com/en-id/flight/fullsearch?ap={origin}.{destination}&dt={date_dmy}&ps={adults}&sc={cabin}',
        ],

        'tiket' => [
            'label'        => 'tiket.com',
            'type'         => 'flight',
            'icon'         => 'fe fe-send',
            'enabled'      => true,
            'url_template' => 'https://www.tiket.com/pesawat/search?d={origin}&a={destination}&date={date_ymd}&adult={adults}&child=0&infant=0',
        ],

        'skyscanner' => [
            'label'        => 'Skyscanner',
            'type'         => 'flight',
            'icon'         => 'fe fe-send',
            'enabled'      => true,
            'url_template' => 'https://www.skyscanner.co.id/transport/flights/{origin_lower}/{destination_lower}/{date_yymmdd}/?adults={adults}',
        ],

        'agoda' => [
            'label'        => 'Agoda (Hotel)',
            'type'         => 'hotel',
            'icon'         => 'fe fe-home',
            'enabled'      => true,
            'url_template' => 'https://www.agoda.com/search?textToSearch={city}&checkIn={checkin_ymd}&checkOut={checkout_ymd}&adults={adults}&rooms={rooms}',
        ],

    ],

];
