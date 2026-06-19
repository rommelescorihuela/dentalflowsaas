<?php

return [
    'free_trial' => [
        'label' => 'Trial Pro',
        'price_usd' => 0,
        'trial_days' => 14,
        'seats' => null,
        'patients' => null,
        'features' => ['*'],
    ],
    'basic' => [
        'label' => 'Starter',
        'price_usd' => 39,
        'seats' => 5,
        'patients' => 500,
        'features' => ['odontogram', 'budgets', 'appointments', 'inventory', 'payments'],
    ],
    'pro' => [
        'label' => 'Pro',
        'price_usd' => 89,
        'seats' => 15,
        'patients' => null,
        'features' => ['odontogram', 'budgets', 'appointments', 'inventory', 'payments', 'portal', 'pdf', 'bi_reports', 'low_inventory_alert'],
    ],
    'enterprise' => [
        'label' => 'Enterprise',
        'price_usd' => null,
        'seats' => null,
        'patients' => null,
        'features' => ['*'],
    ],
];
