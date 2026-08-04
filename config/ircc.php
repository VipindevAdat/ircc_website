<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Student participant unit pricing (IRCC registration)
    |--------------------------------------------------------------------------
    |
    | Price is snapshotted on the registration row at account creation.
    | Before regular_pricing_starts_at (app timezone), early price applies.
    |
    */

    'student_price_early' => 150.00,

    'student_price_regular' => 200.00,

    'student_regular_pricing_starts_at' => '2026-04-01 00:00:00',

];
