<?php

return [
    'whatsapp_admin' => env('DIGIPANGAN_WHATSAPP_ADMIN', '6281234567890'),
    'contact_email'  => env('DIGIPANGAN_CONTACT_EMAIL', 'admin@digipangan-merauke.id'),
    'media_disk'     => env('DIGIPANGAN_MEDIA_DISK', 'public'),
    'pagination'     => (int) env('DIGIPANGAN_PAGE_SIZE', 15),
];
