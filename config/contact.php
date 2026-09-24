<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Contacto público
    |--------------------------------------------------------------------------
    | Canal de contacto para la landing y el plan Enterprise. Si defines
    | CONTACT_WHATSAPP (solo dígitos con código de país, ej. 584121234567)
    | se usa WhatsApp; si no, se cae a CONTACT_EMAIL.
    */
    'email' => env('CONTACT_EMAIL') ?: 'hola@dentalflow.app',
    'whatsapp' => env('CONTACT_WHATSAPP') ?: '',

    /*
    | Cupos disponibles de la beta (opcional). Si se define (número), la
    | landing muestra "Quedan N cupos"; si no, muestra "Cupos limitados".
    */
    'waitlist' => env('WAITLIST_COUNT') ?: null,
];
