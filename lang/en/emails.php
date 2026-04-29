<?php

return [
    'budget' => [
        'sent' => [
            'subject' => 'New Budget Available - DentalFlow',
            'greeting' => 'Hello :name',
            'intro' => 'Your budget **#:id** is ready for review.',
            'detail_title' => 'Budget Details',
            'total' => 'Total',
            'clinic' => 'Clinic',
            'valid_until' => 'Valid until',
            'no_expiry' => 'No expiration date',
            'button' => 'View Budget',
            'outro' => 'If you have any questions, please do not hesitate to contact us.',
        ],
    ],
    'appointments' => [
        'reminder' => [
            'subject' => 'Appointment Reminder - DentalFlow',
            'greeting' => 'Hello :name',
            'intro' => 'This is a reminder that you have an appointment scheduled at our clinic.',
            'detail_title' => 'Appointment Details',
            'date' => 'Date',
            'time' => 'Time',
            'type' => 'Type',
            'notes' => 'Notes',
            'no_notes' => 'No additional notes',
            'button' => 'View My Appointments',
            'outro' => 'If you need to reschedule or cancel, please contact us at least 24 hours in advance.',
        ],
    ],
    'password_reset' => [
        'subject' => 'Reset Password - DentalFlow',
        'intro' => 'You are receiving this email because we received a password reset request for your **DentalFlow** account.',
        'button' => 'Reset Password',
        'expiry' => 'This link will expire in :count minutes.',
        'ignore' => 'If you did not request this change, you can safely ignore this email.',
    ],
    'common' => [
        'greetings' => 'Regards',
        'team' => 'The DentalFlow Team',
    ],
];
