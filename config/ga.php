<?php

return [

    /*
    |--------------------------------------------------------------------------
    | GA Request Configuration
    |--------------------------------------------------------------------------
    */

    // Ambang batas harga barang untuk approval layer 2 (berdasarkan harga
    // tertinggi item goods). Bila > threshold maka perlu persetujuan Manager GA.
    'threshold' => (float) env('GA_THRESHOLD', 500000),

    // Jam sebelum permintaan yang masih pending_l1/l2 dianggap expired.
    'expire_hours' => (int) env('GA_EXPIRE_HOURS', 24),

    // Role spatie untuk approver default customer (L1) dan Level 2 (Manager).
    'l1_role' => env('GA_L1_ROLE', 'ga_staff'),
    'l2_role' => env('GA_L2_ROLE', 'ga_manager'),

];