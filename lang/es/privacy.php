<?php

return [
    'title' => 'Política de Privacidad',
    'last_updated' => 'Última actualización',
    'sections' => [
        'introduction' => [
            'title' => '1. Introducción',
            'content' => 'DentalFlow SaaS ("nosotros", "nuestro") se compromete a proteger la privacidad de sus datos y los de sus pacientes. Esta política describe cómo recopilamos, usamos y protegemos la información personal.',
        ],
        'data_collected' => [
            'title' => '2. Información que Recopilamos',
            'items' => [
                '<strong>Datos de cuenta:</strong> Nombre, email, teléfono, rol en la clínica.',
                '<strong>Datos de pacientes:</strong> Historial médico, odontogramas, citas, presupuestos.',
                '<strong>Datos técnicos:</strong> Dirección IP, logs de acceso, tipo de navegador.',
            ],
        ],
        'data_usage' => [
            'title' => '3. Uso de la Información',
            'intro' => 'Utilizamos la información para:',
            'items' => [
                'Proveer y mejorar el Servicio.',
                'Procesar citas, presupuestos y registros clínicos.',
                'Enviar notificaciones y recordatorios.',
                'Cumplir con obligaciones legales.',
            ],
        ],
        'data_sharing' => [
            'title' => '4. Compartición de Datos',
            'intro' => 'No vendemos ni compartimos sus datos con terceros, excepto:',
            'items' => [
                'Con proveedores de servicios necesarios para operar el Servicio (hosting, emails).',
                'Cuando sea requerido por ley o autoridad competente.',
                'Para proteger los derechos, propiedad o seguridad de DentalFlow.',
            ],
        ],
        'security' => [
            'title' => '5. Seguridad',
            'intro' => 'Implementamos medidas de seguridad técnicas y organizativas para proteger sus datos contra acceso no autorizado, alteración, divulgación o destrucción, incluyendo:',
            'items' => [
                'Encriptación de contraseñas (bcrypt).',
                'Aislamiento estricto de datos entre clínicas (multi-tenancy).',
                'Registro de auditoría de accesos y cambios.',
                'Backups automáticos y seguros.',
            ],
        ],
        'retention' => [
            'title' => '6. Retención de Datos',
            'content' => 'Conservamos sus datos mientras su cuenta esté activa o según sea necesario para cumplir con obligaciones legales. Puede solicitar la eliminación de sus datos contactándonos.',
        ],
        'user_rights' => [
            'title' => '7. Derechos del Usuario',
            'intro' => 'Dependiendo de su ubicación, puede tener derecho a:',
            'items' => [
                'Acceder, rectificar o eliminar sus datos personales.',
                'Restringir u oponerse al procesamiento de sus datos.',
                'Solicitar una copia portable de sus datos.',
            ],
        ],
        'compliance' => [
            'title' => '8. Cumplimiento Legal',
            'content' => 'DentalFlow cumple con el Reglamento General de Protección de Datos (RGPD) y otras regulaciones aplicables. Si usted es un responsable de datos (clínica), DentalFlow actúa como procesador de datos.',
        ],
        'cookies' => [
            'title' => '9. Cookies',
            'content' => 'Utilizamos cookies esenciales para el funcionamiento del Servicio. No utilizamos cookies de rastreo ni publicitarias.',
        ],
        'contact' => [
            'title' => '10. Contacto',
            'intro' => 'Para ejercer sus derechos o realizar consultas sobre privacidad, contáctenos en:',
        ],
    ],
    'back_to_home' => '← Volver al inicio',
];
