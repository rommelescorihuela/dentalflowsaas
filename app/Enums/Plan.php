<?php

declare(strict_types=1);

namespace App\Enums;

enum Plan: string
{
    case FreeTrial = 'free_trial';
    case Basic = 'basic';
    case Pro = 'pro';
    case Enterprise = 'enterprise';

    public function label(): string
    {
        return config("plans.{$this->value}.label", $this->value);
    }

    public function priceUsd(): ?float
    {
        return config("plans.{$this->value}.price_usd");
    }

    public function seatsLimit(): ?int
    {
        return config("plans.{$this->value}.seats");
    }

    public function patientsLimit(): ?int
    {
        return config("plans.{$this->value}.patients");
    }

    public function trialDays(): int
    {
        return config("plans.{$this->value}.trial_days", 14);
    }

    public function features(): array
    {
        return config("plans.{$this->value}.features", []);
    }

    public function hasFeature(string $feature): bool
    {
        $features = $this->features();

        return in_array('*', $features, true) || in_array($feature, $features, true);
    }
}
