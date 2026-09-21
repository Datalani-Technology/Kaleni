<?php

return [
    'phone' => env('CONTACT_PHONE', '+264813382817'),
    'email_info' => env('CONTACT_EMAIL_INFO', 'kalenilucas061@gmail.com'),
    'email_enquiries' => env('CONTACT_EMAIL_ENQUIRIES', ''), // also receives contact form; if empty, only info used
    'email_orders' => env('CONTACT_EMAIL_ORDERS', 'kalenilucas061@gmail.com'),
    'address' => [
        'name' => 'Kaleni Catering Services',
        'city' => 'Windhoek',
        'country' => 'Namibia',
        'po_box' => env('CONTACT_PO_BOX', ''),
    ],
];
