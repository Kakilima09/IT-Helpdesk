<?php

return [

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Gateway (Fonnte)
    |--------------------------------------------------------------------------
    |
    | Token didapat dari dashboard Fonnte. Numeric id (device) hanya
    | diperlukan bila akun menggunakan target id_tujuan (opsional).
    |
    */

    'fonnte_token' => env('FONNTE_TOKEN', ''),
    'fonnte_base_url' => env('FONNTE_BASE_URL', 'https://api.fonnte.com'),

    // Kode negara default untuk nomor yang diawali "0" (misal 08xx -> 62xx)
    'country_code' => env('WHATSAPP_COUNTRY_CODE', '62'),

];