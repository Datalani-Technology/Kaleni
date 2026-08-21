<?php

return [
    'phone' => env('CONTACT_PHONE', '+264815574680'),
    'email_info' => env('CONTACT_EMAIL_INFO', 'info@namsa.com.na'),
    'email_enquiries' => env('CONTACT_EMAIL_ENQUIRIES', ''), // also receives contact form; if empty, only info used
    'email_orders' => env('CONTACT_EMAIL_ORDERS', 'order@namsa.com.na'),
    'address' => [
        'name' => '/Namsa Florals',
        'city' => 'Windhoek',
        'country' => 'Namibia',
        'po_box' => env('CONTACT_PO_BOX', ''),
    ],
];
