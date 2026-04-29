<?php

return [
    'title' => 'Privacy Policy',
    'last_updated' => 'Last updated',
    'sections' => [
        'introduction' => [
            'title' => '1. Introduction',
            'content' => 'DentalFlow SaaS ("we", "our") is committed to protecting the privacy of your data and that of your patients. This policy describes how we collect, use, and protect personal information.',
        ],
        'data_collected' => [
            'title' => '2. Information We Collect',
            'items' => [
                '<strong>Account data:</strong> Name, email, phone, role in the clinic.',
                '<strong>Patient data:</strong> Medical history, odontograms, appointments, budgets.',
                '<strong>Technical data:</strong> IP address, access logs, browser type.',
            ],
        ],
        'data_usage' => [
            'title' => '3. Use of Information',
            'intro' => 'We use the information to:',
            'items' => [
                'Provide and improve the Service.',
                'Process appointments, budgets, and clinical records.',
                'Send notifications and reminders.',
                'Comply with legal obligations.',
            ],
        ],
        'data_sharing' => [
            'title' => '4. Data Sharing',
            'intro' => 'We do not sell or share your data with third parties, except:',
            'items' => [
                'With service providers necessary to operate the Service (hosting, emails).',
                'When required by law or competent authority.',
                'To protect the rights, property, or security of DentalFlow.',
            ],
        ],
        'security' => [
            'title' => '5. Security',
            'intro' => 'We implement technical and organizational security measures to protect your data against unauthorized access, alteration, disclosure, or destruction, including:',
            'items' => [
                'Password encryption (bcrypt).',
                'Strict data isolation between clinics (multi-tenancy).',
                'Access and change audit logs.',
                'Automatic and secure backups.',
            ],
        ],
        'retention' => [
            'title' => '6. Data Retention',
            'content' => 'We retain your data while your account is active or as necessary to comply with legal obligations. You may request deletion of your data by contacting us.',
        ],
        'user_rights' => [
            'title' => '7. User Rights',
            'intro' => 'Depending on your location, you may have the right to:',
            'items' => [
                'Access, rectify, or delete your personal data.',
                'Restrict or object to the processing of your data.',
                'Request a portable copy of your data.',
            ],
        ],
        'compliance' => [
            'title' => '8. Legal Compliance',
            'content' => 'DentalFlow complies with the General Data Protection Regulation (GDPR) and other applicable regulations. If you are a data controller (clinic), DentalFlow acts as a data processor.',
        ],
        'cookies' => [
            'title' => '9. Cookies',
            'content' => 'We use essential cookies for the operation of the Service. We do not use tracking or advertising cookies.',
        ],
        'contact' => [
            'title' => '10. Contact',
            'intro' => 'To exercise your rights or make privacy inquiries, contact us at:',
        ],
    ],
    'back_to_home' => '← Back to Home',
];
