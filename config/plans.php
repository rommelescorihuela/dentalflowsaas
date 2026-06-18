<?php

return [
    'free_trial' => [
        'label' => 'Trial',
        'price_usd' => 0,
        'trial_days' => 14,
        'seats' => 3,
        'patients' => 25,
        'features' => ['odontogram', 'budgets', 'appointments', 'inventory'],
    ],
    'basic' => [
        'label' => 'Básico',
        'price_usd' => null,
        'seats' => 5,
        'patients' => 500,
        'features' => ['odontogram', 'budgets', 'appointments', 'inventory', 'payments'],
    ],
    'pro' => [
        'label' => 'Pro',
        'price_usd' => null,
        'seats' => 15,
        'patients' => null,
        'features' => ['*'],
    ],
    'enterprise' => [
        'label' => 'Enterprise',
        'price_usd' => null,
        'seats' => null,
        'patients' => null,
        'features' => ['*'],
    ],
];
