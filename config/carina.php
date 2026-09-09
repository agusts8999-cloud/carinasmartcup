<?php

return [
    'whatsapp_support' => env('WHATSAPP_SUPPORT', '6281234567890'),
    'payment_reservation_hours' => (int) env('PAYMENT_RESERVATION_HOURS', 24),
    'bank' => [
        'name' => env('BANK_NAME', 'BCA'),
        'account_number' => env('BANK_ACCOUNT_NUMBER', '1234567890'),
        'account_name' => env('BANK_ACCOUNT_NAME', 'CarinaSmartCup'),
    ],
];
